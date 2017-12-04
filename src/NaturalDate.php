<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\NaturalDateException;
use MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound;


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
     * @var Carbon $utcStart When the user does not supply an exact timestamp, this Carbon date serves
     *      as a "bookend". The event represented by this NaturalDate instance did not happen before this value.
     */
    protected $utcStart;

    /**
     * @var Carbon $utcEnd The other "bookend". The event did not happen after this value.
     */
    protected $utcEnd;


    protected $spreadInSeconds;

    /**
     * @var Carbon $utcBestDate (mid point anchor + confidence window / 2)
     */
    protected $utcBestDate;


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

    protected $patternModifiers = [];

    /**
     * @param string $string                 Ex: 'Summer of 78'
     * @param string $timezoneId             Ex: 'America/Denver'
     * @param string $languageCode           Ex: 'en'
     * @param array  $patternModifiers       Keys are PatternMap labels, and values are PatternModifier objects. For
     *                                       instance, there is no way for the NaturalDate class to know when I was a
     *                                       freshman in high school. So the developer would pass in: ['my freshman
     *                                       year in high school' => $naturalDateForHighSchoolFreshmanYearDates]
     *
     * @return static
     * @throws \Exception;
     */
    public static function parse( string $string = '', string $timezoneId = 'UTC', string $languageCode = 'en', $patternModifiers = [] ): NaturalDate {
        date_default_timezone_set( $timezoneId );


        // Run the whole string through the patterns. I take the first pattern that matches.
        try {
            $date = new static( $string, $timezoneId, $languageCode, null, null, '', $patternModifiers );
            // Assign the pattern map that contains all of the pattern modifiers.
            $date->setPatternMap( $date->getLanguageCode() );
            $date->setMatchedPattern();

            return $date->modify();
        } catch ( NoMatchingPatternFound $exception ) {
            $iAnchorTime = strtotime( $date->getInput() );

            // If the string that is passed in can be parsed by PHP's strtotime() function, then our job is done.
            if ( false !== $iAnchorTime ):
                $carbon = Carbon::createFromTimestamp( $iAnchorTime, $date->getTimezoneId() );
                $carbon->setTimezone( 'UTC' );

                return new static( $string, $timezoneId, $languageCode, $carbon, $carbon, NaturalDate::date, $patternModifiers );
            endif;
        } catch ( \Exception $exception ) {
            throw $exception;
        }

        throw new NaturalDateException( "Unable to parse the date: " . $string );
    }

    /**
     * @param string $languageCode
     *
     * @throws \Exception
     */
    protected function setPatternMap( string $languageCode ) {
        switch ( $languageCode ):
            case 'en':
                $this->patternMap = new \MichaelDrennen\NaturalDate\Languages\En\PatternMapForLanguage( $this->patternModifiers );
                break;
            default:
                throw new \Exception( "The Pattern class for language code of [$languageCode] has not been coded yet." );
        endswitch;
    }

    /**
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound
     */
    protected function setMatchedPattern() {
        $input                        = $this->getInput();
        $this->matchedPatternModifier = $this->patternMap->setMatchedPattern( $input );
    }

    protected function setPatternModifiers( array $patternModifiers ) {
        $this->patternModifiers = $patternModifiers;
    }

    public function getPatternModifiers(): array {
        return $this->patternModifiers;
    }

    /**
     * @link http://php.net/manual/en/function.preg-match.php
     * @return array
     */
    public function getPregMatchMatches() {
        $matches = $this->matchedPatternModifier->getMatches();
        array_shift( $matches );
        return $matches;
    }


    protected function modify(): NaturalDate {
        return $this->matchedPatternModifier->modify( $this );
    }


    /**
     * @param mixed $input
     */
    public function setInput( $input ) {
        $this->input = $input;
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


    /**
     * @param Carbon $utcStart
     */
    public function setUtcStart( Carbon $utcStart = null ) {
        $this->utcStart = $utcStart;
    }

    /**
     * @param Carbon $utcEnd
     */
    public function setUtcEnd( Carbon $utcEnd = null ) {
        $this->utcEnd = $utcEnd;
    }

    /**
     * @param mixed $spreadInSeconds
     */
    public function setSpreadInSeconds( $spreadInSeconds ) {
        $this->spreadInSeconds = $spreadInSeconds;
    }


    /**
     * @param mixed $utcBestDate
     */
    public function setUtcBestDate( $utcBestDate ) {
        $this->utcBestDate = $utcBestDate;
    }

    /**
     * @param string $type See the constants like year for valid types.
     */
    public function setType( string $type = '' ) {
        $this->type = $type;
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
        return $this->utcStart;
    }


    /**
     * @return \Carbon\Carbon
     */
    public function getUtcEnd(): Carbon {
        return $this->utcEnd;
    }


    /**
     * @return mixed
     */
    public function getSpreadInSeconds() {
        return $this->spreadInSeconds;
    }


    /**
     * @return mixed
     */
    public function getUtcBestDate() {
        return $this->utcBestDate;
    }

    public function getType(): string {
        return $this->type;
    }

    /**
     * NaturalDate constructor.
     *
     * @param string         $input        Ex: early 2016
     * @param string         $timezoneId   Ex: America\Denver
     * @param string         $languageCode Ex: en
     * @param \Carbon\Carbon $startDateTime
     * @param \Carbon\Carbon $endDateTime
     * @param string         $type
     * @param array          $patternModifiers
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     */
    public function __construct( string $input = '', string $timezoneId = '', string $languageCode = '', Carbon $startDateTime = null, Carbon $endDateTime = null, string $type = '', array $patternModifiers = [] ) {
        $this->setInput( $input );
        $this->setTimezoneId( $timezoneId );
        $this->setLanguageCode( $languageCode );
        $this->setUtcStart( $startDateTime );
        $this->setUtcEnd( $endDateTime );
        $this->setType( $type );
        $this->setPatternModifiers( $patternModifiers );
    }


}