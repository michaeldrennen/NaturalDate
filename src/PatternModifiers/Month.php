<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;


class Month extends PatternModifier {

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        print( "\n\n" . $naturalDate->getInput() );
        print( "\n" . $naturalDate->getTimezoneId() );
        print( "\n\n" );
        $capturedCarbon = Carbon::parse( $naturalDate->getInput(), $naturalDate->getTimezoneId() );
        print( "\n\ncapturedCarbon" );
        print_r( $capturedCarbon );

        $startDate = $capturedCarbon->copy()->modify( 'first day of this month' );
        $endDate   = $startDate->copy()->modify( 'last day of this month' );

        return new NaturalDate( $naturalDate->getInput(),
                                $naturalDate->getTimezoneId(),
                                $naturalDate->getLanguageCode(),
                                $startDate,
                                $endDate,
                                NaturalDate::month,
                                $naturalDate->getPatternModifiers() );

    }

    private function hasMonthAndYear( $elements ): bool {
        return count( $elements ) == 2 ? true : false;
    }

    private function hasJustMonthYear( $elements ): bool {
        return count( $elements ) == 1 ? true : false;
    }


}