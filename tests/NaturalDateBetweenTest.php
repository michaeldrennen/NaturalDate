<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateBetweenTest extends TestCase {


    public function testBetweenTwoYears() {
        $string           = 'between 2000 and 2010';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $localStart = $startDate->setTimezone( $timezoneId );
        $localEnd   = $endDate->setTimezone( $timezoneId );

        $this->assertEquals( Carbon::parse( '2000-01-01 00:00:00', $timezoneId ), $localStart );
        $this->assertEquals( Carbon::parse( '2010-12-31 23:59:59', $timezoneId ), $localEnd );
    }


    public function testBetweenTwoMonthYearCombos() {
        $string           = 'between Feb 2000 and March 2000';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $localStart = $startDate->setTimezone( $timezoneId );
        $localEnd   = $endDate->setTimezone( $timezoneId );

        $this->assertEquals( Carbon::parse( '2000-02-01 00:00:00', $timezoneId ), $localStart );
        $this->assertEquals( Carbon::parse( '2000-03-31 23:59:59', $timezoneId ), $localEnd );
    }


    /**
     * @throws \Exception
     * @group between
     */
    public function testBetweenTwoSpecificDates() {
        $string           = 'between Feb 14, 2017 and March 14, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $startDate = $naturalDate->getLocalStart();
        $endDate   = $naturalDate->getLocalEnd();

        $this->assertEquals( Carbon::parse( '2017-02-14 00:00:00', $timezoneId ), $startDate );
        $this->assertEquals( Carbon::parse( '2017-03-14 23:59:59', $timezoneId ), $endDate );
    }


    public function testBetweenTwoChristmases() {
        $string           = 'between Christmas 1979 and Xmas 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $this->assertEquals( Carbon::parse( '1979-12-25 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '2017-12-25 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    public function testBetweenChristmasAndNewYears() {
        $string           = 'between Christmas 1978 and ny 1979';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $this->assertEquals( Carbon::parse( '1978-12-25 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1979-01-01 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    public function testBetweenThanksgivingAndChristmas() {
        $string           = 'between thanksgiving and christmas 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $this->assertEquals( Carbon::parse( '2017-11-23 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '2017-12-25 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    public function testBetweenThanksgivingAndNewYearsEveFor2017() {
        $string           = 'between thanksgiving and new years eve 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $this->assertEquals( Carbon::parse( '2017-11-23 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '2017-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @throws \Exception
     * @group thisyear
     */
    public function testBetweenThanksgivingAndNewYearsEveForThisYear() {
        $string           = 'between thanksgiving and new years eve';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $dayOfThanksgivingThisYear = Carbon::parse( 'fourth thursday of november ' . date( 'Y' ) )->day;

        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-11-' . $dayOfThanksgivingThisYear . ' 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

}