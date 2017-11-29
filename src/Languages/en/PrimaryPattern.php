<?php
namespace MichaelDrennen\NaturalDate\Languages\En;

class PrimaryPattern {

    // I think I want to put classes or objects as the values in this array.
    // Those will tell the code what further processing to do on this string.
    protected $patterns = [
        'early\s*(.*)$'                      => '',
        'late\s*(.*)$'                       => '',
        'beginning\s*(.*)$'                  => '',
        'middle\s*(.*)$'                     => '',
        'end\s*(.*)$'                        => '',
        'between\s*(.*)\s*[and|&|+]\s*(.*)$' => '',
    ];

    /**
     * @var string $matchedPattern The index from the $patterns array that matches the input string.
     */
    protected $matchedPattern = '';

    /**
     * @var string $input
     */
    protected $input;

    /**
     * PrimaryPattern constructor.
     *
     * @param string $string
     *
     * @throws \Exception
     */
    public function __construct( string $string ) {
        $this->input = $string;
        $this->setMatchedPattern();
    }

    private function setMatchedPattern() {
        foreach ( $this->patterns as $pattern => $processingObject ):
            $matched = preg_match( $pattern, $this->input );
            if ( 1 === $matched ):
                $this->matchedPattern = $pattern;
                return;
            endif;
        endforeach;
        throw new \Exception( "No matching pattern found for input string: " . $this->input );
    }

}