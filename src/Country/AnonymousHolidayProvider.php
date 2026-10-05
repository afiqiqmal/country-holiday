<?php

namespace Holiday\Country;

use Holiday\Providers\AbstractCountryHolidayProvider;

class AnonymousHolidayProvider extends AbstractCountryHolidayProvider
{
    protected ?array $regionSlugs = null;

    /**
     * Get the country slug for Malaysia.
     *
     * @return string|null Country slug
     */
    protected function getCountrySlug(): ?string
    {
        return $this->country;
    }

    public function getSupportedRegions(): array
    {
        return array_keys($this->getRegionSlugs());
    }

    /**
     * Get region slugs as listed on the country page, cached per instance.
     *
     * @return array<string, string> Region name => URL slug
     */
    protected function getRegionSlugs(): array
    {
        return $this->regionSlugs ??= $this->getScraper()
            ->scrapeStates($this->baseUrl.'/'.$this->getCountrySlug());
    }

    protected function buildUrl(?string $region, int $year): string
    {
        foreach ($region ? $this->getRegionSlugs() : [] as $name => $slug) {
            if (strtolower($name) === strtolower($region)) {
                return $this->baseUrl.'/'.strtolower($this->getCountrySlug()).'/'.$slug.'/'.$year;
            }
        }

        return parent::buildUrl($region, $year);
    }
}