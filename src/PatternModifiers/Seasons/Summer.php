<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class Summer extends PatternModifier {

    protected $patterns = [
        "/summer/i",
    ];


    public function modify( NaturalDate $naturalDate ): NaturalDate {

        // I use the meteorological dates for the season changes.
        $naturalDate->setStartMonth( 6 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        $naturalDate->setEndMonth( 8 );
        $naturalDate->setEndDay( 31 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}