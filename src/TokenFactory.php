<?php
namespace MichaelDrennen\NaturalDate;

use MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode;
use MichaelDrennen\NaturalDate\Languages\En\TokenEn;

class TokenFactory {

    const en = 'en';

    /**
     * @param string $languageCode
     * @param string $string
     *
     * @return \MichaelDrennen\NaturalDate\Token
     * @throws \MichaelDrennen\NaturalDate\Exceptions\Token\UndefinedLanguageCode
     */
    public function make( string $languageCode, string $string ): Token {
        switch ( $languageCode ):
            case self::en:
                return new TokenEn( $string );
            default:
                throw new UndefinedLanguageCode();
        endswitch;
    }
}