<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

class Christmas extends PatternModifier {


    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $naturalDate->setStartMonth( 12 );
        $naturalDate->setStartDay( 25 );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();
        print_r( $pregMatchMatches );


        foreach ( $pregMatchMatches as $i => $string ):
            $naturalDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );
        endforeach;

        print_r( $naturalDate );


        //$datePart = $pregMatchMatches[ 0 ];
        //var_dump( $datePart );
        //$capturedDate = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );

        //$year      = $naturalDate->getLocalStart()->year;
        //$startDate = Carbon::parse( $year . '-12-25T00:00:00' );
        //$endDate   = Carbon::parse( $year . '-12-25T23:59:59' );
        $naturalDate->setLocalStart();
        $naturalDate->setLocalEnd();

        return $naturalDate;
    }
}