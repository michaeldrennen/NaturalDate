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
        $startDatePart = $pregMatchMatches[ 0 ];
        //$connectorString = $pregMatchMatches[ 1 ];
        $endDatePart = $pregMatchMatches[ 2 ];

        $capturedStartDate = NaturalDate::parse( $startDatePart,
                                                 $naturalDate->getTimezoneId(),
                                                 $naturalDate->getLanguageCode(),
                                                 $naturalDate->getPatternModifiers(),
                                                 NULL, FALSE );
        $capturedEndDate   = NaturalDate::parse( $endDatePart,
                                                 $naturalDate->getTimezoneId(),
                                                 $naturalDate->getLanguageCode(),
                                                 $naturalDate->getPatternModifiers(),
                                                 NULL, FALSE );


        $naturalDate->addDebugMessage( "Captured start date is: " . $capturedStartDate->getLocalStart(), __FUNCTION__,
                                       __CLASS__ );
        $naturalDate->addDebugMessage( "Captured end date is: " . $capturedEndDate->getLocalEnd(), __FUNCTION__,
                                       __CLASS__ );


        /**
         * Why the need for this code?
         * EXAMPLE STRING: Between Thanksgiving and Christmas 2017
         */
        if ( NaturalDate::yearlessDate == $capturedStartDate->getType() ):
            $modifiedStartDatePart = $startDatePart . " " . $capturedEndDate->getEndYear();
            $capturedStartDate     = NaturalDate::parse( $modifiedStartDatePart,
                                                         $naturalDate->getTimezoneId(),
                                                         $naturalDate->getLanguageCode(),
                                                         $naturalDate->getPatternModifiers(),
                                                         NULL, FALSE );

            // So I just pasted the end year onto the start date because it didn't have a year.
            // Now, for a small little wrinkle.
            // What if someone enters this as a date: "Between Christmas and Valentine's Day 2017"
            // The existing logic will make it: "Between Christmas 2017 and Valentine's Day 2017"
            // That doesn't make sense since I expect the users to enter dates chronologically.
            // So if the start date is older than the end date, append the end year minus 1 year.
            if ( $capturedStartDate->getLocalStart() >= $capturedEndDate->getLocalEnd() ):
                $newStartYear = (int)$capturedEndDate->getEndYear() - 1;
                $modifiedStartDatePart = $startDatePart . " " . $newStartYear;
                $capturedStartDate     = NaturalDate::parse( $modifiedStartDatePart,
                                                             $naturalDate->getTimezoneId(),
                                                             $naturalDate->getLanguageCode(),
                                                             $naturalDate->getPatternModifiers(),
                                                             NULL, FALSE );
            endif;
        endif;


        switch ( $capturedEndDate->getType() ):
            case NaturalDate::date:
                $naturalDate->addDebugMessage( "capturedEndDate was of type date, so setting hh:mm:ss to 23:59:59",
                                               __FUNCTION__, __CLASS__ );
                $capturedEndDate->setEndHour( 23 );
                $capturedEndDate->setEndMinute( 59 );
                $capturedEndDate->setEndSecond( 59 );
                break;

            /**
             * If the end year is null, look at the startYear in the capturedStartDate object. If it makes sense to use that
             * year, then do it. If not, take the next year.
             */
            case NaturalDate::yearlessDate:
                $testCarbon = Carbon::create( $capturedStartDate->getStartYear(), $capturedEndDate->getEndMonth(),
                                              $capturedEndDate->getEndDay(), $capturedEndDate->getEndHour(),
                                              $capturedEndDate->getEndMinute(), $capturedEndDate->getEndSecond(),
                                              $capturedEndDate->getTimezoneId() );
                if ( $testCarbon >= $capturedStartDate->getLocalStart() ):
                    $capturedEndDate->setEndYear( $capturedStartDate->getStartYear() );
                else:
                    $endYear = $capturedStartDate->getStartYear() + 1;
                    $naturalDate->addDebugMessage( "capturedEndDate year was null and setting it to the startDate's year would put the end before the start. So setting the endYear to the next year [" . $endYear . "]",
                                                   __FUNCTION__, __CLASS__ );
                    $capturedEndDate->setEndYear( $endYear );
                endif;
                break;
        endswitch;


        return new NaturalDate( $naturalDate->getInput(),
                                $naturalDate->getTimezoneId(),
                                $naturalDate->getLanguageCode(),
                                $capturedStartDate->getLocalStart(), $capturedEndDate->getLocalEnd(),
                                NaturalDate::range, $naturalDate->getPatternModifiers() );
    }

}