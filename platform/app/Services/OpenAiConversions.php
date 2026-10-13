<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiConversions
{
    public function sendLead(Lead $lead, Request $request): void
    {
        $key = (string) config('services.openai.conversions_key');
        $pixelId = (string) config('services.openai.pixel_id');
        if ($key === '' || $pixelId === '') {
            return;
        }

        $event = [
            'id' => 'lead-'.$lead->id,
            'type' => 'lead_created',
            'timestamp_ms' => (int) round(microtime(true) * 1000),
            'source_url' => $this->sourceUrl($request),
            'action_source' => 'web',
            'user' => $this->apiUser($lead, $request),
            'data' => ['type' => 'customer_action'],
        ];

        $oppref = $this->opaque($request->cookie('__oppref')) ?: $this->refererOppref($request);
        if ($oppref !== null) {
            $event['oppref'] = $oppref;
        }

        try {
            $response = Http::withToken($key)
                ->acceptJson()
                ->asJson()
                ->timeout(5)
                ->post('https://bzr.openai.com/v1/events?pid='.rawurlencode($pixelId), [
                    'validate_only' => false,
                    'events' => [$event],
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAI lead conversion was not accepted.', ['status' => $response->status()]);
            }
        } catch (RequestException|\Throwable) {
            Log::warning('OpenAI lead conversion could not be sent.');
        }
    }

    public function browserUser(Lead $lead): array
    {
        $user = [
            'country' => 'US',
            'region' => 'Texas',
            'external_id_sha256' => hash('sha256', (string) $lead->id),
        ];

        $email = $this->emailHash($lead->email);
        if ($email !== null) {
            $user['email_sha256'] = $email;
        }

        $phone = $this->phoneHash($lead->phone);
        if ($phone !== null) {
            $user['phone_number_sha256'] = $phone;
        }

        [$first, $last] = $this->nameHashes($lead->name);
        if ($first !== null) {
            $user['first_name_sha256'] = $first;
        }
        if ($last !== null) {
            $user['last_name_sha256'] = $last;
        }

        $city = trim((string) $lead->city);
        if ($city !== '') {
            $user['city'] = mb_substr($city, 0, 128);
        }
        $zip = $this->postal($lead->zip);
        if ($zip !== null) {
            $user['postal_code'] = $zip;
        }

        return $user;
    }

    private function apiUser(Lead $lead, Request $request): array
    {
        $browser = $this->browserUser($lead);
        $user = [
            'external_ids_sha256' => [$browser['external_id_sha256']],
            'countries' => ['US'],
            'regions' => ['Texas'],
        ];

        foreach ([
            'email_sha256' => 'emails_sha256',
            'phone_number_sha256' => 'phone_numbers_sha256',
            'first_name_sha256' => 'first_names_sha256',
            'last_name_sha256' => 'last_names_sha256',
        ] as $from => $to) {
            if (isset($browser[$from])) {
                $user[$to] = [$browser[$from]];
            }
        }

        if (isset($browser['city'])) {
            $user['cities'] = [$browser['city']];
        }
        if (isset($browser['postal_code'])) {
            $user['postal_codes'] = [$browser['postal_code']];
        }

        $obref = $this->opaque($request->cookie('__obref'));
        if ($obref !== null) {
            $user['obref'] = $obref;
        }

        $ip = (string) $request->ip();
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $user['ip_address'] = $ip;
        }

        $agent = trim((string) $request->userAgent());
        if ($agent !== '') {
            $user['user_agent'] = mb_substr($agent, 0, 512);
        }

        return $user;
    }

    private function sourceUrl(Request $request): string
    {
        $referer = (string) $request->headers->get('referer');
        if (filter_var($referer, FILTER_VALIDATE_URL)) {
            return $referer;
        }

        return \App\Support\SiteSeo::url('/thank-you/');
    }

    private function refererOppref(Request $request): ?string
    {
        $query = parse_url((string) $request->headers->get('referer'), PHP_URL_QUERY);
        if (! is_string($query) || $query === '') {
            return null;
        }

        parse_str($query, $params);

        return $this->opaque($params['oppref'] ?? null);
    }

    private function opaque(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || strlen($value) > 512 || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            return null;
        }

        return $value;
    }

    private function emailHash(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email === '' ? null : hash('sha256', $email);
    }

    private function phoneHash(?string $phone): ?string
    {
        $phone = ltrim((string) preg_replace('/\D/', '', (string) $phone), '0');
        if (strlen($phone) === 10) {
            $phone = '1'.$phone;
        }

        return strlen($phone) >= 8 && strlen($phone) <= 15 ? hash('sha256', $phone) : null;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function nameHashes(?string $name): array
    {
        $parts = preg_split('/\s+/', trim((string) $name), 2) ?: [];
        $first = strtolower((string) preg_replace('/[[:punct:]\s]+/', '', $parts[0] ?? ''));
        $last = strtolower((string) preg_replace('/[[:punct:]\s]+/', '', $parts[1] ?? ''));

        return [
            $first !== '' ? hash('sha256', $first) : null,
            $last !== '' ? hash('sha256', $last) : null,
        ];
    }

    private function postal(?string $zip): ?string
    {
        $zip = trim((string) preg_replace('/[^A-Za-z0-9 \-]/', '', (string) $zip));

        return $zip === '' ? null : mb_substr($zip, 0, 32);
    }
}
