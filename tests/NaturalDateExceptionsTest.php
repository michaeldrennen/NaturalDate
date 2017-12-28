<?php
namespace MichaelDrennen\NaturalDate\Tests;


use MichaelDrennen\NaturalDate\Exceptions\InvalidLanguageCode;
use MichaelDrennen\NaturalDate\Exceptions\InvalidNaturalDateType;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateExceptionsTest extends TestCase {


    public function testInvalidLanguageCodeShouldThrowException() {
        $this->expectException( InvalidLanguageCode::class );
        $string           = 'Jan 1, 1970';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'badlanguagecode';
        $patternModifiers = [];
        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
    }

    public function testInvalidTimezoneShouldThrowException() {
        $this->expectException( InvalidTimezone::class );
        $string           = 'Jan 1, 1970';
        $timezoneId       = 'Fake/City';
        $languageCode     = 'en';
        $patternModifiers = [];
        NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
    }


    /**
     * @throws \Exception
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     * @group setType
     */
    public function testInvalidTypeShouldThrowException() {
        $this->expectException( InvalidNaturalDateType::class );
        $string           = 'Jan 1, 1970';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $naturalDate->setType( 'thisIsAnInvalidType' );
    }

    /**
     * @throws \Exception
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     * @group setType
     */
    public function testSetTypeToNullShouldThrowException() {
        $this->expectException( InvalidNaturalDateType::class );
        $string           = 'Jan 1, 1970';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $naturalDate->setType();
    }

}