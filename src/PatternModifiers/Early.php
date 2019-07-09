<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

/**
 * Class Early
 *
 * @package MichaelDrennen\NaturalDate\PatternModifiers
 * @pattern en  PatternMap::early     => [ '/^early(.*)$/i' ],
 */
class Early extends PatternModifier {

    /**
     * When this function is called we know the string starts with "early" and the first match is something that should
     * be able to be parsed by NaturalDate::parse().
     *
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->addDebugMessage( "Just entered.", __FUNCTION__, __CLASS__ );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * If the user just passed in the string "early", then assume they mean early today.
         */
        if ( empty( $pregMatchMatches ) ):
            $naturalDate->addDebugMessage( "User just passed in the string \"early\", then assume they mean early today.", __FUNCTION__, __CLASS__ );
            $naturalDate->setStartYear( date( 'Y' ) );
            $naturalDate->setStartMonth( date( 'm' ) );
            $naturalDate->setStartDay( date( 'd' ) );
            $naturalDate->setStartHour( 0 );
            $naturalDate->setStartMinute( 0 );
            $naturalDate->setStartSecond( 0 );
            $naturalDate->setEndYear( date( 'Y' ) );
            $naturalDate->setEndMonth( date( 'm' ) );
            $naturalDate->setEndDay( date( 'd' ) );
            $naturalDate->setEndHour( 6 );
            $naturalDate->setEndMinute( 59 );
            $naturalDate->setEndSecond( 59 );
            return $naturalDate;
        endif;


        /**
         * There should only ever be one captured element based on the regex pattern.
         */
        $datePart = $pregMatchMatches[ 0 ];

        $naturalDate->addDebugMessage( "Passing the captured string [" . $datePart . "] to NaturalDate::parse()", __FUNCTION__, __CLASS__ );

        $capturedDate = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );

        $naturalDate->addDebugMessage( "The captured date is of type [" . $capturedDate->getType() . "], and entering switch now.", __FUNCTION__, __CLASS__ );

        switch ( $capturedDate->getType() ):
            case NaturalDate::year:
                $capturedDate->addDebugMessage( "In Early: The captured date was of type [" . $capturedDate->getType() . "], so now calling modifyYear().", __FUNCTION__, __CLASS__ );
                $this->modifyYear( $capturedDate );

                break;

            case NaturalDate::month:
                $capturedDate->addDebugMessage( "   Early->modify(): The captured string was a month.", __FUNCTION__, __CLASS__ );
                $this->modifyMonth( $capturedDate );
                break;

            case NaturalDate::date:
                $this->modifyDate( $capturedDate );
                break;

            default:
                $capturedDate->addDebugMessage( "In Early: The captured date was of type [" . $capturedDate->getType() . "] which Early doesn't have the code to modify it.", __FUNCTION__, __CLASS__ );
                $capturedDate->addDebugMessage( "In Early: The captured string was [" . $capturedDate->getInput() . "].", __FUNCTION__, __CLASS__ );
                //print_r($capturedDate->getDebugMessages());
                return $capturedDate;
        endswitch;

        //print_r($capturedDate->getDebugMessages());
        return $capturedDate;
    }

    protected function modifyYear( NaturalDate &$naturalDate ) {
        $naturalDate->addDebugMessage( "Inside modifyYear(), setting dates to Jan 1 and April 30th, and leaving times alone if they were not already set." );
        $naturalDate->setStartMonth( 1 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndMonth( 4 );
        $naturalDate->setEndDay( 30 );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndTimesAsEndOfDay();
        //$naturalDate->setType( NaturalDate::year );
    }

    protected function modifyMonth( NaturalDate &$naturalDate ) {

        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndDay( 9 );

        $this->setStartYearIfNotSetAlready( $naturalDate, date( 'Y' ) );
        $this->setEndYearIfNotSetAlready( $naturalDate, date( 'Y' ) );

        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndTimesAsEndOfDay();
        $naturalDate->setType( NaturalDate::month );
    }

    protected function modifyDate( NaturalDate &$naturalDate ) {
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setEndSecond( 0 );
        $naturalDate->setEndHour( 7 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );
        $naturalDate->setType( NaturalDate::date );
    }


}