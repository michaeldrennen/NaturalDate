<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;


class Month extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->addDebugMessage( "Inside Month->modify()" );
        //$capturedCarbon = Carbon::parse( $naturalDate->getInput(), $naturalDate->getTimezoneId() );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();
        $month            = $this->convertMonthStringToInteger( $pregMatchMatches[ 0 ] );

        $naturalDate->setStartMonth( $month );
        $naturalDate->setEndMonth( $month );

        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndDay( Carbon::parse( $naturalDate->getLocalEnd() )
                                       ->copy()
                                       ->modify( 'last day of this month' )->day );


        if ( $this->yearIsInPregMatchMatches( $pregMatchMatches ) ):
            $naturalDate->addDebugMessage( "    A year was caught in the pattern as well." );
            $yearNaturalDate = NaturalDate::parse( $pregMatchMatches[ 1 ] );
            $year            = $yearNaturalDate->getStartYear();
            $naturalDate->setStartYear( $year );
            $naturalDate->setEndYear( $year );
        else:
            $this->setYearIfUnspecified( $naturalDate, $month );
        endif;


        $naturalDate->setType( NaturalDate::month );

        return $naturalDate;
    }


    protected function yearIsInPregMatchMatches( array $pregMatchMatches ): bool {
        $pattern = '/^(\d{4}|\'\d{2}|\d{2})$/';
        foreach ( $pregMatchMatches as $i => $pregMatchMatch ):
            if ( 1 === preg_match( $pattern, trim( $pregMatchMatch ) ) ):
                return TRUE;
            endif;
        endforeach;
        return FALSE;
    }

    protected function convertMonthStringToInteger( string $monthString ): int {
        $fakeDate = Carbon::parse( $monthString . " 1, 2017" );
        return $fakeDate->month;
    }

    /**
     * If the user just specifies a month, then it's up to this block of code to determine what year was meant.
     * If the current month is equal to or after the month parsed by NaturalDate, then we assume they mean this year.
     * However, if the month parsed out is later in the calendar than the current month, then we can assume
     * the user meant the previous year's month.
     * @param NaturalDate $naturalDate
     * @param int $month
     */
    protected function setYearIfUnspecified( NaturalDate &$naturalDate, int $month ) {
        $currentMonth = date( 'n' );
        $year         = date( 'Y' );
        $previousYear = $year - 1;

        if ( $currentMonth >= $month ):
            $naturalDate->setStartYear( $year );
            $naturalDate->setEndYear( $year );
        else:
            $naturalDate->setStartYear( $previousYear );
            $naturalDate->setEndYear( $previousYear );
        endif;
    }
}