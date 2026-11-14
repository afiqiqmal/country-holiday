<?php

namespace Holiday\Tests;

require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ .'/Support/Fixtures.php';

use Holiday\Holiday;
use Holiday\Providers\HolidayProviderFactory;
use Holiday\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * HolidayTest.php
 * to test function in Request class
 */
class HolidayTest extends TestCase
{
    private const YEAR = 2026;

    private function holiday(string $country): Holiday
    {
        return new Holiday($country, new HttpBrowser(Fixtures::client()));
    }

    /**
     * To test getting all region holiday in Malaysia
     */
    public function testGetAllRegionInMalaysiaHoliday()
    {
        $response = $this->holiday('malaysia')->ofYear(self::YEAR)->get();

        $this->assertTrue($response['status']);

        $response = $this->holiday('malaysia')->fromAllStates(self::YEAR)->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInAlbaniaHoliday()
    {
        $response = $this->holiday('albania')->ofYear(self::YEAR)->get();

        $this->assertTrue($response['status']);

        $response = $this->holiday('albania')->fromAllStates(self::YEAR)->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInNigeriaHoliday()
    {
        $response = $this->holiday('nigeria')->ofYear(self::YEAR)->get();

        $this->assertTrue($response['status']);

        $response = $this->holiday('nigeria')->fromAllStates(self::YEAR)->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInJordanHoliday()
    {
        $response = $this->holiday('jordan')->ofYear(self::YEAR)->get();

        $this->assertTrue($response['status']);

        $response = $this->holiday('jordan')->fromAllStates(self::YEAR)->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInIndonesiaHoliday()
    {
        $response = $this->holiday('indonesia')->ofYear(self::YEAR)->get();

        $this->assertTrue($response['status']);

        $response = $this->holiday('indonesia')->fromAllStates(self::YEAR)->get();

        $this->assertTrue($response['status']);
    }

    /**
     * To test getting specific region holiday
     */
    public function testGetSpecificRegionHoliday()
    {
        $holiday = $this->holiday('malaysia');
        $response = $holiday->fromState('Selangor', self::YEAR)->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testGetMultipleRegionsHoliday()
    {
        $holiday = $this->holiday('malaysia');
        $response = $holiday->fromState(['Selangor', 'Malacca'], self::YEAR)->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
        $this->assertTrue($response['data'][1]['regional'] == 'Malacca');
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testErrorMessage()
    {
        $holiday = $this->holiday('malaysia');
        $response = $holiday->fromState(['Selangor', 'Malaccaa'], self::YEAR)->get();

        $this->assertTrue(! $response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');


        $this->assertFalse($response['data'][1]['regional'] == 'Malacca');
        $this->assertTrue($response['data'][1]['collection'] == []);

        $this->assertCount(1, $response['error_messages']);
        $this->assertTrue($response['error_messages'][0] == 'Region \'Malaccaa\' is not supported for this country.');
    }

    public function testSabahIsSupported()
    {
        $response = $this->holiday('malaysia')->fromState('Sabah', self::YEAR)->get();

        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['data'][0]['collection'][0]['data']);
    }

    public function testHolidayTypesAreClassified()
    {
        $holidays = HolidayProviderFactory::create('malaysia', new HttpBrowser(Fixtures::client()))->getHolidays(null, self::YEAR);
        $holidays = array_values(array_filter($holidays));

        $this->assertNotEmpty($holidays);
        $this->assertNotContains('Unknown', array_column($holidays, 'type'));

        $this->assertContains(1, array_column($holidays, 'type_id'));
    }

    public function testRegionUsesSiteSlug()
    {
        $response = $this->holiday('usa')->fromState(['Georgia', 'District of Columbia'], self::YEAR)->get();

        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['data'][0]['collection'][0]['data']);
        $this->assertNotEmpty($response['data'][1]['collection'][0]['data']);
    }

    public function testUnknownCountryReturnsError()
    {
        $response = $this->holiday('united-states')->ofYear(self::YEAR)->get();

        $this->assertFalse($response['status']);
        $this->assertNotEmpty($response['error_messages']);
    }
}
