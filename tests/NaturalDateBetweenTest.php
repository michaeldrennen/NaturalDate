<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateBetweenTest extends TestCase {


    public function testBetweenTwoYears() {
        $string           = 'between 2000 and 2010';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $startDate        = $naturalDate->getUtcStart();
        $endDate          = $naturalDate->getUtcEnd();

        $localStart = $startDate->setTimezone( $timezoneId );
        $localEnd   = $endDate->setTimezone( $timezoneId );

        $this->assertEquals( Carbon::parse( '2000-01-01 00:00:00', $timezoneId ), $localStart );
        $this->assertEquals( Carbon::parse( '2010-12-31 23:59:59', $timezoneId ), $localEnd );
    }


}