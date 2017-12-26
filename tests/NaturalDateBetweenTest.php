<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
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
        $string = 'between Feb 14, 2017 and March 14, 2017';
        //$string           = 'between asdf and qwer';
        //$string           = 'between jan 1, 2000 9pm and jan 15, 2000';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        print_r( $naturalDate->getDebugMessages() );
        $startDate = $naturalDate->getUtcStart();
        $endDate   = $naturalDate->getUtcEnd();

        $localStart = $startDate->setTimezone( $timezoneId );
        $localEnd   = $endDate->setTimezone( $timezoneId );

        $this->assertEquals( Carbon::parse( '2017-02-14 00:00:00', $timezoneId ), $localStart );
        $this->assertEquals( Carbon::parse( '2017-03-14 23:59:59', $timezoneId ), $localEnd );
    }


}