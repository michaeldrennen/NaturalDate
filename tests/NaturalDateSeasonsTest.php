<?php

namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateSeasonsTest extends TestCase {

    /**
     * @group seasons
     */
    public function testNoYearGiven() {
        $thisYear         = date( 'Y' );
        $string           = 'fall';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );
        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-11-30 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    /**
     * @group seasons1
     */
    public function testFall() {
        $thisYear         = 2019;
        $string           = 'fall ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, FALSE );
        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-11-30 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
        print( $naturalDate );
    }


    /**
     * @group seasons1
     */
    public function testOldFall() {
        $thisYear         = 1978;
        $string           = 'fall ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, FALSE );
        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-11-30 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
        print( $naturalDate );
    }


    /**
     * @group seasons
     */
    public function testAutumn() {
        $thisYear         = 2019;
        $string           = 'autumn ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );

        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-11-30 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @group seasons
     */
    public function testAutum() {
        $thisYear         = 2019;
        $string           = 'autum ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );

        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-11-30 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @group seasons
     */
    public function testSpring() {
        $thisYear         = 2019;
        $string           = 'spring ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );

        $this->assertEquals( Carbon::parse( $thisYear . '-03-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-05-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    /**
     * @group seasons
     */
    public function testSummer() {
        $thisYear         = 2019;
        $string           = 'summer ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );

        $this->assertEquals( Carbon::parse( $thisYear . '-06-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-08-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    /**
     * @group seasons
     */
    public function testWinter() {
        $thisYear         = 2019;
        $nextYear         = 2020;
        $string           = 'winter ' . $thisYear;
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );

        $this->assertEquals( Carbon::parse( $thisYear . '-12-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $nextYear . '-03-01 00:00:00', $timezoneId ), $naturalDate->getLocalEnd() );
    }

}