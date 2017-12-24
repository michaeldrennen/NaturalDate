<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidLanguageCode;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound;
use MichaelDrennen\NaturalDate\Exceptions\UnparsableString;
use MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier;


class NaturalDate {

    /**
     * These are the different "types" of NaturalDate objects. The type is used by the PatternModifiers, so they can
     * know exactly how the date should be modified. For example if the "type" is year, and the "Early" PatternModifier
     * is used, then the NaturalDate will have it's start date changed to Jan 1, and it's end date set to June 30.
     */
    const date    = 'date';
    const week    = 'week';
    const month   = 'month';
    const year    = 'year';
    const season  = 'season';
    const quarter = 'quarter';

    const SET_START_AND_END_DATES = 'SET_START_AND_END';
    const SET_START_DATE          = 'SET_START_DATE';
    const SET_END_DATE            = 'SET_END_DATE';

    /**
     * @var string $input The string that the user submitted. EX: "summer of 87"
     */
    protected $input;

    /**
     * @var string $timezoneId Ex: 'America/Denver'
     */
    protected $timezoneId;

    /**
     * @var string $languageCode Ex: 'en'
     */
    protected $languageCode;


    /**
     * @var Carbon $utcAnchorDate
     */
    protected $utcAnchorDate;

    /**
     * @var string $type EX: decade, year, month, day, hour, minute, second. I forget why I wanted this.
     */
    protected $type;

    /**
     * @var Carbon $localStart
     */
    protected $localStart;

    /**
     * @var Carbon $localEnd
     */
    protected $localEnd;


    /**
     * All local values. Used when passing into a PatternModifier's modify function. For example, if you pass a
     * NaturalDate object into the Year PatternModifier, I don't want to set the month or day if they have already been
     * set. This gives me a way to check if they are set or not before I overwrite them with default values.
     */
    protected $startYear;
    protected $startMonth;
    protected $startDay;
    protected $startHour;
    protected $startMinute;
    protected $startSecond;
    protected $endYear;
    protected $endMonth;
    protected $endDay;
    protected $endHour;
    protected $endMinute;
    protected $endSecond;


    // I think I will make all of these calculated fields.
    // Only need to store the local start and end times, and modify them on get()
    ///**
    // * @var Carbon $utcStart When the user does not supply an exact timestamp, this Carbon date serves
    // *      as a "bookend". The event represented by this NaturalDate instance did not happen before this value.
    // */
    //protected $utcStart;
    //
    ///**
    // * @var Carbon $utcEnd The other "bookend". The event did not happen after this value.
    // */
    //protected $utcEnd;
    //
    //
    //protected $spreadInSeconds;
    //
    ///**
    // * @var Carbon $utcBestDate (mid point anchor + confidence window / 2)
    // */
    //protected $utcBestDate;


    /**
     * @var \MichaelDrennen\NaturalDate\PatternMap
     */
    protected $patternMap;


    /**
     * @var string $matchedPatternLabel The label pointing to the PatternModifier used for this NaturalDate object.
     */
    protected $matchedPatternLabel;

    /**
     * @var \MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier $matchedPatternModifier
     */
    protected $matchedPatternModifier;

    /**
     * @var array The PatternModifiers has a list of all of the matched groups from preg_match.
     */
    protected $matchesArrayFromPregMatch = [];

    /**
     * @var array
     */
    protected $patternModifiers = [];

    /**
     * @NOTE Not sure I need this anymore
     * @var array It's possible that a PatternModifier can be triggered, but it doesn't have enough information to know
     *      how to modify the NaturalDate. For example: "early xmas 1979". In that case, "early" will get triggered
     *      first, but it doesn't know if the user meant early in the day or early in the year, etc. So place this
     *      NaturalDate object into an array and pass it forward. During the next iteration, I may have enough
     *      information to use that PatternModifier.
     */
    protected $unprocessedNaturalDates = [];

