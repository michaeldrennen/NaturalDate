<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidLanguageCode;
use MichaelDrennen\NaturalDate\Exceptions\InvalidNaturalDateType;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound;
use MichaelDrennen\NaturalDate\Exceptions\UnparsableString;


class NaturalDate {

    /**
     * These are the different "types" of NaturalDate objects. The type is used by the PatternModifiers, so they can
     * know exactly how the date should be modified. For example if the "type" is year, and the "Early" PatternModifier
     * is used, then the NaturalDate will have it's start date changed to Jan 1, and it's end date set to June 30.
     */
    const datetime     = 'datetime'; // All the way out to the seconds position.
    const date         = 'date';     // Just the year, month, and day
    const yearlessDate = 'yearlessDate'; // For holidays, but the user didn't give the year.
    const week         = 'week';
    const month        = 'month';
    const year         = 'year';
    const season       = 'season';
    const quarter      = 'quarter';
    const range        = 'range'; // A custom range between two NaturalDate objects of above ^ types.


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
     * @var string $type EX: decade, year, month, day, hour, minute, second. I forget why I wanted this.
     */
    protected $type;


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
     * @var array
     */
    protected $patternModifiers = [];

    /**
     * @var array
     */
    protected $debugMessages = [];


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
    public function __construct( string $input = '', string $timezoneId = '', string $languageCode = '', Carbon $localStartDateTime = null, Carbon $localEndDateTime = null, string $type = null, array $patternModifiers = [] ) {
        $this->setInput( $input );
        $this->setTimezoneId( $timezoneId );
        $this->setLanguageCode( $languageCode );
        $this->setLocalStartDateTime( $localStartDateTime );
        $this->setLocalEndDateTime( $localEndDateTime );

        // This will be set if the NaturalDate object is created "manually" from the output of date_parse()
        if ( isset( $type ) ):
            $this->setType( $type );
        endif;
    }

    /**
     * @return string
     */
    public function __toString() {
        $string = '';

        $string .= "\n\n\nNATURAL DATE OBJECT";
        $string .= "\ninput:        " . $this->getInput();
        $string .= "\ntimezoneId:   " . $this->getTimezoneId();
        $string .= "\nlanguageCode: " . $this->getLanguageCode();
        $string .= "\nlocal start:  " . $this->getLocalStart();
        $string .= "\nlocal end:    " . $this->getLocalEnd();
        $string .= "\ntype:         " . $this->getType();

        $string        .= "\n\ndebugMessages:";
        $debugMessages = $this->getDebugMessages();
        foreach ( $debugMessages as $i => $entry ):
            $string .= "\n $i: " . implode( "\t", $entry );
        endforeach;
        $string .= "\nEND OF NATURAL DATE OBJECT\n\n\n";
        return $string;
    }

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
     * @param bool                                    $cleanOutput         Set to false if you want all of the
     *                                                                     debugging messages returned with the
     *                                                                     NaturalDate object.
     *
     * @return static
     * @throws \Exception;
     */
    public static function parse( string $string = '', string $timezoneId = 'UTC', string $languageCode = 'en', $patternModifiers = [], NaturalDate $existingNaturalDate = null, bool $cleanOutput = true ): NaturalDate {

        // Run the whole string through the patterns. I take the first pattern that matches.
        try {

            if ( isset( $existingNaturalDate ) ) {
                $naturalDate = $existingNaturalDate;
                $naturalDate->setTimezoneId( $timezoneId );
                $naturalDate->setInput( $string );
                $naturalDate->setLanguageCode( $languageCode );
                $naturalDate->addDebugMessage( "Existing NaturalDate object was passed in.", __FUNCTION__, __CLASS__ );
            } else {
                $naturalDate = new static( $string, $timezoneId, $languageCode, null, null, null, $patternModifiers );
                $naturalDate->addDebugMessage( "NO NaturalDate object was passed in.", __FUNCTION__, __CLASS__ );
            }

            $naturalDate->setPatternModifiers( $patternModifiers );

            // Assign the pattern map that contains all of the pattern modifiers.
            $naturalDate->setPatternMap( $naturalDate->getLanguageCode() );
            $naturalDate->setMatchedPattern();

            $naturalDate = $naturalDate->modify();

            return self::cleanOutput( $naturalDate, $cleanOutput );
        } catch ( NoMatchingPatternFound $exception ) {
            /**
             * By now, none of the NaturalDate patterns have been matched. Let's give strtotime() a chance.
             * I do this after checking NaturalDate patterns, because some NaturalDate patterns *can* be parsed by strtotime, but they don't get parsed correctly.
             */
            $parsedParts = date_parse( $naturalDate->getInput() );

            if ( $naturalDate->dateParseYieldsDate( $parsedParts ) ):
                $naturalDate->addDebugMessage( "    date_parse yielded a date. No time." );
                $carbon      = Carbon::create( $parsedParts[ 'year' ], $parsedParts[ 'month' ], $parsedParts[ 'day' ], '00', '00', '00', $timezoneId );
                $naturalDate = new static( $string, $timezoneId, $languageCode, $carbon, $carbon, NaturalDate::date, $patternModifiers );
            elseif ( $naturalDate->dateParseYieldsDateTime( $parsedParts ) ):
                $naturalDate->addDebugMessage( "    date_parse yielded a datetime." );
                $carbon      = Carbon::create( $parsedParts[ 'year' ], $parsedParts[ 'month' ], $parsedParts[ 'day' ], (int)$parsedParts[ 'hour' ], (int)$parsedParts[ 'minute' ], (int)$parsedParts[ 'second' ], $timezoneId );
                $naturalDate = new static( $string, $timezoneId, $languageCode, $carbon, $carbon, NaturalDate::datetime, $patternModifiers );
            else:
                throw new UnparsableString( "Unable to parse the date: [" . $string . "]" );
            endif;

            return self::cleanOutput( $naturalDate, $cleanOutput );

        } catch ( NaturalDateException $exception ) {
            throw $exception;
        } catch ( \Exception $exception ) {
            $debugMessages = isset( $naturalDate ) ? $naturalDate->getDebugMessages() : [];
            throw new NaturalDateException( $exception->getMessage(), $exception->getCode(), $exception, $debugMessages );
        }
    }

