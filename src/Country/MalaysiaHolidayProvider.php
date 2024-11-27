<?php

namespace Holiday\Country;

use Holiday\Providers\AbstractCountryHolidayProvider;

class MalaysiaHolidayProvider extends AbstractCountryHolidayProvider
{
    protected array $supportedRegions = [
        'Johor',
        'Kedah',
        'Kelantan',
        'Kuala Lumpur',
        'Labuan',
        'Melaka',
        'Negeri Sembilan',
        'Pahang',
        'Penang',
        'Perak',
        'Perlis',
        'Putrajaya',
        'Sarawak',
        'Selangor',
        'Terengganu'
    ];

    protected array $regionAliases = [
        'Johore' => 'Johor',
        'KL' => 'Kuala Lumpur',
        'Malacca' => 'Melaka',
        'Pulau Pinang' => 'Penang'
    ];

    /**
     * Get the country slug for Malaysia.
     *
     * @return string Country slug
     */
    protected function getCountrySlug(): string
    {
        return 'malaysia';
    }
}