    /**
     * @NOTE Not sure I need this anymore
     * @var bool $processed Set to true if the modify() function was able to successfully modify this NaturalDate. This
     *      is related to the $unprocessedNaturalDates array.
     */
    protected $processed = false;

    /**
     * @var array
     */
    protected $debugMessages = [];


    /**
     * @param string                                  $string              Ex: 'Summer of 78'
     * @param string                                  $timezoneId          Ex: 'America/Denver'
     * @param string                                  $languageCode        Ex: 'en'
     * @param array                                   $patternModifiers    Keys are PatternMap labels, and values are
     *                                                                     PatternModifier objects. For instance, there
     *                                                                     is no way for the NaturalDate class to know
     *                                                                     when I was a freshman in high school. So the
     *                                                                     developer would pass in: ['my freshman year
     *                                                                     in high school' =>
     *                                                                     $naturalDateForHighSchoolFreshmanYearDates]
     * @param \MichaelDrennen\NaturalDate\NaturalDate $existingNaturalDate The idea is to break down each substring
     *                                                                     into a NaturalDate object. Then combine them
     *                                                                     together to create a single NaturalDate
     *                                                                     object that accounts for all of the
     *                                                                     modifiers.
     *
     * @return static
     * @throws \Exception;
     */
    public static function parse( string $string = '', string $timezoneId = 'UTC', string $languageCode = 'en', $patternModifiers = [], NaturalDate $existingNaturalDate = null ): NaturalDate {


        // Run the whole string through the patterns. I take the first pattern that matches.
        try {

            if ( isset( $existingNaturalDate ) ) {
                $naturalDate = $existingNaturalDate;
                $naturalDate->setTimezoneId( $timezoneId );
                $naturalDate->setInput( $string );
                $naturalDate->setLanguageCode( $languageCode );
                $naturalDate->addDebugMessage( "parse(), and an existing NaturalDate object was passed in." );
            } else {
                $naturalDate = new static( $string, $timezoneId, $languageCode, null, null, '', $patternModifiers );
                $naturalDate->addDebugMessage( "parse(), and NO NaturalDate object was passed in." );
            }
            //$naturalDate = new static( $string, $timezoneId, $languageCode, null, null, '', $patternModifiers );

            $naturalDate->setPatternModifiers( $patternModifiers );

            // Assign the pattern map that contains all of the pattern modifiers.
            $naturalDate->setPatternMap( $naturalDate->getLanguageCode() );
            $naturalDate->setMatchedPattern();
            $naturalDate->addDebugMessage( "    The matched pattern is: [" . get_class( $naturalDate->getMatchedPatternModifier() ) . "]." );

            $naturalDate = $naturalDate->modify();

            $naturalDate->setLocalDateTimes();

            return $naturalDate;
        } catch ( NoMatchingPatternFound $exception ) {
            $iAnchorTime = strtotime( $naturalDate->getInput() );

            // If the string that is passed in can be parsed by PHP's strtotime() function, then our job is done.
            if ( false !== $iAnchorTime ):
                $carbon = Carbon::createFromTimestamp( $iAnchorTime, $naturalDate->getTimezoneId() );

                $naturalDate = new static( $string, $timezoneId, $languageCode, $carbon, $carbon, NaturalDate::date, $patternModifiers );
                $naturalDate->setLocalDateTimes();

                return $naturalDate;
            endif;
        } catch ( \Exception $exception ) {
            throw $exception;
        }

        throw new UnparsableString( "Unable to parse the date: [" . $string . "]" );
    }


