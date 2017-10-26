<?php

namespace MichaelDrennen\NaturalDate;


class Token {

    const modifier  = 'modifier';
    const bridge    = 'bridge';
    const connector = 'connector';

    protected $string;

    protected $modifierTokens = [
        'early', 'late',
        'beginning', 'middle', 'end', 'begining',
    ];

    protected $bridgeTokens = [
        'between', 'betwen',
    ];

    protected $connectorTokens = [
        'and', '&', '+', 'n',
    ];

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
     */
    public function __construct( string $string ) {
        $this->setString( $string );
        $this->setTokenType( $string );
    }

    /**
     * @param $naturalDate
     *
     * @return \MichaelDrennen\NaturalDate\NaturalDate
     */
    public function process( &$naturalDate ): NaturalDate {


        return $naturalDate;
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

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isModifierToken( string $token ): bool {
        return in_array( $token, $this->modifierTokens );
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isBridgeToken( string $token ): bool {
        return in_array( $token, $this->bridgeTokens );
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    private function isConnectorToken( string $token ): bool {
        return in_array( $token, $this->connectorTokens );
    }

}