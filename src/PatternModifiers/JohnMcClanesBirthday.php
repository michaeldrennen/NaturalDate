<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

use Carbon\Carbon;
use MichaelDrennen\NaturalDate\NaturalDate;

class JohnMcClanesBirthday extends PatternModifier {

    protected $patterns = [
        "/john mcclane\'s birthday/i",
        "/john mcclanes birthday/i",
        "/john mcclane birthday/i",
    ];

    public function modify( NaturalDate $naturalDate ): NaturalDate {
        return new NaturalDate( $naturalDate->getInput(),
                                $naturalDate->getTimezoneId(),
                                $naturalDate->getLanguageCode(),
                                Carbon::parse( 'November 2, 1955' ),
                                Carbon::parse( 'November 2, 1955 23:59:59' ),
                                NaturalDate::date,
                                $naturalDate->getPatternModifiers() );
    }
}