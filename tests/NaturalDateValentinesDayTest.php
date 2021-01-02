<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateValentinesDayTest extends TestCase {


    public function testHolidayOfThisYear() {
        $string           = 'valentines day';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-02-14 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-02-14 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    public function testHoliday1980() {
        $string           = 'valentines day 1980';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( '1980-02-14 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1980-02-14 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testHolidayOf1980() {
        $string           = 'valentines day of 1980';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( '1980-02-14 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1980-02-14 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testHolidayFollowedByMonthShouldThrowException() {
        $this->expectException( NaturalDateException::class );
        $string           = 'valentines day of january';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        echo $naturalDate;
    }

}