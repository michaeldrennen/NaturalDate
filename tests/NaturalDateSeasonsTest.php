<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateSeasonsTest extends TestCase {


    /**
     * @group seasons
     */
    public function testFall() {
        $string           = 'fall 2019';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $thisYear = 2019;
        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


    /**
     * @group seasons
     */
    public function testAutumn() {
        $string           = 'autumn 2019';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null );

        $thisYear = 2019;
        $this->assertEquals( Carbon::parse( $thisYear . '-09-01 00:00:00', $timezoneId ), $naturalDate->getLocalStart() );
        $this->assertEquals( Carbon::parse( $thisYear . '-12-31 23:59:59', $timezoneId ), $naturalDate->getLocalEnd() );
    }


}