    protected static function cleanOutput( NaturalDate $naturalDate, bool $cleanOutput = false ): NaturalDate {
        if ( $cleanOutput ):
            /**
             * These 4 fields are dynamically declared, so users of this class have clear and easy access to the
             * bookend dates created by NaturalDate.
             */
            $naturalDate->utcStart   = $naturalDate->getUtcStart();
            $naturalDate->utcEnd     = $naturalDate->getUtcEnd();
            $naturalDate->localStart = $naturalDate->getLocalStart();
            $naturalDate->localEnd   = $naturalDate->getLocalEnd();

            /**
             * These fields are not needed by the end user of this class.
             */
            unset( $naturalDate->patternMap );
            unset( $naturalDate->matchedPatternLabel );
            unset( $naturalDate->matchedPatternModifier );
            unset( $naturalDate->patternModifiers );
            unset( $naturalDate->debugMessages );
            //unset( $naturalDate->startYear );
            //unset( $naturalDate->startMonth );
            //unset( $naturalDate->startDay );
            //unset( $naturalDate->startHour );
            //unset( $naturalDate->startMinute );
            //unset( $naturalDate->startSecond );
            //unset( $naturalDate->endYear );
            //unset( $naturalDate->endMonth );
            //unset( $naturalDate->endMinute );
            //unset( $naturalDate->endHour );
            //unset( $naturalDate->endMinute );
            //unset( $naturalDate->endSecond );
        endif;
        return $naturalDate;
    }


    /**
     * Examine the output from PHP's date_parse() function, and return true if only date elements are returned. Not
     * time elements.
     *
     * @link http://php.net/manual/en/function.date-parse.php
     *
     * @param array $parts The output from date_parse()
     *
     * @return bool
     */
    protected function dateParseYieldsDate( array $parts ) {

        if (
            ! empty( $parts[ 'year' ] ) &&
            ! empty( $parts[ 'month' ] ) &&
            ! empty( $parts[ 'day' ] ) &&
            false === $parts[ 'hour' ] &&
            false === $parts[ 'minute' ] &&
            false === $parts[ 'second' ]
        ):
            return true;
        endif;
        return false;
    }