    public function __toString() {
        $string = '';

        $string        .= "\n\n\nNATURAL DATE OBJECT";
        $string        .= "\ninput:        " . $this->getInput();
        $string        .= "\ntimezoneId:   " . $this->getTimezoneId();
        $string        .= "\nlanguageCode: " . $this->getLanguageCode();
        $string        .= "\nlocalStart:   " . $this->localStart->toDateTimeString();
        $string        .= "\nlocalEnd:     " . $this->localEnd->toDateTimeString();
        $string        .= "\ndebugMessages:";
        $debugMessages = $this->getDebugMessages();
        foreach ( $debugMessages as $i => $message ):
            $string .= "\n $i: " . $message;
        endforeach;
        $string .= "\nEND OF NATURAL DATE OBJECT\n\n\n";
        return $string;
    }


    public function addDebugMessage( string $message ) {
        $this->debugMessages[] = "(" . $this->getInput() . ")" . $message;
    }

    public function getDebugMessages(): array {
        return $this->debugMessages;
    }

    public function setLocalDateTimes() {
        $this->setLocalStart();
        $this->setLocalEnd();
    }

    /**
     * @param string $languageCode Ex: "en" for english
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidLanguageCode
     */
    protected function setPatternMap( string $languageCode ) {
        switch ( $languageCode ):
            case 'en':
                $this->patternMap = new Languages\En\PatternMapForLanguage( $this->patternModifiers );
                break;
            default:
                throw new InvalidLanguageCode( "The Pattern class for language code of [$languageCode] has not been coded yet." );
        endswitch;
    }

    /**
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound
     */
    protected function setMatchedPattern() {
        $input                        = $this->getInput();
        $this->matchedPatternModifier = $this->patternMap->setMatchedPattern( $input );
        $this->addDebugMessage( "Inside NaturalDate->setMatchedPattern(): String is [" . $input . "]" );
        $this->addDebugMessage( "    still inside NaturalDate->setMatchedPattern(): matchedPatternModifier is [" . get_class( $this->matchedPatternModifier ) . "]" );
    }

    /**
     * Used solely for debugging right now.
     *
     * @return \MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier
     */
    public function getMatchedPatternModifier(): PatternModifier {
        return $this->matchedPatternModifier;
    }

    protected function setPatternModifiers( array $patternModifiers ) {
        $this->patternModifiers = $patternModifiers;
    }

    public function getPatternModifiers(): array {
        return $this->patternModifiers;
    }

    /**
     * @note The first element of the $matches array is the full string, which I never need. So I shift it off.
     * @link http://php.net/manual/en/function.preg-match.php
     * @return array
     */
    public function getPregMatchMatches() {
        $matches = $this->matchedPatternModifier->getMatches();
        array_shift( $matches );
        $matches = array_map( 'trim', $matches );
        $matches = array_filter( $matches );
        $matches = array_values( $matches ); // reset indexes to zero based
        return $matches;
    }


    /**
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     * @throws \Exception
     */
    protected function modify(): NaturalDate {
        return $this->matchedPatternModifier->modify( $this );
    }


    /**
     * @param mixed $input
     */
    public function setInput( $input ) {
        $this->input = trim( $input );
    }

    /**
     * @param string $timezoneId Ex: 'America/Denver'
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     * @link http://php.net/manual/en/datetimezone.listabbreviations.php
     */
    public function setTimezoneId( string $timezoneId = null ) {
        // Validate Timezone Id;
        $timeZones = DateTimeZone::listIdentifiers();
        if ( false === in_array( $timezoneId, $timeZones ) ) {
            throw new InvalidTimezone( "The timezone id you passed in [" . $timezoneId . "] is not valid because it is not found in the array returned by DateTimeZone::listIdentifiers()" );
        }
        date_default_timezone_set( $timezoneId );
        $this->timezoneId = $timezoneId;
    }

    /**
     * @param string $languageCode EX: "en"
     */
    public function setLanguageCode( string $languageCode = null ) {
        $this->languageCode = $languageCode;
    }

    /**
     * @param mixed $utcAnchorDate
     */
    public function setUtcAnchorDate( $utcAnchorDate ) {
        $this->utcAnchorDate = $utcAnchorDate;
    }

