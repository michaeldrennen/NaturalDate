<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;


class Year extends PatternModifier {

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        // $naturalDate input will be a year of type: 2016, or 16, or '16
        $strLen = strlen( $naturalDate->getInput() );
        switch ( $strLen ):
            case 4: // 2016
                $year = $naturalDate->getInput();
                break;
            case 3: // '16
                $yearPart = substr( $naturalDate->getInput(), 1 );
                $year     = Carbon::createFromFormat( 'y', $yearPart, $naturalDate->getTimezoneId() );
                break;
            case 2: // 16
                $year = Carbon::createFromFormat( 'y', $naturalDate->getInput(), $naturalDate->getTimezoneId() );
                break;
            default;
                throw new NaturalDateException( "The length of the year passed in was unexpected. " );
        endswitch;

        // @TODO Add some logic here to determine if both start and end year should be set...
        $naturalDate->setStartYear( $year );
        $naturalDate->setEndYear( $year );

        parent::modify($naturalDate);
        return $naturalDate;

        //$start = Carbon::parse( $year . '-01-01 00:00:00' );
        //$end   = Carbon::parse( $year . '-12-31 23:59:59' );
        //
        //return new NaturalDate( $naturalDate->getInput(),
        //                        $naturalDate->getTimezoneId(),
        //                        $naturalDate->getLanguageCode(),
        //                        $start,
        //                        $end,
        //                        NaturalDate::year,
        //                        $naturalDate->getPatternModifiers() );
    }


}