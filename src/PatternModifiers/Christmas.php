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
        $naturalDate->setStartTimesAsStartOfToday();
        $naturalDate->setEndMonth( 12 );
        $naturalDate->setEndDay( 25 );
        $naturalDate->setEndTimesAsEndOfToday();

        $naturalDate->setType( NaturalDate::date );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * At most, there can only be one matching section.
         */
        if ( ! empty( $pregMatchMatches ) ):
            $string = $pregMatchMatches[ 0 ];
            $naturalDate->addDebugMessage( "Inside Christmas->modify(), and about to parse this string [" . $string . "]" );
            $naturalDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );
        endif;

        return $naturalDate;
    }
}