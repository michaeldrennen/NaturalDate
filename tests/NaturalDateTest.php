<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\JohnMcClanesBirthday;
use PHPUnit\Framework\TestCase;

class NaturalDateTest extends TestCase {


    public function testNaturalDateInstantiation() {
        $string           = 'Last Friday of December 2016 11:30pm';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        try {
            $date = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        } catch ( \Exception $exception ) {
            // Do nothing.
        }

        $this->assertInstanceOf( NaturalDate::class, $date, "Expecting to have an instance of NaturalDate." );
    }

    public function testYear() {
        $string           = '1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1978-01-01 00:00:00' ), $startDate );
        $this->assertEquals( Carbon::parse( '1978-12-31 23:59:59' ), $endDate );
    }


    public function testEarlyModifierYear() {
        $string           = 'early 2016';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        try {
            $date      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
            $startDate = $date->getUtcStart();
            $endDate   = $date->getUtcEnd();

            $this->assertEquals( Carbon::parse( '2016-01-01 00:00:00' ), $startDate );
            $this->assertEquals( Carbon::parse( '2016-04-30 23:59:59' ), $endDate );
        } catch ( \Exception $exception ) {
            print_r( $exception->getTrace() );
            var_dump( $exception->getMessage() );
            print( $exception->getTraceAsString() );
        }
    }

    public function testEarlyModifierJustMonth() {
        $string           = 'early jan';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $thisYear = date( 'Y' ); // Keeps the Unit Tests up to date.
        $this->assertEquals( Carbon::parse( $thisYear . '-01-01 00:00:00' ), $startDate );
        $this->assertEquals( Carbon::parse( $thisYear . '-01-09 23:59:59' ), $endDate );
    }

    public function testEarlyModifierWithMonthAndYear() {
        $string           = 'early jan 1978';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $date->getUtcStart();
        $endDate          = $date->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1978-01-01 00:00:00' ), $startDate );
        $this->assertEquals( Carbon::parse( '1978-01-09 23:59:59' ), $endDate );
    }

    public function testParseWithAdditionalPatternModifierAsJohnMcClanesBirthday() {


        $string           = "john mcclane's birthday";
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [ 'JMBirthday' => new JohnMcClanesBirthday( [] ) ];
        $date             = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
    }

    //public function testNaturalDateWithUnparsableString() {
    //    $this->expectException( StrToTimeParseFailure::class );
    //    $string = "Did you ever hear the tragedy of Darth Plagueis The Wise?";
    //    NaturalDate::parse( $string, 'America/Denver' );
    //}

}