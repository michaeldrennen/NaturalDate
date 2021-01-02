<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers\Seasons;

use MichaelDrennen\NaturalDate\NaturalDate;


class Spring extends AbstractSeason {

    public function modify( NaturalDate $naturalDate ): NaturalDate {
        parent::modify($naturalDate);

        // I use the meteorological dates for the season changes.
        $naturalDate->setStartMonth( 3 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        // I have to do this one on the first second of Spring, because sometimes Feb has 29 days.
        $naturalDate->setEndMonth( 5 );
        $naturalDate->setEndDay( 31 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}