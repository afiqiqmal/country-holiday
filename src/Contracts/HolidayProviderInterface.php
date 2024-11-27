<?php

namespace Holiday\Contracts;

use Holiday\Exception\RegionException;

interface HolidayProviderInterface
{
    /**
     * Retrieve holidays for a specific region and year.
     *
     * @param string $region The specific region or state
     * @param int $year The year to retrieve holidays for
     * @return array List of holidays
     * @throws RegionException If the region is invalid
     */
    public function getHolidays(string $region, int $year): array;

    /**
     * Get the list of supported regions for the country.
     *
     * @return array List of supported regions/states
     */
    public function getSupportedRegions(): array;
}