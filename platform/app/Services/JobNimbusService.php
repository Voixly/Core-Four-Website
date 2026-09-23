<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JobNimbusService
{
    /**
     * Send a residential job to JobNimbus as a contact.
     * True when JobNimbus accepted it, false when the call failed, null when there is nothing to send.
     */
    public function pushContact(Job $job): ?bool
    {
        $key = (string) config('services.jobnimbus.key');
        if ($key === '' || $job->type !== 'residential') {
            return null;
        }

        $job->loadMissing('lead');
        $lead = $job->lead;
        $name = trim((string) ($lead->name ?? ''));
        if ($name === '' && ! $lead?->email && ! $lead?->phone) {
            return null;
        }

        try {
            $directory = $this->directory($key);
            $payload = $this->payload($job, $directory);
            $existing = $this->findExisting($key, (string) ($payload['email'] ?? ''), (string) $payload['external_id']);

            $response = $existing
                ? $this->request($key)->put($this->url('contacts/'.$existing), $payload)
                : $this->request($key)->post($this->url('contacts'), $payload);

            if ($response->failed()) {
                unset($payload['source_name'], $payload['sales_rep'], $payload['sales_rep_name'], $payload['owners'], $payload['location']);
                $response = $existing
                    ? $this->request($key, false)->put($this->url('contacts/'.$existing), $payload)
                    : $this->request($key, false)->post($this->url('contacts'), $payload);
            }

            if ($response->failed()) {
                Log::warning('JobNimbus contact was not saved', [
                    'job' => $job->number,
                    'status' => $response->status(),
                    'body' => mb_substr($response->body(), 0, 500),
                ]);

                return false;
            }

            $jnid = (string) ($response->json('jnid') ?: $existing);
            if ($jnid !== '') {
                $job->forceFill(['jobnimbus_contact_id' => $jnid])->save();
            }
            $lead?->log(null, 'jobnimbus', 'Contact sent to JobNimbus'.($jnid !== '' ? ' ('.$jnid.')' : ''));

            return true;
        } catch (\Throwable $e) {
            Log::warning('JobNimbus contact failed', [
                'job' => $job->number,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $directory
     * @return array<string, mixed>
     */
    private function payload(Job $job, array $directory): array
    {
        $lead = $job->lead;
        $full = trim((string) ($lead->name ?? 'Homeowner'));
        $parts = preg_split('/\s+/', $full, 2) ?: [];
        $first = $parts[0] ?: 'Homeowner';
        $last = $parts[1] ?? '';
        $phone = preg_replace('/\D+/', '', (string) ($lead->phone ?? '')) ?: null;

        $description = trim(implode("\n", array_filter([
            $lead->need ?? null,
            $job->customer_summary,
            $job->address,
            $lead->page_url ? 'Page: '.$lead->page_url : null,
            'Job '.$job->number,
        ])));

        $payload = array_filter([
            'first_name' => $first,
            'last_name' => $last,
            'display_name' => trim($first.' '.$last),
            'email' => $lead->email,
            'home_phone' => $phone,
            'address_line1' => $job->address,
            'city' => $job->city ?: $lead->city,
            'state_text' => 'TX',
            'zip' => $job->zip ?: $lead->zip,
            'country_name' => 'United States',
            'description' => $description !== '' ? $description : null,
            'record_type_name' => config('services.jobnimbus.record_type'),
            'status_name' => config('services.jobnimbus.status'),
            'external_id' => 'corefour-job-'.$job->id,
        ], fn ($value) => $value !== null && $value !== '');

        $source = $this->matchName($directory['sources'] ?? [], (string) ($lead->source ?: 'website'), ['website', 'web', 'google', 'internet']);
        if ($source) {
            $payload['source_name'] = $source;
        }

        $rep = $this->matchRecord($directory['users'] ?? [], (string) config('services.jobnimbus.sales_rep'));
        if ($rep) {
            $payload['sales_rep'] = $rep['id'];
            $payload['sales_rep_name'] = $rep['name'];
            $payload['owners'] = [['id' => $rep['id']]];
        } else {
            $payload['sales_rep_name'] = (string) config('services.jobnimbus.sales_rep');
        }

        $location = $this->matchRecord($directory['locations'] ?? [], (string) config('services.jobnimbus.location'));
        if ($location) {
            $payload['location'] = ['id' => $location['id']];
        }

        return $payload;
    }

    /**
     * @return array{sources: list<string>, users: list<array{id: string, name: string}>, locations: list<array{id: int|string, name: string}>}
     */
    private function directory(string $key): array
    {
        return Cache::remember('jobnimbus.directory', 600, function () use ($key) {
            $response = $this->request($key)->get($this->url('account'));
            $json = $response->successful() ? $response->json() : [];
            if (! is_array($json)) {
                $json = [];
            }

            return [
                'sources' => $this->names($json, ['sources', 'lead_sources']),
                'users' => $this->records($json, ['users', 'team']),
                'locations' => $this->records($json, ['locations']),
            ];
        });
    }

    private function findExisting(string $key, string $email, string $externalId): ?string
    {
        foreach (array_filter([
            $externalId !== '' ? ['term' => ['external_id' => $externalId]] : null,
            $email !== '' ? ['term' => ['email' => $email]] : null,
        ]) as $clause) {
            $response = $this->request($key)->get($this->url('contacts'), [
                'size' => 1,
                'filter' => json_encode(['must' => [$clause]]),
            ]);
            $jnid = $response->json('results.0.jnid') ?: $response->json('contacts.0.jnid');
            if (is_string($jnid) && $jnid !== '') {
                return $jnid;
            }
        }

        return null;
    }

    private function request(string $key, bool $withActor = true): \Illuminate\Http\Client\PendingRequest
    {
        $pending = Http::withToken($key)
            ->acceptJson()
            ->asJson()
            ->timeout(15);

        $actor = (string) config('services.jobnimbus.actor');
        if ($withActor && $actor !== '') {
            $pending = $pending->withQueryParameters(['actor' => $actor]);
        }

        return $pending;
    }

    private function url(string $path): string
    {
        return 'https://app.jobnimbus.com/api1/'.ltrim($path, '/');
    }

    /**
     * @param  array<string, mixed>  $json
     * @param  list<string>  $keys
     * @return list<string>
     */
    private function names(array $json, array $keys): array
    {
        $names = [];
        foreach ($keys as $key) {
            foreach ($this->list($json, $key) as $row) {
                $name = is_array($row) ? ($row['name'] ?? $row['source_name'] ?? null) : $row;
                if (is_string($name) && $name !== '') {
                    $names[] = $name;
                }
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * @param  array<string, mixed>  $json
     * @param  list<string>  $keys
     * @return list<array{id: int|string, name: string}>
     */
    private function records(array $json, array $keys): array
    {
        $records = [];
        foreach ($keys as $key) {
            foreach ($this->list($json, $key) as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $name = $row['name'] ?? trim(($row['first_name'] ?? '').' '.($row['last_name'] ?? ''));
                $id = $row['jnid'] ?? $row['id'] ?? null;
                if (is_string($name) && $name !== '' && (is_string($id) || is_int($id))) {
                    $records[] = ['id' => $id, 'name' => $name];
                }
            }
        }

        return $records;
    }

    /**
     * @param  array<string, mixed>  $json
     * @return list<mixed>
     */
    private function list(array $json, string $key): array
    {
        $value = $json[$key] ?? $json['settings'][$key] ?? [];

        return is_array($value) ? array_values($value) : [];
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $hints
     */
    private function matchName(array $names, string $preferred, array $hints): ?string
    {
        $preferred = strtolower($preferred);
        foreach ($names as $name) {
            if (strtolower($name) === $preferred) {
                return $name;
            }
        }
        foreach ($names as $name) {
            $lower = strtolower($name);
            foreach ($hints as $hint) {
                if (str_contains($lower, $hint)) {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * @param  list<array{id: int|string, name: string}>  $records
     * @return array{id: int|string, name: string}|null
     */
    private function matchRecord(array $records, string $name): ?array
    {
        $wanted = strtolower($name);
        foreach ($records as $record) {
            if (strtolower($record['name']) === $wanted) {
                return $record;
            }
        }
        foreach ($records as $record) {
            if (str_contains(strtolower($record['name']), $wanted)) {
                return $record;
            }
        }

        return null;
    }
}
