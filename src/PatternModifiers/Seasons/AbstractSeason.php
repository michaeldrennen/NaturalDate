<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers\Seasons;

use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;
use MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier;

abstract class AbstractSeason extends PatternModifier {

    public function __construct( array $patterns = [] ) {
        parent::__construct( $patterns );
    }

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * If there is no date string after the "Season" word then I assume they mean the given Season of this year.
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
        $naturalDate->addDebugMessage( "Inside AbstractSeason->modify(), and about to parse this string [" . $string . "]" );
        $capturedDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), NULL, FALSE );


        switch ( $capturedDate->getType() ):
            case NaturalDate::year:
                $year = $capturedDate->getStartYear();
                $naturalDate->setStartYear( $year );
                $naturalDate->setEndYear( $year );
                break;

            default:
                throw new NaturalDateException( "The 'Season' captured date with string [" . $string . "] was of type [" . $capturedDate->getType() . "] and the PatternModifier needs the captured date to be of type: year. What else would make sense there?" );
        endswitch;

        return $naturalDate;
    }
}