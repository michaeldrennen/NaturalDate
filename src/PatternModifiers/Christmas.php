<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;

class Christmas extends PatternModifier {


    public function modify( NaturalDate $naturalDate ): NaturalDate {

        $pregMatchMatches = $naturalDate->getPregMatchMatches();
        $datePart         = $pregMatchMatches[ 0 ];
        var_dump( $datePart );
        $capturedDate = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );

        $year      = $naturalDate->getLocalStart()->year;
        $startDate = Carbon::parse( $year . '-12-25T00:00:00' );
        $endDate   = Carbon::parse( $year . '-12-25T23:59:59' );
        $naturalDate->setLocalStart( $startDate );
        $naturalDate->setLocalEnd( $endDate );

        return $capturedDate;
    }
}