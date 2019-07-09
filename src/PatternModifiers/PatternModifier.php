<?php

namespace MichaelDrennen\NaturalDate\PatternModifiers;

use MichaelDrennen\NaturalDate\NaturalDate;

abstract class PatternModifier implements PatternModifierInterface {

    /**
     * @var array
     */
    protected $patterns = [];

    /**
     * @var array
     */
    protected $matches  = [];

    /**
     * PatternModifier constructor.
     *
     * @param array $patterns
     */
    public function __construct( array $patterns = [] ) {
        $this->patterns = array_merge( $this->getPatterns(), $patterns );
    }


    /**
     * @param string $input
     * @return bool
     */
    public function match( string $input ): bool {
        $patterns = $this->getPatterns();
        foreach ( $patterns as $pattern ):
            $matched       = preg_match( $pattern, $input, $this->matches );
            $this->matches = array_map( 'trim', $this->matches );  // Trim the whitespace.
            $this->matches = array_filter( $this->matches );               // Remove empties from the matches array.
            if ( 1 === $matched ):
                return TRUE;
            endif;
        endforeach;

        return FALSE;
    }


    /**
     * @return array
     */
    protected function getPatterns(): array {
        return $this->patterns;
    }


    /**
     * @return array
     */
    public function getMatches(): array {
        return $this->matches;
    }


    /**
     * @param NaturalDate $naturalDate
     * @param int $year
     */
    protected function setStartYearIfNotSetAlready( NaturalDate &$naturalDate, int $year ) {
        if ( is_null( $naturalDate->getStartYear() ) ):
            $naturalDate->setStartYear( $year );
        endif;
    }


    /**
     * @param NaturalDate $naturalDate
     * @param int $year
     */
    protected function setEndYearIfNotSetAlready( NaturalDate &$naturalDate, int $year ) {
        if ( is_null( $naturalDate->getEndYear() ) ):
            $naturalDate->setEndYear( $year );
        endif;
    }

}