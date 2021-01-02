<?php

namespace MichaelDrennen\NaturalDate\Tests;

use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\Year;

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