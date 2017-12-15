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

        $naturalDate = parent::modify( $naturalDate );

        $pregMatchMatches = $naturalDate->getPregMatchMatches();
        $datePart         = $pregMatchMatches[ 0 ];
        $capturedDate     = NaturalDate::parse( $datePart, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), $naturalDate );

        echo "\n\n>>>>>>>>>>>>>>>>Inside modify early() " . $naturalDate->getInput() . " [" . $naturalDate->getType() . "] \n";

        switch ( $capturedDate->getType() ):
            case NaturalDate::year:
                $this->modifyYear( $capturedDate );
                break;

            case NaturalDate::month:
                $this->modifyMonth( $capturedDate );
                break;

            case NaturalDate::date:
                $this->modifyDate( $capturedDate );
                break;

            default:
                $capturedDate->pushUnprocessedNaturalDate( $naturalDate );
                $capturedDate->setProcessed( false );
                echo "\n\n>>>>>>>>>>>>>>>>Pushed unprocessed.\n";
                return $capturedDate;
        endswitch;
        $capturedDate->setProcessed( true );


        return $capturedDate;
    }

    protected function modifyYear( NaturalDate &$naturalDate ) {
        $naturalDate->setStartMonth( 1 );
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndMonth( 4 );
        $naturalDate->setEndDay( 30 );
        $naturalDate->setType(NaturalDate::year);
        echo "\n\n>>>>>>>>>>>>>>>>inside modify year\n";
    }

    protected function modifyMonth( NaturalDate &$naturalDate ) {
        $naturalDate->setStartDay( 1 );
        $naturalDate->setEndDay( 9 );
        $naturalDate->setType(NaturalDate::month);
    }

    protected function modifyDate( NaturalDate &$naturalDate ) {
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setEndSecond( 0 );
        $naturalDate->setEndHour( 7 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );
        $naturalDate->setType(NaturalDate::date);
    }


}