<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\InvalidStringLengthForYear;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternMap;
use MichaelDrennen\NaturalDate\PatternModifiers\JohnMcClanesBirthday;
use PHPUnit\Framework\TestCase;

class NaturalDateCustomPatternModifiersTest extends TestCase {


    public function testParseWithAdditionalPatternModifierAsJohnMcClanesBirthday() {
        $string           = "john mcclane's birthday";
        $timezoneId       = 'America/Phoenix';
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

    /**
     * @throws \Exception
     * @group badyear
     */
    public function testParseWithAdditionalPatternModifierThatHasCustomYearPatternsButUsesDefaultYearParsing() {
        $this->expectException( InvalidStringLengthForYear::class );
        $string           = "20017";
        $timezoneId       = 'America/Phoenix';
        $languageCode     = 'en';
        $patternModifiers = [ PatternMap::year => new CustomYear( [] ) ];

        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

    }


}

