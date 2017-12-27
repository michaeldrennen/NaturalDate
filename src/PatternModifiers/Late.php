<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

/**
 * Class Late
 *
 * @package MichaelDrennen\NaturalDate\PatternModifiers
 * @pattern en  PatternMap::late     => [ '/^late(.*)$/i' ],
 */
class Late extends PatternModifier {

    /**
     * When this function is called we know the string starts with "late" and the first match is something that should
     * be able to be parsed by NaturalDate::parse().
     *
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->addDebugMessage( "    Late->modify(): Just entered." );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * If the user just passed in the string "late", then assume they mean late today.
         */
        if ( empty( $pregMatchMatches ) ):
            $naturalDate->setStartYear( date( 'Y' ) );
            $naturalDate->setStartMonth( date( 'm' ) );
            $naturalDate->setStartDay( date( 'd' ) );
            $naturalDate->setStartHour( 20 );
            $naturalDate->setStartMinute( 0 );
            $naturalDate->setStartSecond( 0 );
            $naturalDate->setEndYear( date( 'Y' ) );
            $naturalDate->setEndMonth( date( 'm' ) );
            $naturalDate->setEndDay( date( 'd' ) );
            $naturalDate->setEndHour( 23 );
            $naturalDate->setEndMinute( 59 );
            $naturalDate->setEndSecond( 59 );
            $naturalDate->addDebugMessage( "    Late->modify(): the user just passed in the string \"late\", then assume they mean late today." );
            return $naturalDate;
        endif;


        /**
         * There should only ever be one captured element based on the regex pattern.
         */
        $datePart = $pregMatchMatches[ 0 ];

        $capturedDate = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );


        switch ( $capturedDate->getType() ):
            case NaturalDate::year:
                $capturedDate->addDebugMessage( "In Late: The captured date was of type [" . $capturedDate->getType() . "], so now calling modifyYear()." );
                $this->modifyYear( $capturedDate );
                break;

            case NaturalDate::month:
                $capturedDate->addDebugMessage( "   Late->modify(): The captured string was a month." );
                $this->modifyMonth( $capturedDate );
                break;

            case NaturalDate::date:
                $capturedDate->addDebugMessage( "   Late->modify(): The captured string was a date." );
                $this->modifyDate( $capturedDate );
                break;

            default:
                $capturedDate->addDebugMessage( "In Late: The captured date was of type [" . $capturedDate->getType() . "] which Late doesn't have the code to modify it." );
                $capturedDate->addDebugMessage( "In Late: The captured string was [" . $capturedDate->getInput() . "]." );

                return $capturedDate;
        endswitch;

        return $capturedDate;
    }

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @throws \Exception
     */
    protected function modifyYear( NaturalDate &$naturalDate ) {
        $naturalDate->addDebugMessage( "Inside modifyYear(), setting dates to Sep 1 and Dec 31th, and leaving times alone if they were not already set." );
        $naturalDate->setStartMonth( 9 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndMonth( 12 );
        $naturalDate->setEndDay( 31 );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndTimesAsEndOfDay();
        $naturalDate->setType( NaturalDate::year );
    }

    protected function modifyMonth( NaturalDate &$naturalDate ) {
        $naturalDate->setStartDay( 21 );
        $lastDayOfTheMonth = $naturalDate->getLocalEnd()->format( 't' );
        $naturalDate->setEndDay( $lastDayOfTheMonth );
        $this->setStartYearIfNotSetAlready( $naturalDate, date( 'Y' ) );
        $this->setEndYearIfNotSetAlready( $naturalDate, date( 'Y' ) );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndTimesAsEndOfDay();
        $naturalDate->setType( NaturalDate::month );
    }

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @throws \Exception
     */
    protected function modifyDate( NaturalDate &$naturalDate ) {
        $naturalDate->setStartHour( 17 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setEndSecond( 0 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );
        $naturalDate->setType( NaturalDate::date );
    }


}