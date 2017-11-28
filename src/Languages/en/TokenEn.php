<?php
namespace MichaelDrennen\NaturalDate\Languages\En;

use MichaelDrennen\NaturalDate\Token;

class TokenEn extends Token {

    protected $modifierTokens = [
        'early', 'late',
        'beginning', 'middle', 'end',
        'begining', // sic*
    ];

    protected $bridgeTokens = [
        'between',
        'betwen', // sic*
    ];

    protected $connectorTokens = [
        'and', '&', 'n', '+',
    ];

    /**
     * TokenEn constructor.
     *
     * @param string $string
     * @param int    $tokenPosition Tokens are saved into an array. This value is the array index of this Token.
     */
    public function __construct( $string, $tokenPosition ) {
        parent::__construct( $string, $tokenPosition );
    }
}