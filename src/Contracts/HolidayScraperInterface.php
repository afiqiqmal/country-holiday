<?php

namespace Holiday\Contracts;

interface HolidayScraperInterface
{
    /**
     * Scrape holidays from a specific URL.
     *
     * @param string $url The URL to scrape holidays from
     * @param int $year The year of holidays
     * @return array Scraped holiday data
     */
    public function scrape(string $url, int $year): array;
}