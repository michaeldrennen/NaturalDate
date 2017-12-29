<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;

class Today extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $start = Carbon::today( $naturalDate->getTimezoneId() );
        $end   = Carbon::today( $naturalDate->getTimezoneId() );

        $naturalDate->setStartYear( $start->year );
        $naturalDate->setStartMonth( $start->month );
        $naturalDate->setStartDay( $start->day );
        $naturalDate->setEndYear( $end->year );
        $naturalDate->setEndMonth( $end->month );
        $naturalDate->setEndDay( $end->day );

        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndTimesAsEndOfDay();

        $naturalDate->setType( NaturalDate::date );
        return $naturalDate;
    }
}