    /**
     * Examine the output from PHP's date_parse() function, and return true if both date and time elements are returned.
     *
     * @link http://php.net/manual/en/function.date-parse.php
     *
     * @param array $parts The output from date_parse()
     *
     * @return bool
     */
    protected function dateParseYieldsDateTime( array $parts ) {
        if (
            ! empty( $parts[ 'year' ] ) &&
            ! empty( $parts[ 'month' ] ) &&
            ! empty( $parts[ 'day' ] ) &&
            false !== $parts[ 'hour' ] &&
            false !== $parts[ 'minute' ] &&
            false !== $parts[ 'second' ]
        ):
            return true;
        endif;
        return false;
    }


    /**
     * I added this function to aid in development and debugging.
     *
     * @param string $message
     * @param string $function __FUNCTION__
     * @param string $class    __CLASS__
     */
    public function addDebugMessage( string $message, string $function = null, string $class = null ) {
        $this->debugMessages[] = [
            'input'    => $this->getInput(),
            'class'    => $class,
            'function' => $function,
            'message'  => $message,
        ];
    }

    /**
     * @return array
     */
    public function getDebugMessages(): array {
        if ( isset( $this->debugMessages ) ):
            return $this->debugMessages;
        else:
            return [];
        endif;
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
        $this->addDebugMessage( "matchedPatternModifier is [" . get_class( $this->matchedPatternModifier ) . "]", __FUNCTION__, __CLASS__ );
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



    //public function getLocalStart(): Carbon {
    //    return $this->localStart;
    //}

    //public function getLocalEnd(): Carbon {
    //    return $this->localEnd;
    //}


    /**
     * @param string $type See the constants above like 'year' for valid types.
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NaturalDateException
     */
    public function setType( string $type = null ) {
        $this->addDebugMessage( "Just entered.", __FUNCTION__, __CLASS__ );

        if ( is_null( $type ) ):
            throw new InvalidNaturalDateType( "You are trying to set the type to null, and that isn't allowed. It starts null. It doesn't go back to null." );
        endif;


        $orderOfSpecificity = [
            NaturalDate::year,
            NaturalDate::season,
            NaturalDate::quarter,
            NaturalDate::month,
            NaturalDate::week,
            NaturalDate::date,
            NaturalDate::yearlessDate,
            NaturalDate::datetime,
            NaturalDate::range,
        ];

        $keyOfNewType = array_search( $type, $orderOfSpecificity );

        if ( false === $keyOfNewType ):
            throw new InvalidNaturalDateType( "You are trying to set the 'type' of NaturalDate to [" . $type . "] which has not been coded for." );
        endif;

        $keyOfExistingType = array_search( $this->type, $orderOfSpecificity );

        if ( false === $keyOfExistingType ):
            $this->type = $type;
            $this->addDebugMessage( "Type of NaturalDate was not set before, so setting it to [" . $this->getType() . "]", __FUNCTION__, __CLASS__ );
        elseif ( $keyOfNewType > $keyOfExistingType ):
            $this->type = $type;
            $this->addDebugMessage( "Type of NaturalDate set to [" . $this->getType() . "]", __FUNCTION__, __CLASS__ );
        elseif ( $keyOfNewType == $keyOfExistingType ):
            $this->addDebugMessage( "Type of NaturalDate is equal to existing type of [" . $this->type . "]", __FUNCTION__, __CLASS__ );
        elseif ( $keyOfNewType < $keyOfExistingType ):
            $this->addDebugMessage( "Type of NaturalDate is is more specific than  [" . $this->type . "] so type will remain unchanged.", __FUNCTION__, __CLASS__ );
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
     * Public accessor to the UTC converted local Carbon start date.
     *
     * @return \Carbon\Carbon
     */
    public function getUtcStart(): Carbon {
        return $this->getLocalStart()->setTimezone( 'UTC' );
    }


    /**
     * Public accessor to the UTC converted local Carbon end date.
     *
     * @return \Carbon\Carbon
     */
    public function getUtcEnd(): Carbon {
        return $this->getLocalEnd()->setTimezone( 'UTC' );
    }


    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }


    /**
     * Used in the constructor when the parse() function is able to find a date using date_parse();
     *
     * @see \MichaelDrennen\NaturalDate\NaturalDate::parse()
     *
     * @param \Carbon\Carbon|null $start
     */
    protected function setLocalStartDateTime( Carbon $start = null ) {
        if ( is_null( $start ) ):
            return;
        endif;

        $this->setStartYear( $start->year );
        $this->setStartMonth( $start->month );
        $this->setStartDay( $start->day );
        $this->setStartHour( $start->hour );
        $this->setStartMinute( $start->minute );
        $this->setStartSecond( $start->second );
    }

    /**
     * Used in the constructor when the parse() function is able to find a date using date_parse();
     *
     * @see \MichaelDrennen\NaturalDate\NaturalDate::parse()
     *
     * @param \Carbon\Carbon|null $end
     */
    protected function setLocalEndDateTime( Carbon $end = null ) {
        if ( is_null( $end ) ):
            return;
        endif;
        $this->setEndYear( $end->year );
        $this->setEndMonth( $end->month );
        $this->setEndDay( $end->day );
        $this->setEndHour( $end->hour );
        $this->setEndMinute( $end->minute );
        $this->setEndSecond( $end->second );
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
        $this->startSecond = str_pad( $second, 2, '0', STR_PAD_LEFT );
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
        $this->setLocalEndDateTime();
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
     * use to set the start hour:min:sec to 00:00:00 (at the beginning of the day). By default, it will not overwrite
     * any values that are set in startHour, startMinute, or startSecond.
     *
     * @param bool $override Set to true if you want to ignore any existing values and overwrite with 00:00:00
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

    /**
     * Useful if you want to set the hour:min:sec to 23:59:59 (at the end of the day). By default, it will not overwrite
     * any values that are set in endHour, endMinute, or endSecond.
     *
     * @param bool $override Set to true if you want to ignore any existing values and overwrite with 23:59:59
     */
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


    /**
     * @return string|null
     */
    public function getStartYear() {
        return $this->startYear;
    }

    /**
     * @return string|null
     */
    public function getStartMonth() {
        return $this->startMonth;
    }

    /**
     * @return string|null
     */
    public function getStartDay() {
        return $this->startDay;
    }

    /**
     * @return string|null
     */
    public function getStartHour() {
        return $this->startHour;
    }

    /**
     * @return string|null
     */
    public function getStartMinute() {
        return $this->startMinute;
    }

    /**
     * @return string|null
     */
    public function getStartSecond() {
        return $this->startSecond;
    }

    /**
     * @return string|null
     */
    public function getEndYear() {
        return $this->endYear;
    }

    /**
     * @return string|null
     */
    public function getEndMonth() {
        return $this->endMonth;
    }

    /**
     * @return string|null
     */
    public function getEndDay() {
        return $this->endDay;
    }

    /**
     * @return string|null
     */
    public function getEndHour() {
        return $this->endHour;
    }

    /**
     * @return string|null
     */
    public function getEndMinute() {
        return $this->endMinute;
    }

    /**
     * @return string|null
     */
    public function getEndSecond() {
        return $this->endSecond;
    }

    /**
     * Returns a string of all the local start datetime values, or defaults if elements aren't set.
     *
     * @return Carbon
     */
    public function getLocalStart(): Carbon {
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
    public function getLocalEnd(): Carbon {
        $year   = $this->getEndYear() ? $this->getEndYear() : '0000';
        $month  = $this->getEndMonth() ? $this->getEndMonth() : '01';
        $day    = $this->getEndDay() ? $this->getEndDay() : '01';
        $hour   = $this->getEndHour() ? $this->getEndHour() : '23';
        $minute = $this->getEndMinute() ? $this->getEndMinute() : '59';
        $second = $this->getEndSecond() ? $this->getEndSecond() : '59';

        $string = $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute . ':' . $second;

        return Carbon::parse( $string, $this->getTimezoneId() );
    }
}