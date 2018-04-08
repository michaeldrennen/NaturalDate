<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class JohnMcClanesBirthday extends PatternModifier {

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