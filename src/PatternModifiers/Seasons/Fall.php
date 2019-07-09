<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\Seasons\AbstractSeason;


class Fall extends AbstractSeason {

    public function modify( NaturalDate $naturalDate ): NaturalDate {

        parent::modify($naturalDate);

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