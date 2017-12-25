<?php
namespace MichaelDrennen\NaturalDate\Tests;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\InvalidStringLengthForYear;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternMap;
use MichaelDrennen\NaturalDate\PatternModifiers\JohnMcClanesBirthday;
use MichaelDrennen\NaturalDate\PatternModifiers\Year;
use PHPUnit\Framework\TestCase;

/**
 * Class CustomYear
 *
 * @package MichaelDrennen\NaturalDate\Tests
 *          The purpose for this custom class is to trigger an invalid string length exception error. The only way that
 *          can happen is if a developer overrides the Year pattern with their own, but makes use of the parent
 *          PatternModifier modify() function which only has code to parse 2014, 14, '14 type years.
 */
class CustomYear extends Year {

    protected $patterns = [
        '/^(\d{5})$/',
    ];

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        return parent::modify( $naturalDate );
    }
}

class NaturalDateCustomPatternModifiersTest extends TestCase {


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

    /**
     * @throws \Exception
     * @group badyear
     */
    public function testParseWithAdditionalPatternModifierThatHasCustomYearPatternsButUsesDefaultYearParsing() {
        $this->expectException( InvalidStringLengthForYear::class );
        $string           = "20017";
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [ PatternMap::year => new CustomYear( [] ) ];

        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );

    }


}