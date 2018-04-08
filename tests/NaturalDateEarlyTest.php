<?php

namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateEarlyTest extends TestCase {


    public function testEarlyModifierJustMonth() {
        $string           = 'early jan';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();
        $thisYear         = date( 'Y' ); // Keeps the Unit Tests up to date.

        //print_r( $naturalDate->getDebugMessages() );

        $this->assertEquals( Carbon::parse( $thisYear . '-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( $thisYear . '-01-10 06:59:59', 'UTC' ), $endDate );
    }


    public function testEarlyChristmas() {
        $string           = 'early xmas 1979';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1979-12-25 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1979-12-25 14:59:59', 'UTC' ), $endDate );
    }


    public function testEarlyModifierWithJustTheWordEarlyShouldReturnEarlyToday() {
        $string           = 'early';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();
        //$startDate        = $naturalDate->getLocalStart();
        //$endDate          = $naturalDate->getLocalEnd();


        $this->assertEquals( Carbon::parse( Carbon::parse( date( 'Y-m-d 06:00:00' ) ), 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( Carbon::parse( date( 'Y-m-d 12:59:59' ) ), 'UTC' ), $endDate );
    }

    /**
     * @throws \Exception
     * @group early
     */
    public function testEarlyModifierYear() {
        $string           = 'early 2016';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];

        $naturalDate = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $startDate = $naturalDate->getLocalStart();
        $endDate   = $naturalDate->getLocalEnd();

        $this->assertEquals( Carbon::parse( '2016-01-01 00:00:00', $timezoneId ), $startDate );
        $this->assertEquals( Carbon::parse( '2016-04-30 23:59:59', $timezoneId ), $endDate );
    }

    public function testEarlyModifierWithMonthAndYear() {
        $string           = 'early jan 1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $startDate = $naturalDate->getUtcStart();
        $endDate   = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1978-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1978-01-10 06:59:59', 'UTC' ), $endDate );
    }

}