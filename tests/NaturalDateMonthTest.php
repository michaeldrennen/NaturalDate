<?php

namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateMonthTest extends TestCase {


    /**
     * @test
     * @group month
     */
    public function testMonthWithYear() {
        $string           = 'jan 2019';
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '2019-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '2019-02-01 06:59:59', 'UTC' ), $endDate );
    }

    /**
     * @test
     * @group month
     */
    public function testMonthWithoutYear() {
        $string           = 'jan';
        $currentYear      = date( 'Y' ); // So the unit test will work next year.
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( $currentYear . '-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( $currentYear . '-02-01 06:59:59', 'UTC' ), $endDate );
    }


}