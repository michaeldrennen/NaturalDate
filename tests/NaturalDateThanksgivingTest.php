<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateThanksgivingTest extends TestCase {


    /**
     * @test
     * @group holidays
     */
    public function testThanksgiving() {
        $string           = 'thanksgiving 2017';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );


        $this->assertEquals( Carbon::parse( '2017-11-23 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( '2017-11-23 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }

    /**
     * @test
     * @group holidays
     */
    public function testThanksgivingFollowedByMonthShouldThrowException() {
        $this->expectException( NaturalDateException::class );
        $string           = 'thanksgiving of january';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
    }

}