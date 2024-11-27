<?php

namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use Holiday\Holiday;
use PHPUnit\Framework\TestCase;

/**
 * HolidayTest.php
 * to test function in Request class
 */
class HolidayTest extends TestCase
{
    /**
     * To test getting all region holiday in Malaysia
     */
    public function testGetAllRegionInMalaysiaHoliday()
    {
        $response = Holiday::for('malaysia')->get();

        $this->assertTrue($response['status']);

        $response = Holiday::for('malaysia')->fromAllStates()->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInAlbaniaHoliday()
    {
        $response = Holiday::for('albania')->get();

        $this->assertTrue($response['status']);

        $response = Holiday::for('albania')->fromAllStates()->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInNigeriaHoliday()
    {
        $response = Holiday::for('nigeria')->get();

        $this->assertTrue($response['status']);

        $response = Holiday::for('nigeria')->fromAllStates()->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInJordanHoliday()
    {
        $response = Holiday::for('jordan')->get();

        $this->assertTrue($response['status']);

        $response = Holiday::for('jordan')->fromAllStates()->get();

        $this->assertTrue($response['status']);
    }

    public function testGetAllRegionInIndonesiaHoliday()
    {
        $response = Holiday::for('indonesia')->get();

        $this->assertTrue($response['status']);

        $response = Holiday::for('indonesia')->fromAllStates()->get();

        $this->assertTrue($response['status']);
    }

    /**
     * To test getting specific region holiday
     */
    public function testGetSpecificRegionHoliday()
    {
        $holiday = Holiday::for('malaysia');
        $response = $holiday->fromState('Selangor')->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testGetMultipleRegionsHoliday()
    {
        $holiday = Holiday::for('malaysia');
        $response = $holiday->fromState(['Selangor', 'Malacca'])->get();

        $this->assertTrue($response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');
        $this->assertTrue($response['data'][1]['regional'] == 'Malacca');
    }

    /**
     * To test getting multiple regions holiday
     */
    public function testErrorMessage()
    {
        $holiday = Holiday::for('malaysia');
        $response = $holiday->fromState(['Selangor', 'Malaccaa'])->get();

        $this->assertTrue(! $response['status']);
        $this->assertTrue($response['data'][0]['regional'] == 'Selangor');


        $this->assertFalse($response['data'][1]['regional'] == 'Malacca');
        $this->assertTrue($response['data'][1]['collection'] == []);

        $this->assertCount(1, $response['error_messages']);
        $this->assertTrue($response['error_messages'][0] == 'Region \'Malaccaa\' is not supported for this country.');
    }
}