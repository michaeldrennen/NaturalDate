<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\InvalidStringLengthForYear;
use MichaelDrennen\NaturalDate\NaturalDate;


class Year extends PatternModifier {

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidStringLengthForYear
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->addDebugMessage( "Just entered.", __FUNCTION__, __CLASS__ );

        // $naturalDate input will be a year of type: 2016, or 16, or '16
        $strLen = strlen( $naturalDate->getInput() );
        switch ( $strLen ):
            case 4: // 2016
                $year = $naturalDate->getInput();
                break;
            case 3: // '16
                $yearPart = substr( $naturalDate->getInput(), 1 );
                $year     = Carbon::createFromFormat( 'y', $yearPart, $naturalDate->getTimezoneId() )->year;
                break;
            case 2: // 16
                $year = Carbon::createFromFormat( 'y', $naturalDate->getInput(), $naturalDate->getTimezoneId() )->year;
                break;
            default;
                // This would only be thrown if the developer supplied their own pattern for
                throw new InvalidStringLengthForYear( "The length of the year passed in was unexpected." );
        endswitch;

        $naturalDate->addDebugMessage( "Parsed year is [" . $year . "]", __FUNCTION__, __CLASS__ );

        // @TODO Add some logic here to determine if both start and end year should be set...
        $naturalDate->setStartYear( $year );
        $naturalDate->setEndYear( $year );


        $this->setStartMonthIfNotSet( $naturalDate );
        $this->setEndMonthIfNotSet( $naturalDate );
        $this->setStartDayIfNotSet( $naturalDate );
        $this->setEndDayIfNotSet( $naturalDate );

        $naturalDate->setType( NaturalDate::year );

        $naturalDate->addDebugMessage( "naturalDate object had type set to [" . $naturalDate->getType() . "]", __FUNCTION__, __CLASS__ );

        return $naturalDate;
    }

    protected function setStartDayIfNotSet( NaturalDate &$naturalDate ) {
        $startDay = $naturalDate->getStartDay();
        if ( is_null( $startDay ) ):
            $naturalDate->setStartDay( 1 );
        endif;
    }

    protected function setEndDayIfNotSet( NaturalDate &$naturalDate ) {
        $endDay = $naturalDate->getEndDay();
        if ( is_null( $endDay ) ):
            $naturalDate->setEndDay( 31 );
        endif;
    }

    protected function setStartMonthIfNotSet( NaturalDate &$naturalDate ) {
        $startMonth = $naturalDate->getStartMonth();
        if ( is_null( $startMonth ) ):
            $naturalDate->setStartMonth( 1 );
        endif;
    }

    protected function setEndMonthIfNotSet( NaturalDate &$naturalDate ) {
        $endMonth = $naturalDate->getEndMonth();
        if ( is_null( $endMonth ) ):
            $naturalDate->setEndMonth( 12 );
        endif;
    }


}