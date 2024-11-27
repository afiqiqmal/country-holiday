<?php

namespace Holiday\Providers;

use Holiday\Contracts\HolidayProviderInterface;
use Holiday\Exception\RegionException;
use Holiday\OfficialHolidaysScraper;
use Symfony\Component\BrowserKit\HttpBrowser;

abstract class AbstractCountryHolidayProvider implements HolidayProviderInterface
{
    protected HttpBrowser $client;
    protected array $supportedRegions = [];
    protected array $regionAliases = [];
    protected string $baseUrl = "https://www.officeholidays.com/countries";
    protected ?string $country = null;

    /**
     * Create a new country holiday provider.
     *
     * @param HttpBrowser|null $client HTTP client for web scraping
     */
    public function __construct(?HttpBrowser $client = null)
    {
        $this->client = $client ?? new HttpBrowser();
    }

    public function setCountrySlug(string $slug): void
    {
        $this->country = $slug;
    }

    protected function getScraper(): OfficialHolidaysScraper
    {
        return new OfficialHolidaysScraper($this->client);
    }

    /**
     * Normalize a region name.
     *
     * @param string $region Region to normalize
     * @return string Normalized region name
     * @throws RegionException If region is not supported
     */
    protected function normalizeRegion(string $region): string
    {
        // Check aliases first
        foreach ($this->regionAliases as $alias => $officialName) {
            if (strtolower($alias) === strtolower($region)) {
                $region = $officialName;
                break;
            }
        }

        // Check if region is supported
        $supportedRegions = array_map('strtolower', $this->getSupportedRegions());
        if (!in_array(strtolower($region), $supportedRegions)) {
            throw new RegionException("Region '{$region}' is not supported for this country.");
        }

        return $region;
    }

    /**
     * Get supported regions for the country.
     *
     * @return array List of supported regions
     */
    public function getSupportedRegions(): array
    {
        return $this->supportedRegions;
    }

    /**
     * Get holidays for a specific region and year.
     *
     * @param ?string $region Region to get holidays for
     * @param int|null $year Year to retrieve holidays for
     * @return array List of holidays
     * @throws RegionException If region is invalid
     */
    public function getHolidays(?string $region, ?int $year = null): array
    {
        $year = $year ?? (int)date('Y');

        $normalizedRegion = $region ? $this->normalizeRegion($region) : $region;

        $url = $this->buildUrl($normalizedRegion, $year);

        return $this->getScraper()->scrape($url, $year);
    }

    /**
     * Build the URL for scraping holidays.
     *
     * @param ?string $region Normalized region name
     * @param int $year Year to retrieve holidays for
     * @return string Complete URL for holiday scraping
     */
    protected function buildUrl(?string $region, int $year): string
    {
        if (! $region) {
            return $this->baseUrl . '/' . strtolower($this->getCountrySlug()) . '/' . $year;
        }

        return $this->baseUrl . '/' . strtolower($this->getCountrySlug()) . '/' .
            str_replace(' ', '-', $region) . '/' . $year;
    }

    /**
     * Get the country slug for URL generation.
     *
     * @return string|null Country slug
     */
    abstract protected function getCountrySlug(): ?string;
}