<?php

use MichaelDrennen\NaturalDate\NaturalDate;

class NaturalDateTest extends PHPUnit_Framework_TestCase {

    public function testNaturalDate() {
        $string = 'Last Friday of December 2016';
        $date = NaturalDate::parse($string);
        $this->assertInstanceOf('NaturalDate', $date, "Expecting to have an instance of NaturalDate.");
    }

}