    public function setLocalStart( Carbon $localStart = null ) {
        if ( is_null( $localStart ) ) {
            $this->addDebugMessage( "Inside setLocalStart(): No Carbon date was passed in so going to set localStart to " . $this->getStartDate() );
            $this->localStart = $this->getStartDate();
        } else {
            $this->localStart = $localStart;
        }

    }

    public function setLocalEnd( Carbon $localEnd = null ) {
        if ( is_null( $localEnd ) ) {
            $this->addDebugMessage( "Inside setLocalEnd(): No Carbon date was passed in so going to set localEnd to " . $this->getEndDate() );
            $this->localEnd = $this->getEndDate();
        } else {
            $this->localEnd = $localEnd;
        }

    }

    public function getLocalStart(): Carbon {
        return $this->localStart;
    }

    public function getLocalEnd(): Carbon {
        return $this->localEnd;
    }

    /**
     * @param Carbon $utcStart
     * * @param string $timezoneId Ex: America/Denver
     */
    //public function setUtcStart( Carbon $utcStart = null, string $timezoneId ) {
    //    $this->utcStart = $utcStart;
    //}

    /**
     * @param Carbon $utcEnd
     * @param string $timezoneId Ex: America/Denver
     */
    //public function setUtcEnd( Carbon $utcEnd = null, string $timezoneId ) {
    //    $this->utcEnd = $utcEnd;
    //}

    /**
     * @param mixed $spreadInSeconds
     */
    //public function setSpreadInSeconds( $spreadInSeconds ) {
    //    $this->spreadInSeconds = $spreadInSeconds;
    //}


    /**
     * @param mixed $utcBestDate
     */
    //public function setUtcBestDate( $utcBestDate ) {
    //    $this->utcBestDate = $utcBestDate;
    //}

    /**
     * @param string $type See the constants above like 'year' for valid types.
     *
     * @throws \Exception
     */
    public function setType( string $type = '' ) {

        if ( isset( $this->type ) ):
            $orderOfSpecificity = [
                NaturalDate::year,
                NaturalDate::season,
                NaturalDate::quarter,
                NaturalDate::month,
                NaturalDate::week,
                NaturalDate::date,
            ];

            $keyOfNewType = array_search( $type, $orderOfSpecificity );

            if ( false === $keyOfNewType ):
                throw new \Exception( "You are trying to set the 'type' of NaturalDate to " . $type . " which has not been coded for." );
            endif;

            $keyOfExistingType = array_search( $this->type, $orderOfSpecificity );

            if ( $keyOfNewType > $keyOfExistingType ):
                $this->type = $type;
                $this->addDebugMessage( "Type of NaturalDate set to " . $type );
            elseif ( $keyOfNewType == $keyOfExistingType ):
                $this->addDebugMessage( "Type of NaturalDate is equal to existing type of " . $type );
            elseif ( $keyOfNewType < $keyOfExistingType ):
                $this->addDebugMessage( "Type of NaturalDate is is more specific than  [" . $type . "] so type will remain unchanged." );
            endif;
        else:
            $this->type = $type;
        endif;
    }

    /**
     * @return mixed
     */
    public function getInput() {
        return $this->input;
    }


    /**
     * @return string Ex: 'America/Denver'
     */
    public function getTimezoneId() {
        return $this->timezoneId;
    }


    /**
     * @return string EX: "en"
     */
    public function getLanguageCode(): string {
        return $this->languageCode;
    }


    /**
     * @return mixed
     */
    public function getUtcAnchorDate() {
        return $this->utcAnchorDate;
    }


    /**
     * @return \Carbon\Carbon
     */
    public function getUtcStart(): Carbon {
        $this->addDebugMessage( "Inside getUtcStart(): " . $this->localStart );
        return $this->localStart->setTimezone( 'UTC' );
    }


    /**
     * @return \Carbon\Carbon
     */
    public function getUtcEnd(): Carbon {
        return $this->localEnd->setTimezone( 'UTC' );
    }


