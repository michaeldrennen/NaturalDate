<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

abstract class PatternModifier implements PatternModifierInterface {

    protected $patterns = [];
    protected $matches = [];

    /**
     * PatternModifier constructor.
     *
     * @param array $patterns
     */
    public function __construct( array $patterns = [] ) {
        $this->patterns = array_merge( $this->getPatterns(), $patterns );
    }


    public function match( string $input ): bool {
        $patterns = $this->getPatterns();
        foreach ( $patterns as $pattern ):
            //echo "\nPATTERN: " . $pattern . "\n";
            $matched       = preg_match( $pattern, $input, $this->matches );
            $this->matches = array_map( 'trim', $this->matches );   // Trim the whitespace.
            $this->matches = array_filter( $this->matches );                // Remove empties from the matches array.
            if ( 1 === $matched ):
                return true;
            endif;
        endforeach;

        return false;
    }


    protected function getPatterns(): array {
        return $this->patterns;
    }

    /**
     * @return array
     */
    public function getMatches(): array {
        return $this->matches;
    }

}