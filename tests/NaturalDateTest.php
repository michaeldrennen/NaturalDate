<?php

use MichaelDrennen\NaturalDate\NaturalDate;

class NaturalDateTest extends PHPUnit_Framework_TestCase {

    public function testNaturalDate() {
        $string = 'Last Friday of December 2016 11:30pm';
        $date = NaturalDate::parse($string, 'America/Denver');
        $this->assertInstanceOf(NaturalDate::class, $date, "Expecting to have an instance of NaturalDate.");
    }

}