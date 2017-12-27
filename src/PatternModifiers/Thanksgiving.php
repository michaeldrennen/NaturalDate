<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;

class Thanksgiving extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $naturalDate->setStartMonth( 11 );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndMonth( 11 );
        $naturalDate->setEndTimesAsEndOfDay();

        $naturalDate->setType( NaturalDate::date );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * If there is no date string after the word "Thanksgiving" then I assume they mean Thanksgiving of this year.
         */
        if ( empty( $pregMatchMatches ) ):
            $carbonThanksgiving = Carbon::parse( 'fourth thursday of november ' . date( 'Y' ) );
            return new NaturalDate( $naturalDate->getInput(), $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $carbonThanksgiving, $carbonThanksgiving, NaturalDate::date, $naturalDate->getPatternModifiers() );
        endif;


        /**
         * At most, there can only be one matching section.
         */
        $string = $pregMatchMatches[ 0 ];
        $naturalDate->addDebugMessage( "Parsing this string [" . $string . "]", __FUNCTION__, __CLASS__ );
        $capturedDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );

        if ( isset( $capturedDate ) && NaturalDate::year == $capturedDate->getType() ):
            $year               = $capturedDate->getStartYear();
            $carbonThanksgiving = Carbon::parse( 'fourth thursday of november ' . $year );
            $naturalDate->setStartYear( $year );
            $naturalDate->setEndYear( $year );
            $naturalDate->setStartDay( $carbonThanksgiving->day );
            $naturalDate->setEndDay( $carbonThanksgiving->day );
        else:
            throw new NaturalDateException( "The Thanksgiving PatternModifier needs the captured date to be of type: year. What else would make sense there?" );
        endif;

        return $naturalDate;
    }
}