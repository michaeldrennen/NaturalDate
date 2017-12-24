<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\Exceptions\UnparsableString;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\JohnMcClanesBirthday;
use PHPUnit\Framework\TestCase;

class NaturalDateTest extends TestCase {





    public function testChristmas() {
        $string           = 'xmas 1979';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '1979-12-25 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1979-12-26 06:59:59', 'UTC' ), $endDate );
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



    public function testParseWithAdditionalPatternModifierAsJohnMcClanesBirthday() {
        $string           = "john mcclane's birthday";
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [ 'JMBirthday' => new JohnMcClanesBirthday( [] ) ];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );


        $startDate = $naturalDate->getUtcStart();
        $endDate   = $naturalDate->getUtcEnd();
        $type      = $naturalDate->getType();

        $this->assertEquals( Carbon::parse( '1955-11-02 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '1955-11-03 06:59:59', 'UTC' ), $endDate );
        $this->assertEquals( NaturalDate::date, $type );
    }

    public function testNaturalDateWithUnparsableString() {
        $this->expectException( UnparsableString::class );
        $string = "Did you ever hear the tragedy of Darth Plagueis The Wise?";
        NaturalDate::parse( $string, 'America/Denver' );
    }

}