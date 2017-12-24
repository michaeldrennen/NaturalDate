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

        $year  = null;
        $month = null;
        if ( $this->yearIsInPregMatchMatches( $pregMatchMatches ) ):
            $naturalDate->addDebugMessage( "    A year was caught in the pattern as well." );
            $yearNaturalDate = NaturalDate::parse( $pregMatchMatches[ 1 ] );
            $year            = $yearNaturalDate->getStartYear();
            $naturalDate->setStartYear( $year );
            $naturalDate->setEndYear( $year );
        endif;

        $month = $this->convertMonthStringToInteger( $pregMatchMatches[ 0 ] );

        $naturalDate->setStartMonth( $month );
        $naturalDate->setEndMonth( $month );

        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndDay( Carbon::parse( $naturalDate->getEndDate() )
                                       ->copy()
                                       ->modify( 'last day of this month' )->day );

        $naturalDate->setType( NaturalDate::month );

        return $naturalDate;
    }


    protected function yearIsInPregMatchMatches( array $pregMatchMatches ): bool {
        $pattern = '/^(\d{4}|\'\d{2}|\d{2})$/';
        foreach ( $pregMatchMatches as $i => $pregMatchMatch ):
            if ( 1 === preg_match( $pattern, trim( $pregMatchMatch ) ) ):
                return true;
            endif;
        endforeach;
        return false;
    }

    protected function convertMonthStringToInteger( string $monthString ): int {
        $fakeDate = Carbon::parse( $monthString . " 1, 2017" );
        return $fakeDate->month;
    }
}