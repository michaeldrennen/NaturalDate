<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;


class Month extends PatternModifier {

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $capturedCarbon = Carbon::parse( $naturalDate->getInput(), $naturalDate->getTimezoneId() );
        // @TODO Add code to to determine if start month or end month should be set.
        $naturalDate->setStartMonth( $capturedCarbon->month );
        $naturalDate->setEndMonth( $capturedCarbon->month );
        $naturalDate->setType( NaturalDate::month );

        return $naturalDate;
    }

}