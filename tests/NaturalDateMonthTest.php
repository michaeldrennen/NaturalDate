<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\Exceptions\UnparsableString;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateMonthTest extends TestCase {


    /**
     * @test
     * @group month
     */
    public function testMonthWithYear() {
        $string           = 'jan 2019';
        $timezoneId       = 'America/Denver';
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
    public function testMonthWithoutYear(){
        $string           = 'jan';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $this->assertEquals( Carbon::parse( '2019-01-01 07:00:00', 'UTC' ), $startDate );
        $this->assertEquals( Carbon::parse( '2019-02-01 06:59:59', 'UTC' ), $endDate );
    }




}