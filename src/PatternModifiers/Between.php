<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

/**
 * Class Between
 *
 * @package MichaelDrennen\NaturalDate\PatternModifiers
 * @pattern en  PatternMap::between     => [ '/between\s*(.*)(and|&|\+)\s*(.*)/i' ],
 */
class Between extends PatternModifier {

    /**
     * When this function is called we know the string starts with "between" and there are two captured strings that
     * should be able to be parsed by NaturalDate.
     *
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->addDebugMessage( "    Between->modify(): Just entered." );
        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * There should only ever be one captured element based on the regex pattern.
         */
        $startDatePart   = $pregMatchMatches[ 0 ];
        $connectorString = $pregMatchMatches[ 1 ];
        $endDatePart     = $pregMatchMatches[ 2 ];

        $capturedStartDate = NaturalDate::parse( $startDatePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );
        $capturedEndDate   = NaturalDate::parse( $endDatePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );

        return new NaturalDate( $naturalDate->getInput(), $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $capturedStartDate->getLocalStart(), $capturedEndDate->getLocalEnd(), NaturalDate::range, $naturalDate->getPatternModifiers() );
    }

}