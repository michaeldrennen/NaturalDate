<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;

class Yesterday extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $start = Carbon::yesterday( $naturalDate->getTimezoneId() );
        $end   = Carbon::yesterday( $naturalDate->getTimezoneId() );

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