    /**
     * @return mixed
     */
    //public function getSpreadInSeconds() {
    //    return $this->spreadInSeconds;
    //}


    /**
     * @return mixed
     */
    //public function getUtcBestDate() {
    //    return $this->utcBestDate;
    //}

    public function getType(): string {
        return $this->type;
    }

    /**
     * NaturalDate constructor.
     *
     * @param string         $input        Ex: early 2016
     * @param string         $timezoneId   Ex: America\Denver
     * @param string         $languageCode Ex: en
     * @param \Carbon\Carbon $localStartDateTime
     * @param \Carbon\Carbon $localEndDateTime
     * @param string         $type
     * @param array          $patternModifiers
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     * @throws \Exception
     */
    public function __construct(
        string $input = '',
        string $timezoneId = '',
        string $languageCode = '',
        Carbon $localStartDateTime = null,
        Carbon $localEndDateTime = null,
        string $type = null,
        array $patternModifiers = [] ) {
        $this->setInput( $input );
        $this->setTimezoneId( $timezoneId );
        $this->setLanguageCode( $languageCode );
        //$this->setLocalStart( $localStartDateTime );
        //$this->setLocalEnd( $localEndDateTime );
        //$this->setType( $type );
        //$this->setPatternModifiers( $patternModifiers );
    }

    public function setStartYear( string $year ) {

        $this->startYear = $year;
    }

    public function setStartMonth( string $month ) {
        $this->startMonth = str_pad( $month, 2, '0', STR_PAD_LEFT );
    }

    public function setStartDay( string $day ) {
        $this->startDay = str_pad( $day, 2, '0', STR_PAD_LEFT );
    }

    public function setStartHour( string $hour ) {
        $this->startHour = str_pad( $hour, 2, '0', STR_PAD_LEFT );
    }

    public function setStartMinute( string $minute ) {
        $this->startMinute = str_pad( $minute, 2, '0', STR_PAD_LEFT );
    }

    public function setStartSecond( string $second ) {
        $this->startSecond = str_pad( $second, 2, '0', STR_PAD_LEFT );;
    }

    public function setEndYear( string $year ) {
        $this->endYear = $year;
    }

    public function setEndMonth( string $month ) {
        $this->endMonth = str_pad( $month, 2, '0', STR_PAD_LEFT );
    }

    public function setEndDay( string $day ) {
        $this->endDay = str_pad( $day, 2, '0', STR_PAD_LEFT );
    }

    public function setEndHour( string $hour ) {
        $this->endHour = str_pad( $hour, 2, '0', STR_PAD_LEFT );
    }

    public function setEndMinute( string $minute ) {
        $this->endMinute = str_pad( $minute, 2, '0', STR_PAD_LEFT );
    }

    public function setEndSecond( string $second ) {
        $this->endSecond = str_pad( $second, 2, '0', STR_PAD_LEFT );
    }

    public function setStartDateTimeAsStartOfToday() {
        $this->setStartYear( date( 'Y' ) );
        $this->setStartMonth( date( 'm' ) );
        $this->setStartDay( date( 'd' ) );
        $this->setStartHour( 0 );
        $this->setStartMinute( 0 );
        $this->setStartSecond( 0 );
    }

    public function setEndDateTimeAsEndOfToday() {
        $this->setEndYear( date( 'Y' ) );
        $this->setEndMonth( date( 'm' ) );
        $this->setEndDay( date( 'd' ) );
        $this->setEndHour( 23 );
        $this->setEndMinute( 59 );
        $this->setEndSecond( 59 );
    }

    /**
     * @param bool $override If times were already set, do you want to override them with zeros?
     */
    public function setStartTimesAsStartOfDay( bool $override = false ) {
        if ( $override ):
            $this->setStartHour( 0 );
            $this->setStartMinute( 0 );
            $this->setStartSecond( 0 );
        else:
            if ( ! isset( $this->startHour ) ):
                $this->setStartHour( 0 );
            endif;

            if ( ! isset( $this->startMinute ) ):
                $this->setStartMinute( 0 );
            endif;

            if ( ! isset( $this->startSecond ) ):
                $this->setStartSecond( 0 );
            endif;
        endif;

    }

