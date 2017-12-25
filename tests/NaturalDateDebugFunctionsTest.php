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
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        echo $naturalDate;
    }

    public function testGetDebugMessages() {
        $string           = 'December 25, 2017';
        $timezoneId       = 'America/Denver';
        $languageCode     = 'en';
        $patternModifiers = [];
        $naturalDate      = NaturalDate::parse( $string, $timezoneId, $languageCode, $patternModifiers );
        $debugMessages    = $naturalDate->getDebugMessages();
        $this->assertTrue( is_array( $debugMessages ) );
    }

}