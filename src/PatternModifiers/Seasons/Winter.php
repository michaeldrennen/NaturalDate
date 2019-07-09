<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\Seasons\AbstractSeason;


class Winter extends AbstractSeason {

    /**
     * If the user enters "Winter of 1978" then NaturalDate assumes the year sent in references the START of winter.
     * @param NaturalDate $naturalDate
     * @return NaturalDate
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        parent::modify($naturalDate);
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


        // Because winter ends in the next year.
        $endYear = $naturalDate->getStartYear() + 1;
        $naturalDate->setEndYear($endYear);

        $naturalDate->setType( NaturalDate::season );

        return $naturalDate;
    }
}