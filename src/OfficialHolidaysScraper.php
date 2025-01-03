<?php

namespace Holiday;

use Holiday\Contracts\HolidayScraperInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

class OfficialHolidaysScraper implements HolidayScraperInterface
{
    private HttpBrowser $client;

    /**
     * Create a new OfficialHolidaysScraper.
     *
     * @param HttpBrowser $client HTTP client for web scraping
     */
    public function __construct(HttpBrowser $client)
    {
        $this->client = $client;
    }

    /**
     * Scrape holidays from a specific URL.
     *
     * @param string $url The URL to scrape holidays from
     * @param int $year The year of holidays
     * @return array Scraped holiday data
     */
    public function scrape(string $url, int $year): array
    {
        $crawler = $this->client->request('GET', $url);

        return $crawler->filter('.country-table tr')->each(
            function ($node) use ($year) {
                return $this->parseHolidayNode($node, $year);
            }
        );
    }

    public function scrapeStates(string $url): array
    {
        $crawler = $this->client->request('GET', $url);

        return array_filter(
            $crawler->filter('select.region_select option')->each(
                function (Crawler $node) {

                    if ($node->attr('value') === 'all' ||
                        $node->attr('value') === '' ||
                        $node->attr('value') === null) {
                        return null;
                    }

                    return $node->innerText();
                }
            )
        );
    }

    /**
     * Parse a single holiday node from the crawled data.
     *
     * @param Crawler $node Holiday node
     * @param int $year Year of the holidays
     * @return array|null Parsed holiday data
     */
    private function parseHolidayNode(Crawler $node, int $year): ?array
    {
        if ($node->children()->nodeName() !== 'td') {
            return null;
        }

        $temp = $this->extractHolidayDetails($node, $year);

        return $temp ? $this->classifyHolidayType($temp, $node) : null;
    }

    /**
     * Extract basic holiday details from a node.
     *
     * @param Crawler $node Holiday node
     * @param int $year Year of the holidays
     * @return array|null Basic holiday details
     */
    private function extractHolidayDetails(Crawler $node, int $year): ?array
    {
        $temp['day'] = trim($node->children()->eq(0)->text());
        $date_str = strtok(trim($node->children()
                ->eq(1)
                ->extract(['_text', 'class'])[0][0]), "\n")." ".$year;

        if (empty($date_str)) {
            return null;
        }

        $date = date_create_from_format('F d Y', preg_replace("/[\n\r]/", "", $date_str));

        if (!$date) {
            $date = date_create_from_format('Y-m-d', $node->children()->eq(1)->children()->text());

            if (!$date) {
                return null;
            }
        }

        $temp['date'] = date_format($date, 'Y-m-d');
        $temp['date_formatted'] = date_format($date, 'd F Y');
        $temp['month'] = date('F', strtotime($temp['date']));
        $temp['name'] = trim($node->children()->eq(2)->text());
        $temp['description'] = trim($node->children()->eq(3)->text());
        $temp['comments'] = trim($node->children()->eq(4)->text());

        return $temp;
    }

    /**
     * Classify holiday type based on CSS class.
     *
     * @param array $temp Basic holiday details
     * @param Crawler $node Holiday node
     * @return array Holiday details with type information
     */
    private function classifyHolidayType(array $temp, Crawler $node): array
    {
        $temp['is_holiday'] = true;

        $holidayTypeMap = [
            'govt_holiday' => [
                'type' => "Government/Public Sector Holiday",
                'type_id' => 1
            ],
            'nap-past' => [
                'type' => "Not a Public Holiday",
                'type_id' => 2,
                'is_holiday' => false
            ],
            'nap' => [
                'type' => "Not a Public Holiday",
                'type_id' => 2,
                'is_holiday' => false
            ],
            'country-past' => [
                'type' => "National Holiday",
                'type_id' => 3
            ],
            'country' => [
                'type' => "National Holiday",
                'type_id' => 3
            ],
            'region-past' => [
                'type' => "Regional Holiday",
                'type_id' => 4
            ],
            'region' => [
                'type' => "Regional Holiday",
                'type_id' => 4
            ]
        ];

        $nodeClass = trim($node->extract(['class'])[0]);
        $holidayType = $holidayTypeMap[$nodeClass] ?? [
            'type' => "Unknown",
            'type_id' => 5
        ];

        return array_merge($temp, $holidayType);
    }

}