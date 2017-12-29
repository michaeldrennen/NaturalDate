<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateBetweenTest extends TestCase {


    /**
     * @throws \Exception
     * @group btw
     */
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
     * @group 2
     */
    public function testBetweenThanksgivingAndNewYearsEveForThisYear() {
        $string           = 'between thanksgiving and new years eve';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $dayOfThanksgivingThisYear = Carbon::parse( 'fourth thursday of november ' . date( 'Y' ) )->day;

        $expectedStart = date( 'Y' ) . '-11-' . $dayOfThanksgivingThisYear . ' 00:00:00';
        $expectedEnd   = date( 'Y' ) . '-12-31 23:59:59';

        $this->assertEquals( Carbon::parse( $expectedStart, $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $expectedEnd, $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @throws \Exception
     */
    public function testBetweenWithReversedSpecialDatesHavingStartDateThisYear() {
        $string           = 'between thanksgiving and halloween';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null, false );

        $dayOfThanksgivingThisYear = Carbon::parse( 'fourth thursday of november ' . date( 'Y' ) )->day;
        $startYear                 = date( 'Y' );
        $nextYear                  = $startYear + 1;
        $this->assertEquals( Carbon::parse( $startYear . '-11-' . $dayOfThanksgivingThisYear . ' 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $nextYear . '-10-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @throws \Exception
     */
    public function testBetweenYesterdayAndToday() {
        $string           = 'between yesterday and today';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $yesterdayString = date( 'Y-m-d 00:00:00', time() - 86400 );
        $todayString     = date( 'Y-m-d 23:59:59', time() );

        $yesterday = Carbon::parse( $yesterdayString, $timezoneId );
        $today     = Carbon::parse( $todayString, $timezoneId );

        $this->assertEquals( $yesterday, $naturalDate->getLocalStart() );
        $this->assertEquals( $today, $naturalDate->getLocalEnd() );
    }

    /**
     * @throws \Exception
     */
    public function testBetweenTodayAndTomorrow() {
        $string           = 'between today and tomorrow';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $todayString    = date( 'Y-m-d 00:00:00', time() );
        $tomorrowString = date( 'Y-m-d 23:59:59', time() + 86400 );

        $today    = Carbon::parse( $todayString, $timezoneId );
        $tomorrow = Carbon::parse( $tomorrowString, $timezoneId );

        $this->assertEquals( $today, $naturalDate->getLocalStart() );
        $this->assertEquals( $tomorrow, $naturalDate->getLocalEnd() );
    }

}