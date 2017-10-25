<?php

namespace MichaelDrennen\NaturalDate;

use MichaelDrennen\NaturalDate\Exceptions\StrToTimeParseFailure;

class Token {

    protected $string;

    protected $modifierTokens = [
        'early', 'late',
        'beginning', 'middle', 'end', 'begining',
    ];

    public function __construct( string $string ) {
        $this->setString( $string );
    }

    protected function setString( string $string ) {
        $string       = strtolower( $string );
        $this->string = $string;
    }


}