<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

interface PatternModifierInterface {

    public function __construct( array $patterns );

    public function modify( NaturalDate $naturalDate ): NaturalDate;
}