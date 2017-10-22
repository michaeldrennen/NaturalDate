<?php
namespace MichaelDrennen\NaturalDate\Tests;

use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;
use MichaelDrennen\NaturalDate\NaturalDate;
use PHPUnit\Framework\TestCase;

class NaturalDateTest extends TestCase {

    public function testNaturalDate() {
        $string = 'Last Friday of December 2016 11:30pm';
        $date   = NaturalDate::parse( $string, 'America/Denver' );
        $this->assertInstanceOf( NaturalDate::class, $date, "Expecting to have an instance of NaturalDate." );
    }

    public function testNaturalDateWithUnparsableString() {
        $this->expectException( StrToTimeParseFailure::class );
        $string = "Did you ever hear the tragedy of Darth Plagueis The Wise?";
        NaturalDate::parse( $string, 'America/Denver' );
    }

}