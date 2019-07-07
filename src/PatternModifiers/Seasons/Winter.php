<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;


class Winter extends PatternModifier {

    protected $patterns = [
        "/winter/i",
    ];


    public function modify( NaturalDate $naturalDate ): NaturalDate {

        // I use the meteorological dates for the season changes.
        $naturalDate->setStartMonth( 12 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        // I have to do this one on the first second of Spring, because sometimes Feb has 29 days.
        $naturalDate->setEndMonth( 3 );
        $naturalDate->setEndDay( 1 );
        $naturalDate->setEndHour( 0 );
        $naturalDate->setEndMinute( 0 );
        $naturalDate->setEndSecond( 0 );

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}