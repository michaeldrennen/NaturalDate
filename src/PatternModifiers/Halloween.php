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

        /**
         * If there is no date string after the word "Halloween" then I assume they mean Halloween of this year.
         */
        if ( empty( $pregMatchMatches ) ):
            $naturalDate->setStartYear( date( 'Y' ) );
            $naturalDate->setEndYear( date( 'Y' ) );
            return $naturalDate;
        endif;


        /**
         * At most, there can only be one matching section.
         */
        $string = $pregMatchMatches[ 0 ];
        $naturalDate->addDebugMessage( "Parsing this string [" . $string . "]", __FUNCTION__, __CLASS__ );
        $capturedDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );

        if ( isset( $capturedDate ) && NaturalDate::year == $capturedDate->getType() ):
            $year = $capturedDate->getStartYear();
            $naturalDate->setStartYear( $year );
            $naturalDate->setEndYear( $year );
        else:
            throw new NaturalDateException( "The Halloween PatternModifier needs the captured date to be of type: year. What else would make sense there?" );
        endif;

        return $naturalDate;
    }
}