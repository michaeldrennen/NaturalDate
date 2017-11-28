<?php
namespace MichaelDrennen\NaturalDate;

use MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode;
use MichaelDrennen\NaturalDate\Languages\En\TokenEn;

class TokenFactory {

    const en = 'en';

    /**
     * @param string $languageCode
     * @param string $string
     * @param int    $tokenPosition Tokens are saved into an array. This value is the array index of this Token.
     *
     * @return \MichaelDrennen\NaturalDate\Token
     * @throws \MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode
     */
    public function make( string $languageCode, string $string, int $tokenPosition ): Token {
        switch ( $languageCode ):
            case self::en:
                return new TokenEn( $string, $tokenPosition );
            default:
                throw new UndefinedLanguageCode();
        endswitch;
    }
}