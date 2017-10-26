<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;

class NaturalDate {

    protected $inputString;
    protected $timezoneId;

    /**
     * @var string $languageCode Ex: 'en'
     */
    protected $languageCode;
    protected $countryCode; // Not sure I need this...?
    protected $utcAnchorDate;
    protected $utcAnchorTime;
    protected $localAnchorDate;
    protected $localAnchorTime;

    protected $utcConfidenceWindowStart;
    protected $utcConfidenceWindowEnd;

    protected $confidenceWindowString; // (see above. decade, year, early 2015, etc)
    protected $confidenceWindowInSeconds;
    protected $utcBestDate; //(mid point anchor + confidence window / 2)
    protected $localBestDate;

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
        $date->setInputString( $string );
        $date->setTimezoneId( $timezoneId );
        $date->setLanguageCode( $languageCode );

        date_default_timezone_set( $timezoneId );
        $iAnchorTime = strtotime( $date->getInputString() );

        // If the string that is passed in can be parsed by PHP's strtotime() function, then our job is done.
        if ( false !== $iAnchorTime ):
            $date->setLocalAnchorDate( date( 'Y-m-d', $iAnchorTime ) );
            $date->setLocalAnchorTime( date( 'H:i:s', $iAnchorTime ) );

            $carbon = Carbon::createFromTimestamp( $iAnchorTime, $date->getTimezoneId() );
            $carbon->setTimezone( 'UTC' );

            $date->setUtcAnchorDate( $carbon->toDateString() );
            $date->setUtcAnchorTime( $carbon->toTimeString() );

            $date->setUtcConfidenceWindowStart( $carbon );
            $date->setUtcConfidenceWindowEnd( $carbon );

            return $date;
        endif;

        $date->parseStringToTokens( $date->getLanguageCode(), $date->getInputString() );

        /**
         * @var \MichaelDrennen\NaturalDate\Token $token
         */
        foreach ( $date->tokens as $token ):
            $date = $token->process( $date );
        endforeach;

        return $date;
    }

    protected function parseStringToTokens( string $languageCode, string $string ) {
        $string         = trim( $string );
        $explodedTokens = explode( " ", $string );
        foreach ( $explodedTokens as $explodedToken ):
            $this->tokens[] = $this->makeTokenFromString( $explodedToken );
        endforeach;
    }

    /**
     * @param string $languageCode
     * @param string $string
     *
     * @return \MichaelDrennen\NaturalDate\Token
     * @throws \MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode
     */
    protected function makeTokenFromString( string $languageCode, string $string ): Token {
        $tokenFactory = new TokenFactory();

        return $tokenFactory->make( $languageCode, $string );
    }


    /**
     * @return mixed
     */
    public function getInputString() {
        return $this->inputString;
    }

    /**
     * @param mixed $inputString
     */
    public function setInputString( $inputString ) {
        $this->inputString = $inputString;
    }

    /**
     * @return mixed
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

    public function setLanguageCode( string $languageCode ) {
        $this->languageCode = $languageCode;
    }

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
     * @return mixed
     */
    public function getLocalAnchorTime() {
        return $this->localAnchorTime;
    }

    /**
     * @param mixed $localAnchorTime
     */
    public function setLocalAnchorTime( $localAnchorTime ) {
        $this->localAnchorTime = $localAnchorTime;
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

    /**
     * @return mixed
     */
    public function getLocalBestDate() {
        return $this->localBestDate;
    }

    /**
     * @param mixed $localBestDate
     */
    public function setLocalBestDate( $localBestDate ) {
        $this->localBestDate = $localBestDate;
    }


    public function __construct() {

    }


}