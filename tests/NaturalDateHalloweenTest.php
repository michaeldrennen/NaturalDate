<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateHalloweenTest extends TestCase {


    public function testHalloweenOfThisYear() {
        $string           = 'halloween';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-10-31 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( date( 'Y' ) . '-10-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    public function testHalloween1980() {
        $string           = 'halloween 1980';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( '1980-10-31 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1980-10-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testHalloweenOf1980() {
        $string           = 'halloween of 1980';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

        $this->assertEquals( Carbon::parse( '1980-10-31 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1980-10-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testHalloweenFollowedByMonthShouldThrowException() {
        $this->expectException( NaturalDateException::class );
        $string           = 'halloween of january';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
    }

}