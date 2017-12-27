<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

class NewYears extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $naturalDate->setStartMonth( 1 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndMonth( 1 );
        $naturalDate->setEndDay( 1 );
        $naturalDate->setEndTimesAsEndOfDay();

        $naturalDate->setType( NaturalDate::date );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * At most, there can only be one matching section.
         */
        if ( ! empty( $pregMatchMatches ) ):
            $string = $pregMatchMatches[ 0 ];
            $naturalDate->addDebugMessage( "Inside NewYears->modify(), and about to parse this string [" . $string . "]", __FUNCTION__, __CLASS__ );
            $naturalDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );
        endif;

        return $naturalDate;
    }
}