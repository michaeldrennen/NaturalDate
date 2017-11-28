<?php

namespace MichaelDrennen\NaturalDate;


class Token {

    /**
     * Valid token types. I create constants here to make the code clearer to me in my IDE.
     */
    const modifier  = 'modifier';
    const bridge    = 'bridge';
    const connector = 'connector';

    /**
     * @var string $string The string that this token was made from.
     */
    protected $string;

    /**
     * @var int $tokenPosition Tokens are saved into an array. This value is the array index of this Token.
     */
    protected $tokenPosition;

    /**
     * @var \MichaelDrennen\NaturalDate\NaturalDate $naturalDate
     */
    protected $naturalDate;


    /**
     * @var string $tokenType One of the string constants defined above. Ex: self::modifier|bridge|connector
     */
    protected $tokenType;

    /**
     * @var bool
     */
    protected $isModifier = false;

    /**
     * @var bool
     */
    protected $isBridge = false;

    /**
     * @var bool
     */
    protected $isConnector = false;

    /**
     * Token constructor.
     *
     * @param string $string
     * @param int    $tokenPosition Tokens are saved into an array. This value is the array index of this Token.
     */
    public function __construct( string $string, int $tokenPosition ) {
        $this->setString( $string );
        $this->setTokenType( $string );
        $this->setTokenPosition( $tokenPosition );
    }

    /**
     * @param $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     */
    public function process( &$naturalDate ): NaturalDate {
        $this->naturalDate = $naturalDate;
        return $this->naturalDate;
    }

    /**
     * @param string $string
     */
    protected function setString( string $string ) {
        $string       = strtolower( $string );
        $this->string = $string;
    }

    /**
     * @param string $string
     */
    protected function setTokenType( string $string ) {
        if ( $this->isModifierToken( $string ) ):
            $this->tokenType  = self::modifier;
            $this->isModifier = true;
        elseif ( $this->isBridgeToken( $string ) ):
            $this->tokenType = self::bridge;
            $this->isBridge  = true;
        elseif ( $this->isConnectorToken( $string ) ):
            $this->tokenType   = self::connector;
            $this->isConnector = true;
        endif;
    }

    protected function getTokenType(): string {
        return $this->tokenType;
    }

    protected function setTokenPosition( $tokenPosition ) {
        $this->tokenPosition = $tokenPosition;
    }

    protected function getTokenPosition(): int {
        return $this->tokenPosition;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isModifierToken( string $token ): bool {
        return in_array( $token, $this->naturalDate->modifierTokens );
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isBridgeToken( string $token ): bool {
        return in_array( $token, $this->naturalDate->bridgeTokens );
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isConnectorToken( string $token ): bool {
        return in_array( $token, $this->naturalDate->connectorTokens );
    }

}