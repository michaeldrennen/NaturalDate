<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class Fall extends PatternModifier {

    protected $patterns = [
        "/fall/i",
    ];


    public function modify( NaturalDate $naturalDate ): NaturalDate {

        // I use the meteorological dates for the season changes.
        $naturalDate->setStartMonth( 9 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        $naturalDate->setEndMonth( 11 );
        $naturalDate->setEndDay( 30 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}