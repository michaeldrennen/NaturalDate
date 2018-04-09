<?php

namespace MichaelDrennen\NaturalDate\Tests;

use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateDebugFunctionsTest extends TestCase {


    /**
     * @throws \Exception
     * @group string
     */
    public function testToString() {
        $this->expectOutputRegex( '/NATURAL DATE OBJECT/' );
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, FALSE );
        echo $naturalDate;
    }

    public function testToJson() {

        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );
        $jsonOutput       = $naturalDate->toJson();
        $decodedJson      = json_decode( $jsonOutput );
        $lastJsonError    = json_last_error();
        $this->assertEquals( JSON_ERROR_NONE, $lastJsonError );
    }


    public function testGetDebugMessages() {
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, FALSE );
        $debugMessages    = $naturalDate->getDebugMessages();
        $this->assertTrue( is_array( $debugMessages ) );
    }

    public function testDebugMessagesShouldBeEmpty() {
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = new NaturalDate( $string, $timezoneId, $languageCode );
        $debugMessages    = $naturalDate->getDebugMessages();
        $this->assertTrue( empty( $debugMessages ) );
    }

    public function testDebugMessagesShouldBeUnset() {
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, NULL, TRUE );
        $debugMessages    = $naturalDate->getDebugMessages();
        $this->assertTrue( empty( $debugMessages ) );
    }

}