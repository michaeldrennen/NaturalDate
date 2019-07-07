<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\NaturalDate;

abstract class AbstractHoliday extends PatternModifier {

    protected $day;
    protected $month;

    /**
     * AbstractHoliday constructor.
     *
     * @param array $patterns
     */
    public function __construct( array $patterns = [] ) {
        parent::__construct( $patterns );
    }

    /**
     * @param \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    public function modify( NaturalDate $naturalDate ): NaturalDate {

        if ( false === isset( $this->day ) ):
            throw new NaturalDateException( 'The $day property must be set in the class that extends AbstractHoliday' );
        endif;

        if ( false === isset( $this->month ) ):
            throw new NaturalDateException( 'The $month property must be set in the class that extends AbstractHoliday' );
        endif;

        $pregMatchMatches = $naturalDate->getPregMatchMatches();

        /**
         * If there is no date string after the "Holiday" word then I assume they mean the given Holiday of this year.
         */
        if ( empty( $pregMatchMatches ) ):
            $naturalDate->setStartMonth( $this->month );
            $naturalDate->setStartTimesAsStartOfDay();
            $naturalDate->setEndMonth( $this->month );
            $naturalDate->setEndTimesAsEndOfDay();
            $naturalDate->setType( NaturalDate::date );
            $this->setDay( Carbon::now() );
            $naturalDate->setStartDay( $this->day );
            $naturalDate->setEndDay( $this->day );
            $naturalDate->setStartYear( date( 'Y' ) );
            $naturalDate->setEndYear( date( 'Y' ) );
            $naturalDate->setType( NaturalDate::yearlessDate );
            return $naturalDate;
        endif;

        /**
         * At most, there can only be one matching section.
         */
        $string = $pregMatchMatches[ 0 ];
        $naturalDate->addDebugMessage( "Inside AbstractHoliday->modify(), and about to parse this string [" . $string . "]" );
        $capturedDate = NaturalDate::parse( $string, $naturalDate->getTimezoneId(), $naturalDate->getLanguageCode(), $naturalDate->getPatternModifiers(), null, false );


        switch ( $capturedDate->getType() ):
            case NaturalDate::year:
                $year = $capturedDate->getStartYear();
                $naturalDate->setStartYear( $year );
                $naturalDate->setEndYear( $year );
                break;

            default:
                throw new NaturalDateException( "The 'Holiday' captured date with string [" . $string . "] was of type [" . $capturedDate->getType() . "] and the PatternModifier needs the captured date to be of type: year. What else would make sense there?" );
        endswitch;


        $naturalDate->setStartMonth( $this->month );
        $naturalDate->setStartTimesAsStartOfDay();
        $naturalDate->setEndMonth( $this->month );
        $naturalDate->setEndTimesAsEndOfDay();

        $naturalDate->setType( NaturalDate::date );

        $this->setDay( $capturedDate->getLocalStart() );


        $naturalDate->setStartDay( $this->day );
        $naturalDate->setEndDay( $this->day );


        return $naturalDate;
    }

    /**
     * For holidays like Thanksgiving, the actual date of the holiday changes from year to year. In those cases, I will
     * set the startDay as string that is able to be parsed by Carbon::parse(). Then take the date from that.
     *
     * @param Carbon $capturedDate
     */
    protected function setDay( Carbon $capturedDate ) {
        if ( false === is_numeric( $this->day ) ):
            $carbon    = Carbon::parse( $this->day . ' ' . $capturedDate->year );
            $this->day = $carbon->day;
        endif;
    }
}