<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
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
        $naturalDate->addDebugMessage( "Just entered.", __FUNCTION__, __CLASS__ );
        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * There should only ever be one captured element based on the regex pattern.
         */
        $startDatePart   = $pregMatchMatches[ 0 ];
        $connectorString = $pregMatchMatches[ 1 ];
        $endDatePart     = $pregMatchMatches[ 2 ];

        $capturedStartDate = NaturalDate::parse( $startDatePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate, false );
        $capturedEndDate   = NaturalDate::parse( $endDatePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate, false );

        $naturalDate->addDebugMessage( "Captured start date is: " . $capturedStartDate->getLocalStart(), __FUNCTION__, __CLASS__ );
        $naturalDate->addDebugMessage( "Captured end date is: " . $capturedEndDate->getLocalEnd(), __FUNCTION__, __CLASS__ );

        if ( NaturalDate::date == $capturedEndDate->getType() ):
            $naturalDate->addDebugMessage( "capturedEndDate was of type date, so setting hh:mm:ss to 23:59:59", __FUNCTION__, __CLASS__ );
            $capturedEndDate->setEndHour( 23 );
            $capturedEndDate->setEndMinute( 59 );
            $capturedEndDate->setEndSecond( 59 );
        endif;

        /**
         * If the end year is null, look at the startYear in the capturedStartDate object. If it makes sense to use that
         * year, then do it. If not, take the next year.
         */
        if ( is_null( $capturedEndDate->getEndYear() ) &&
             ! is_null( $capturedEndDate->getEndMonth() ) &&
             ! is_null( $capturedEndDate->getEndDay() )
        ):
            $testCarbon = Carbon::create( $capturedStartDate->getStartYear(), $capturedEndDate->getEndMonth(), $capturedEndDate->getEndDay(), null, null, null, $capturedEndDate->getTimezoneId() );
            if ( $testCarbon >= $capturedStartDate->getLocalStart() ):
                $capturedEndDate->setEndYear( $capturedStartDate->getStartYear() );
            else:
                $endYear = $capturedStartDate->getStartYear() + 1;
                $naturalDate->addDebugMessage( "capturedEndDate year was null and setting it to the startDate's year would put the end before the start. So setting the endYear to the next year [" . $endYear . "]", __FUNCTION__, __CLASS__ );
                $capturedEndDate->setEndYear( $endYear );
            endif;
        elseif ( $capturedStartDate <= $capturedEndDate ):
            $endYear = $capturedStartDate->getStartYear() + 1;
            $naturalDate->addDebugMessage( "capturedEndDate less than capturedStartDate so setting the endYear to the next year [" . $endYear . "]", __FUNCTION__, __CLASS__ );
            $capturedEndDate->setEndYear( $endYear );
        endif;

        print_r( $naturalDate->getDebugMessages() );

        return new NaturalDate( $naturalDate->getInput(),
                                $naturalDate->getTimezoneId(),
                                $naturalDate->getLanguageCode(),
                                $capturedStartDate->getLocalStart(), $capturedEndDate->getLocalEnd(),
                                NaturalDate::range, $naturalDate->getPatternModifiers() );
    }

}