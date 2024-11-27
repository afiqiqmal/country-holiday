<?php

namespace Holiday;

use Holiday\Contracts\HolidayProviderInterface;
use Holiday\Exception\CountryNotSupportedException;
use Holiday\Providers\HolidayProviderFactory;
use Symfony\Component\BrowserKit\HttpBrowser;

class Holiday
{
    private string $country;
    private string|array|null $selectedRegions = null;
    private int|null $year = null;
    private int|null $month = null;
    private bool $groupByMonth = false;

    private array $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];

    /**
     * Create a new HolidayManager.
     *
     * @param string $country Country to retrieve holidays for
     * @param HttpBrowser|null $client HTTP client for web requests
     */
    public function __construct(string $country, ?HttpBrowser $client = null)
    {
        $this->country = $country;
    }

    /**
     * Static method to create a HolidayManager instance.
     *
     * @param string $country Country to retrieve holidays for
     * @return static New HolidayManager instance
     */
    public static function for(string $country): self
    {
        return new self($country);
    }

    /**
     * Select all regions/states for the country.
     *
     * @param int|null $year Year to retrieve holidays for
     * @return $this
     */
    public function fromAllStates(?int $year = null): self
    {
        $this->selectedRegions = null;
        $this->year = $year;
        return $this;
    }

    /**
     * Select specific region(s) for holiday retrieval.
     *
     * @param array|string $regions Region(s) to retrieve holidays for
     * @param int|null $year Year to retrieve holidays for
     * @return $this
     */
    public function fromState(array|string $regions, ?int $year = null): self
    {
        $this->selectedRegions = $regions;
        $this->year = $year;
        return $this;
    }

    /**
     * Specify the year for holiday retrieval.
     *
     * @param int $year Year to retrieve holidays for
     * @return $this
     */
    public function ofYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Group holidays by month.
     *
     * @return $this
     */
    public function groupByMonth(): self
    {
        $this->groupByMonth = true;
        return $this;
    }

    /**
     * Filter holidays by specific month.
     *
     * @param int $month Month to filter (1-12)
     * @return $this
     */
    public function filterByMonth(int $month): self
    {
        $this->month = $month;
        return $this;
    }

    /**
     * Retrieve holidays based on configured filters.
     *
     * @return array Filtered holiday results
     * @throws CountryNotSupportedException
     */
    public function get(): array
    {
        // Get holiday provider for the country
        $provider = HolidayProviderFactory::create($this->country);

        // Determine regions to retrieve
        $regions = $this->determineRegions($provider);

        // Prepare results
        $result = $this->queryHolidays($provider, $regions);

        // Apply additional filtering
        return $this->processResults($result);
    }

    /**
     * Determine regions to retrieve holidays for.
     *
     * @param HolidayProviderInterface $provider Holiday provider
     * @return array|null Regions to retrieve holidays for
     */
    private function determineRegions(HolidayProviderInterface $provider): ?array
    {
        // If no regions specified, use all supported regions
        if ($this->selectedRegions === null) {
            return $provider->getSupportedRegions();
        }

        // Ensure regions is always an array
        return is_array($this->selectedRegions)
            ? $this->selectedRegions
            : [$this->selectedRegions];
    }

    /**
     * Query holidays for specified regions and year.
     *
     * @param HolidayProviderInterface $provider Holiday provider
     * @param ?array $regions Regions to retrieve holidays for
     * @return array Raw holiday results
     */
    private function queryHolidays(HolidayProviderInterface $provider, ?array $regions): array
    {
        $year = $this->year ?? (int)date('Y');
        $final = [];
        $error_messages = [];

        if (empty($regions)) {
            $final = array_values(
                array_filter(
                    $provider->getHolidays(null, $year)
                )
            );
        }

        foreach ($regions as $region) {
            try {
                $holidaysData = $provider->getHolidays($region, $year);

                $final[] = [
                    'regional' => $region,
                    'collection' => [
                        [
                            'year' => $year,
                            'data' => array_values(
                                array_filter(
                                    $holidaysData
                                )
                            )
                        ]
                    ]
                ];
            } catch (\Exception $e) {
                $error_messages[] = $e->getMessage();
                $final[] = [
                    'regional' => $region,
                    'collection' => []
                ];
            }
        }

        return [
            'status' => empty($error_messages),
            'data' => $final,
            'error_messages' => $error_messages,
            'developer' => [
                "name" => "Hafiq",
                "email" => "hafiqiqmal93@gmail.com",
                "github" => "https://github.com/afiqiqmal"
            ]
        ];
    }

    /**
     * Process results with additional filtering and grouping.
     *
     * @param array $result Raw holiday results
     * @return array Processed holiday results
     */
    private function processResults(array $result): array
    {
        if (!$result['status']) {
            return $result;
        }

        // Filter by month if specified
        if ($this->month !== null) {
            $result['data'] = array_values($this->filterBySpecificMonth($result['data']));
        }

        // Group by month if requested
        if ($this->groupByMonth) {
            $result['data'] = array_values($this->groupHolidaysByMonth($result['data']));
        }

        return $result;
    }

    /**
     * Filter holidays by specific month.
     *
     * @param array $data Holiday data
     * @return array Filtered holiday data
     */
    private function filterBySpecificMonth(array $data): array
    {
        foreach ($data as &$regionalData) {
            foreach ($regionalData['collection'] as &$yearCollection) {
                $yearCollection['data'] = array_values(
                    array_filter(
                        $yearCollection['data'],
                        function ($holiday) use ($yearCollection) {

                            if (! isset($holiday['date'])) {
                                return false;
                            }

                            $month = date('F', strtotime($holiday['date']));
                            return strtolower($month) ===
                                strtolower($this->months[$this->month]);
                        }
                    )
                );
            }
        }
        return $data;
    }

    /**
     * Group holidays by month.
     *
     * @param array $data Holiday data
     * @return array Grouped holiday data
     */
    private function groupHolidaysByMonth(array $data): array
    {
        foreach ($data as &$regionalData) {
            foreach ($regionalData['collection'] as &$yearCollection) {
                $groupedHolidays = [];

                foreach ($yearCollection['data'] as $holiday) {
                    if (! $holiday) {
                        continue;
                    }

                    $month = date('F', strtotime($holiday['date']));

                    $monthIndex = array_search(
                        $month,
                        array_column($groupedHolidays, 'month')
                    );

                    if ($monthIndex === false) {
                        $groupedHolidays[] = [
                            'month' => $month,
                            'data' => [$holiday]
                        ];
                    } else {
                        $groupedHolidays[$monthIndex]['data'][] = $holiday;
                    }
                }

                $yearCollection['data'] = $groupedHolidays;
            }
        }

        return $data;
    }

    /**
     * Get the current selected regions.
     *
     * @return string|array|null Selected regions
     */
    public function getSelectedRegions(): string|array|null
    {
        return $this->selectedRegions;
    }

    /**
     * Get the current selected year.
     *
     * @return int|null Selected year
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Get the current selected month.
     *
     * @return int|null Selected month
     */
    public function getMonth(): ?int
    {
        return $this->month;
    }
}