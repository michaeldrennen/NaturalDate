<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

class Halloween extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $naturalDate->setStartMonth( 10 );
        $naturalDate->setStartDay( 31 );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndMonth( 10 );
        $naturalDate->setEndDay( 31 );
        $naturalDate->setEndTimesAsEndOfDay();

        $naturalDate->setType( NaturalDate::date );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * At most, there can only be one matching section.
         */
        if ( ! empty( $pregMatchMatches ) ):
            $string = $pregMatchMatches[ 0 ];
            $naturalDate->addDebugMessage( "Parsing this string [" . $string . "]", __FUNCTION__, __CLASS__ );
            $naturalDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );
        endif;

        return $naturalDate;
    }
}