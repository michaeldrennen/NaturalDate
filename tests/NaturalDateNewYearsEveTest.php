<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateNewYearsEveTest extends TestCase {


    /**
     * @throws \Exception
     * @group nye
     */
    public function testNyeForThisYear() {
        $string           = 'new years eve';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $thisYear = date( 'Y' );
        $this->assertEquals( Carbon::parse( $thisYear . '-12-31 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testNyeFor1978() {
        $string           = 'new years eve 1978';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $this->assertEquals( Carbon::parse( '1978-12-31 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '1978-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    public function testNyeFollowedByHolidayShouldThrowException() {
        $this->expectException( NaturalDateException::class );
        $string           = 'new years eve of halloween';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );
    }

}