<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

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
        $capturedDate     = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );

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
                $naturalDate->pushUnprocessedNaturalDate( $naturalDate );
                break;
        endswitch;
        return $capturedDate;
    }

    protected function modifyYear( NaturalDate &$naturalDate ) {
        $naturalDate->setStartMonth( 1 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndMonth( 4 );
        $naturalDate->setEndDay( 30 );
    }

    protected function modifyMonth( NaturalDate &$naturalDate ) {
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndDay( 9 );
    }

    protected function modifyDate( NaturalDate &$naturalDate ) {
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setEndSecond( 0 );
        $naturalDate->setEndHour( 7 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );
    }


}