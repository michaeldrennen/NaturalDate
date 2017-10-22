<?php

namespace MichaelDrennen\NaturalDate;

use Carbon\Carbon;
use DateTimeZone;
use MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone;
use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;

class NaturalDate {

    protected $inputString;
    protected $timezoneId;
    protected $countryCode; // Not sure I need this...?
    protected $utcAnchorDate;
    protected $utcAnchorTime;
    protected $localAnchorDate;
    protected $localAnchorTime;
    protected $utcConfidenceWindowStartDate;
    protected $utcConfidenceWindowEndDate;
    protected $utcConfidenceWindowStartTime;
    protected $utcConfidenceWindowEndTime;
    protected $confidenceWindowString; // (see above. decade, year, early 2015, etc)
    protected $confidenceWindowInSeconds;
    protected $utcBestDate; //(mid point anchor + confidence window / 2)
    protected $localBestDate;

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
     * @return mixed
     */
    public function getUtcConfidenceWindowStartDate() {
        return $this->utcConfidenceWindowStartDate;
    }

    /**
     * @param mixed $utcConfidenceWindowStartDate
     */
    public function setUtcConfidenceWindowStartDate( $utcConfidenceWindowStartDate ) {
        $this->utcConfidenceWindowStartDate = $utcConfidenceWindowStartDate;
    }

    /**
     * @return mixed
     */
    public function getUtcConfidenceWindowEndDate() {
        return $this->utcConfidenceWindowEndDate;
    }

    /**
     * @param mixed $utcConfidenceWindowEndDate
     */
    public function setUtcConfidenceWindowEndDate( $utcConfidenceWindowEndDate ) {
        $this->utcConfidenceWindowEndDate = $utcConfidenceWindowEndDate;
    }

    /**
     * @return mixed
     */
    public function getUtcConfidenceWindowStartTime() {
        return $this->utcConfidenceWindowStartTime;
    }

    /**
     * @param mixed $utcConfidenceWindowStartTime
     */
    public function setUtcConfidenceWindowStartTime( $utcConfidenceWindowStartTime ) {
        $this->utcConfidenceWindowStartTime = $utcConfidenceWindowStartTime;
    }

    /**
     * @return mixed
     */
    public function getUtcConfidenceWindowEndTime() {
        return $this->utcConfidenceWindowEndTime;
    }

    /**
     * @param mixed $utcConfidenceWindowEndTime
     */
    public function setUtcConfidenceWindowEndTime( $utcConfidenceWindowEndTime ) {
        $this->utcConfidenceWindowEndTime = $utcConfidenceWindowEndTime;
    }

    /**
     * @return mixed
     */
    public function getConfidenceWindowString() {
        return $this->confidenceWindowString;
    }

    /**
     * @param mixed $confidenceWindowString
     */
    public function setConfidenceWindowString( $confidenceWindowString ) {
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


    /**
     * @param string $string
     * @param string $timezoneId
     *
     * @return static
     * @throws \MichaelDrennen\NaturalDate\Exceptions\InvalidTimezone
     * @throws \MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure
     */
    public static function parse( string $string = '', string $timezoneId ): NaturalDate {


        $date = new static();
        $date->setInputString( $string );
        $date->setTimezoneId( $timezoneId );

        date_default_timezone_set( $timezoneId );
        $iAnchorTime = strtotime( $date->getInputString() );

        if ( false === $iAnchorTime ):
            throw new StrToTimeParseFailure( "The input string you passed [" . $date->getInputString() . "] could not be parsed by strtotime()" );
        endif;

        $date->setLocalAnchorDate( date( 'Y-m-d', $iAnchorTime ) );
        $date->setLocalAnchorTime( date( 'H:i:s', $iAnchorTime ) );

        $carbon = Carbon::createFromTimestamp( $iAnchorTime, $date->getTimezoneId() );
        $carbon->setTimezone( 'UTC' );

        $date->setUtcAnchorDate( $carbon->toDateString() );
        $date->setUtcAnchorTime( $carbon->toTimeString() );


        return $date;
    }

}