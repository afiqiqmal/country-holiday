<?php

namespace Holiday\Country;

use Holiday\Providers\AbstractCountryHolidayProvider;

class AnonymousHolidayProvider extends AbstractCountryHolidayProvider
{
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
        return $this->getScraper()
            ->scrapeStates($this->baseUrl.'/'.$this->getCountrySlug());
    }
}