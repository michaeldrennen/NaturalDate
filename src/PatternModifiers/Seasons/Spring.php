<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class Spring extends PatternModifier {

    protected $patterns = [
        "/spring/i",
    ];


    public function modify( NaturalDate $naturalDate ): NaturalDate {

        // I use the meteorological dates for the season changes.
        $naturalDate->setStartMonth( 3 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        // I have to do this one on the first second of Spring, because sometimes Feb has 29 days.
        $naturalDate->setEndMonth( 5 );
        $naturalDate->setEndDay( 31 );
        $naturalDate->setEndHour( 0 );
        $naturalDate->setEndMinute( 0 );
        $naturalDate->setEndSecond( 0 );

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}