    public function setEndTimesAsEndOfDay( bool $override = false ) {
        if ( $override ):
            $this->setEndHour( 23 );
            $this->setEndMinute( 59 );
            $this->setEndSecond( 59 );
        else:
            if ( ! isset( $this->endHour ) ):
                $this->setEndHour( 23 );
            endif;

            if ( ! isset( $this->endMinute ) ):
                $this->setEndMinute( 59 );
            endif;

            if ( ! isset( $this->endSecond ) ):
                $this->setEndSecond( 59 );
            endif;
        endif;
    }


    // GETTERS
    public function getStartYear() {
        return $this->startYear;
    }

    public function getStartMonth() {
        return $this->startMonth;
    }

    public function getStartDay() {
        return $this->startDay;
    }

    public function getStartHour() {
        return $this->startHour;
    }

    public function getStartMinute() {
        return $this->startMinute;
    }

    public function getStartSecond() {
        return $this->startSecond;
    }

    public function getEndYear() {
        return $this->endYear;
    }

    public function getEndMonth() {
        return $this->endMonth;
    }

    public function getEndDay() {
        return $this->endDay;
    }

    public function getEndHour() {
        return $this->endHour;
    }

    public function getEndMinute() {
        return $this->endMinute;
    }

    public function getEndSecond() {
        return $this->endSecond;
    }

    /**
     * Returns a string of all the local start datetime values, or defaults if elements aren't set.
     *
     * @return Carbon
     */
    public function getStartDate(): Carbon {
        $year   = $this->getStartYear() ? $this->getStartYear() : '0000';
        $month  = $this->getStartMonth() ? $this->getStartMonth() : '01';
        $day    = $this->getStartDay() ? $this->getStartDay() : '01';
        $hour   = $this->getStartHour() ? $this->getStartHour() : '00';
        $minute = $this->getStartMinute() ? $this->getStartMinute() : '00';
        $second = $this->getStartSecond() ? $this->getStartSecond() : '00';

        $string = $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute . ':' . $second;
        return Carbon::parse( $string, $this->getTimezoneId() );


    }

    /**
     * Returns a Carbon of local end datetime, or defaults if elements aren't set.
     *
     * @return Carbon
     */
    public function getEndDate(): Carbon {
        $year   = $this->getEndYear() ? $this->getEndYear() : '0000';
        $month  = $this->getEndMonth() ? $this->getEndMonth() : '01';
        $day    = $this->getEndDay() ? $this->getEndDay() : '01';
        $hour   = $this->getEndHour() ? $this->getEndHour() : '23';
        $minute = $this->getEndMinute() ? $this->getEndMinute() : '59';
        $second = $this->getEndSecond() ? $this->getEndSecond() : '59';

        $string = $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute . ':' . $second;
        return Carbon::parse( $string, $this->getTimezoneId() );
    }

    //public function pushUnprocessedNaturalDate( NaturalDate $naturalDate ) {
    //    array_push( $this->unprocessedNaturalDates, $naturalDate );
    //}
    //
    //public function popUnprocessedNaturalDate(): NaturalDate {
    //    return array_pop( $this->unprocessedNaturalDates );
    //}
    //
    //public function getUnprocessedNaturalDates(): array {
    //    return $this->unprocessedNaturalDates;
    //}
    //
    //public function setUnprocessedNaturalDates( $unprocessedNaturalDates ) {
    //    $this->unprocessedNaturalDates = $unprocessedNaturalDates;
    //}
    //
    //public function setProcessed( bool $processed ) {
    //    $this->processed = $processed;
    //}
    //
    //public function getProcessed(): bool {
    //    return $this->processed;
    //}
}