<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


/**
 * Interface PatternModifierInterface
 * @package MichaelDrennen\NaturalDate\PatternModifiers
 */
interface PatternModifierInterface {

    /**
     * PatternModifierInterface constructor.
     * @param array $patterns
     */
    public function __construct( array $patterns );

    /**
     * @param NaturalDate $naturalDate
     * @return NaturalDate
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate;
}