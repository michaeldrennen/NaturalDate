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
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null, false );
        echo $naturalDate;
    }

    public function testGetDebugMessages() {
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null, false );
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
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers, null, true );
        $debugMessages    = $naturalDate->getDebugMessages();
        $this->assertTrue( empty( $debugMessages ) );
    }

}