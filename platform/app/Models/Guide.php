<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guide extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'audience', 'filename', 'downloads', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(GuideDownload::class);
    }

    public function path(): string
    {
        return '/guides/'.$this->slug.'/';
    }

    public function icon(): string
    {
        return match ($this->slug) {
            'houston-homeowner-storm-checklist' => 'fa-cloud-bolt',
            'insurance-claim-walkthrough' => 'fa-file-shield',
            'commercial-roof-condition-scorecard' => 'fa-building',
            default => 'fa-book-open',
        };
    }

    public function ctaLabel(): string
    {
        return match ($this->slug) {
            'houston-homeowner-storm-checklist' => 'Get the storm checklist',
            'insurance-claim-walkthrough' => 'Get the claim walkthrough',
            'commercial-roof-condition-scorecard' => 'Get the roof scorecard',
            'suburb-replacement-timeline' => 'Get the replacement timeline',
            default => 'Get the free guide',
        };
    }

    public static function featuredFor(string $context): Collection
    {
        $slugs = match (true) {
            $context === 'home' => [
                'houston-homeowner-storm-checklist',
                'commercial-roof-condition-scorecard',
            ],
            str_contains($context, 'storm')
                || str_contains($context, 'emergency')
                || str_contains($context, 'roof-repair')
                || str_contains($context, 'roof-inspections') => [
                    'houston-homeowner-storm-checklist',
                ],
            str_contains($context, 'insurance') => [
                'insurance-claim-walkthrough',
            ],
            str_starts_with($context, 'commercial') || $context === 'city-commercial' => [
                'commercial-roof-condition-scorecard',
            ],
            $context === 'city-residential' => [
                'houston-homeowner-storm-checklist',
            ],
            str_starts_with($context, 'residential') || $context === 'financing' => [
                'suburb-replacement-timeline',
            ],
            default => [],
        };

        if ($slugs === []) {
            return new Collection;
        }

        return static::query()
            ->where('is_active', true)
            ->whereIn('slug', $slugs)
            ->get()
            ->sortBy(fn (self $guide) => array_search($guide->slug, $slugs, true))
            ->values();
    }
}
