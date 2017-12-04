<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;

class Early extends PatternModifier {

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $pregMatchMatches = $naturalDate->getPregMatchMatches();
        $datePart         = $pregMatchMatches[ 0 ];
        $capturedDate     = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers() );

        switch ( $capturedDate->getType() ):
            case 'year':
                $this->modifyYear( $capturedDate );
                break;

            case 'month':
                $this->modifyMonth( $capturedDate );
                break;

            case 'date':
                $this->modifyDate( $capturedDate );
                break;

            default:

                break;
        endswitch;
        return $capturedDate;
    }

    protected function modifyYear( NaturalDate &$naturalDate ) {
        $year      = $naturalDate->getUtcStart()->year;
        $startDate = Carbon::parse( $year . '-01-01T00:00:00' );
        $endDate   = Carbon::parse( $year . '-04-30T23:59:59' );
        $naturalDate->setUtcStart( $startDate );
        $naturalDate->setUtcEnd( $endDate );
    }

    protected function modifyMonth( NaturalDate &$naturalDate ) {
        $year  = $naturalDate->getUtcStart()->year;
        $month = $naturalDate->getUtcStart()->month;

        $startDate = Carbon::parse( $year . '-' . $month . '-01T00:00:00' );
        $endDate   = Carbon::parse( $year . '-' . $month . '-09T23:59:59' );
        $naturalDate->setUtcStart( $startDate );
        $naturalDate->setUtcEnd( $endDate );
    }

    protected function modifyDate( NaturalDate &$naturalDate ) {
        $year  = $naturalDate->getUtcStart()->year;
        $month = $naturalDate->getUtcStart()->month;
        $day   = $naturalDate->getUtcStart()->day;

        $startDate = Carbon::parse( $year . '-' . $month . '-' . $day . 'T00:00:00' );
        $endDate   = Carbon::parse( $year . '-' . $month . '-' . $day . 'T07:59:59' );
        $naturalDate->setUtcStart( $startDate );
        $naturalDate->setUtcEnd( $endDate );
    }


}