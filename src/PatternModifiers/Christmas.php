<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

class Christmas extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $naturalDate->setStartMonth( 12 );
        $naturalDate->setStartDay( 25 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );
        $naturalDate->setEndMonth( 12 );
        $naturalDate->setEndDay( 25 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::date );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        foreach ( $pregMatchMatches as $i => $string ):
            $naturalDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );
        endforeach;

        return $naturalDate;
    }
}