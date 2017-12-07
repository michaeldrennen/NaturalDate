<?php
namespace MichaelDrennen\NaturalDate\PatternModifiers;

abstract class PatternModifier implements PatternModifierInterface {

    protected $patterns;
    protected $matches = [];

    public function __construct( array $patterns ) {
        $this->patterns = $patterns;
    }


    public function match( string $input ): bool {
        $patterns = $this->getPatterns();
        foreach ( $patterns as $pattern ):
            echo "\n" . $pattern;
            $matched = preg_match( $pattern, $input, $this->matches );
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