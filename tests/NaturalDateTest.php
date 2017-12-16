<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\JohnMcClanesBirthday;
use PHPUnit\Framework\TestCase;

class NaturalDateTest extends TestCase {

    public function testChristmas() {
        $string           = 'xmas 1979';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1979-12-25 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1979-12-26 06:59:59', 'UTC' ), $endDate );
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

    public function testNaturalDateInstantiation() {
        $string           = 'Last Friday of December 2016 11:30pm';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertInstanceOf( NaturalDate::class, $date, "Expecting to have an instance of NaturalDate." );
    }

    public function testYear() {
        $string           = '1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1978-01-01 07:00:00', 'UTC' ), $startDate );
        // $this->assertEquals( Carbon::parse( '1979-01-01 06:59:59', 'UTC' ), $endDate );
    }


    public function testEarlyModifierYear() {
        $string           = 'early 2016';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '2016-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '2016-05-01 05:59:59', 'UTC' ), $endDate );
    }



    public function testEarlyModifierWithMonthAndYear() {
        $string           = 'early jan 1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1978-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1978-01-10 06:59:59', 'UTC' ), $endDate );
    }


    public function testParseWithAdditionalPatternModifierAsJohnMcClanesBirthday() {
        $string           = "john mcclane's birthday";
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [ 'JMBirthday' => new JohnMcClanesBirthday( [] ) ];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();
        $type             = $naturalDate->getType();

        $this->assertEquals( Carbon::parse( '1955-11-02 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1955-11-03 06:59:59', 'UTC' ), $endDate );
        $this->assertEquals( NaturalDate::date, $type );
    }

    public function testEarlyModifierJustMonth() {
        $string           = 'early jan';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();
        $thisYear         = date( 'Y' ); // Keeps the Unit Tests up to date.

        print_r( $naturalDate->getDebugMessages() );

        $this->assertEquals( Carbon::parse( $thisYear . '-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( $thisYear . '-01-10 06:59:59', 'UTC' ), $endDate );
    }


    //public function testNaturalDateWithUnparsableString() {
    //    $this->expectException( StrToTimeParseFailure::class );
    //    $string = "Did you ever hear the tragedy of Darth Plagueis The Wise?";
    //    NaturalDate::parse( $string, 'America/Denver' );
    //}

}