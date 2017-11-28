<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;

class NaturalDate {

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


    protected $countryCode; // Not sure I need this...?

    /**
     * @var Carbon $utcAnchorDate
     */
    protected $utcAnchorDate;


    /**
     * @var Carbon $utcConfidenceWindowStart When the user does not supply an exact timestamp, this Carbon date serves
     *      as a "bookend". The event represented by this NaturalDate instance did not happen before this value.
     */
    protected $utcConfidenceWindowStart;

    /**
     * @var Carbon $utcConfidenceWindowEnd The other "bookend". The event did not happen after this value.
     */
    protected $utcConfidenceWindowEnd;

    /**
     * @var string $confidenceWindowString
     */
    protected $confidenceWindowString; // (see above. decade, year, early 2015, etc)


    protected $confidenceWindowInSeconds;

    /**
     * @var Carbon $utcBestDate (mid point anchor + confidence window / 2)
     */
    protected $utcBestDate;


    /**
     * @var array $tokens An array of Token objects.
     */
    protected $tokens;

    /**
     * @param string $string       Ex: 'Summer of 78'
     * @param string $timezoneId   Ex: 'America/Denver'
     * @param string $languageCode Ex: 'en'
     *
     * @return static
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     */
    public static function parse( string $string = '', string $timezoneId = 'UTC', string $languageCode = 'en' ): NaturalDate {

        $date = new static();
        $date->setInput( $string );
        $date->setTimezoneId( $timezoneId );
        $date->setLanguageCode( $languageCode );

        date_default_timezone_set( $timezoneId );
        $iAnchorTime = strtotime( $date->getInput() );

        // If the string that is passed in can be parsed by PHP's strtotime() function, then our job is done.
        if ( false !== $iAnchorTime ):
            $date->setLocalAnchorDate( date( 'Y-m-d', $iAnchorTime ) );

            $carbon = Carbon::createFromTimestamp( $iAnchorTime, $date->getTimezoneId() );
            $carbon->setTimezone( 'UTC' );

            $date->setUtcAnchorDate( $carbon->toDateString() );

            $date->setUtcConfidenceWindowStart( $carbon );
            $date->setUtcConfidenceWindowEnd( $carbon );

            return $date;
        endif;

        $date->parseStringToTokens( $date->getLanguageCode(), $date->getInput() );

        /**
         * @var \MichaelDrennen\NaturalDate\Token $token
         */
        foreach ( $date->tokens as $token ):
            $date = $token->process( $date );
        endforeach;

        return $date;
    }

    /**
     * @param string $languageCode
     * @param string $string
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode
     */
    protected function parseStringToTokens( string $languageCode, string $string ) {
        $string         = trim( $string );
        $explodedTokens = explode( " ", $string );
        foreach ( $explodedTokens as $tokenPosition => $explodedToken ):
            $this->tokens[ $tokenPosition ] = $this->makeTokenFromString( $languageCode, $explodedToken, $tokenPosition );
        endforeach;
    }

    /**
     * @param string $languageCode
     * @param string $string
     * @param int    $tokenPosition Tokens are saved into an array. This value is the array index of this Token.
     *
     * @return \MichaelDrennen\NaturalDate\Token
     * @throws \MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode
     */
    protected function makeTokenFromString( string $languageCode, string $string, int $tokenPosition ): Token {
        $tokenFactory = new TokenFactory();

        return $tokenFactory->make( $languageCode, $string, $tokenPosition );
    }


    /**
     * @return mixed
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param mixed $input
     */
    public function setInput( $input ) {
        $this->input = $input;
    }

    /**
     * @return string Ex: 'America/Denver'
     */
    public function getTimezoneId() {
        return $this->timezoneId;
    }

    /**
     * @param string $timezoneId Ex: 'America/Denver'
     *
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     * @link http://php.net/manual/en/datetimezone.listabbreviations.php
     */
    public function setTimezoneId( string $timezoneId ) {
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
    public function setLanguageCode( string $languageCode ) {
        $this->languageCode = $languageCode;
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
    public function getCountryCode() {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode( $countryCode ) {
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getUtcAnchorDate() {
        return $this->utcAnchorDate;
    }

    /**
     * @param mixed $utcAnchorDate
     */
    public function setUtcAnchorDate( $utcAnchorDate ) {
        $this->utcAnchorDate = $utcAnchorDate;
    }

    /**
     * @return mixed
     */
    public function getUtcAnchorTime() {
        return $this->utcAnchorTime;
    }

    /**
     * @param mixed $utcAnchorTime
     */
    public function setUtcAnchorTime( $utcAnchorTime ) {
        $this->utcAnchorTime = $utcAnchorTime;
    }

    /**
     * @return mixed
     */
    public function getLocalAnchorDate() {
        return $this->localAnchorDate;
    }

    /**
     * @param mixed $localAnchorDate
     */
    public function setLocalAnchorDate( $localAnchorDate ) {
        $this->localAnchorDate = $localAnchorDate;
    }


    /**
     * @param Carbon $utcConfidenceWindowStart
     */
    public function setUtcConfidenceWindowStart( Carbon $utcConfidenceWindowStart ) {
        $this->utcConfidenceWindowStart = $utcConfidenceWindowStart;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getUtcConfidenceWindowStart(): Carbon {
        return $this->utcConfidenceWindowStart;
    }

    /**
     * @param Carbon $utcConfidenceWindowEnd
     */
    public function setUtcConfidenceWindowEnd( Carbon $utcConfidenceWindowEnd ) {
        $this->utcConfidenceWindow = $utcConfidenceWindowEnd;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getUtcConfidenceWindowEnd(): Carbon {
        return $this->utcConfidenceWindowEnd;
    }


    /**
     * @return mixed
     */
    public function getConfidenceWindowString(): string {
        return $this->confidenceWindowString;
    }

    /**
     * @param string $confidenceWindowString
     */
    public function setConfidenceWindowString( string $confidenceWindowString ) {
        $this->confidenceWindowString = $confidenceWindowString;
    }

    /**
     * @return mixed
     */
    public function getConfidenceWindowInSeconds() {
        return $this->confidenceWindowInSeconds;
    }

    /**
     * @param mixed $confidenceWindowInSeconds
     */
    public function setConfidenceWindowInSeconds( $confidenceWindowInSeconds ) {
        $this->confidenceWindowInSeconds = $confidenceWindowInSeconds;
    }

    /**
     * @return mixed
     */
    public function getUtcBestDate() {
        return $this->utcBestDate;
    }

    /**
     * @param mixed $utcBestDate
     */
    public function setUtcBestDate( $utcBestDate ) {
        $this->utcBestDate = $utcBestDate;
    }

    public function __construct() {

    }

}