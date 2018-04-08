<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateLateTest extends TestCase {


    public function testLateModifierJustMonth() {
        $string           = 'late jan';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        //print_r($naturalDate);

        $startDate = $naturalDate->getUtcStart();
        $endDate   = $naturalDate->getUtcEnd();
        $thisYear  = date( 'Y' ); // Keeps the Unit Tests up to date.

        //print_r( $naturalDate->getDebugMessages() );

        $this->assertEquals( Carbon::parse( $thisYear . '-01-21 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( $thisYear . '-02-01 06:59:59', 'UTC' ), $endDate );
    }

    public function testLateModifierWithMonthAndYear() {
        $string           = 'late jan 1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $startDate = $naturalDate->getUtcStart();
        $endDate   = $naturalDate->getUtcEnd();

        $localStart = $startDate->setTimezone( $timezoneId );
        $localEnd   = $endDate->setTimezone( $timezoneId );

        $this->assertEquals( Carbon::parse( '1978-01-21 00:00:00', $timezoneId ), $localStart );
        $this->assertEquals( Carbon::parse( '1978-01-31 23:59:59', $timezoneId ), $localEnd );
    }


    public function testLateChristmas() {
        $string           = 'late xmas 1979';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1979-12-26 00:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1979-12-26 06:59:59', 'UTC' ), $endDate );
    }


    public function testLateModifierWithJustTheWordLateShouldReturnLateToday() {
        $string           = 'late';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();


        $this->assertEquals( Carbon::parse( Carbon::parse( date( 'Y-m-d 02:00:00' ) )->addDay(), 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( Carbon::parse( date( 'Y-m-d 05:59:59' ) )->addDay(), 'UTC' ), $endDate );
    }

    public function testLateModifierYear() {
        $string           = 'late 2016';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '2016-09-01 06:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '2017-01-01 06:59:59', 'UTC' ), $endDate );
    }


}