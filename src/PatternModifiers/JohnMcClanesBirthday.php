<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class JohnMcClanesBirthday extends PatternModifier {

    /**
     * This $patterns array is set in the Languages/{en}/PatternMapForLanguages.php file for PatternModifiers that are
     * built into the NaturalDate library. For user-defined PatternModifiers, like this one, you need to define your
     * own regex patterns that will trigger this PatternModifier.
     * @var array
     */
    protected $patterns = [
        "/john mcclane\'s birthday/i",
        "/john mcclanes birthday/i",
        "/john mcclane birthday/i",
    ];

    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->setStartYear( 1955 );
        $naturalDate->setStartMonth( 11 );
        $naturalDate->setStartDay( 2 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        $naturalDate->setEndYear( 1955 );
        $naturalDate->setEndMonth( 11 );
        $naturalDate->setEndDay( 2 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::date );

        return $naturalDate;
    }
}