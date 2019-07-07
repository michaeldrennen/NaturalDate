<?php

namespace MichaelDrennen\NaturalDate;

use MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound;
use MichaelDrennen\NaturalDate\PatternModifiers\Between;
use MichaelDrennen\NaturalDate\PatternModifiers\Christmas;
use MichaelDrennen\NaturalDate\PatternModifiers\Early;
use MichaelDrennen\NaturalDate\PatternModifiers\Fall;
use MichaelDrennen\NaturalDate\PatternModifiers\Halloween;
use MichaelDrennen\NaturalDate\PatternModifiers\Late;
use MichaelDrennen\NaturalDate\PatternModifiers\Month;
use MichaelDrennen\NaturalDate\PatternModifiers\NewYears;
use MichaelDrennen\NaturalDate\PatternModifiers\NewYearsEve;
use MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier;
use MichaelDrennen\NaturalDate\PatternModifiers\Thanksgiving;
use MichaelDrennen\NaturalDate\PatternModifiers\Today;
use MichaelDrennen\NaturalDate\PatternModifiers\Tomorrow;
use MichaelDrennen\NaturalDate\PatternModifiers\ValentinesDay;
use MichaelDrennen\NaturalDate\PatternModifiers\Year;
use MichaelDrennen\NaturalDate\PatternModifiers\Yesterday;

class PatternMap {

    const early     = 'early';
    const late      = 'late';
    const beginning = 'beginning';
    const middle    = 'middle';
    const end       = 'end';
    const between   = 'between';
    const before    = 'before';
    const after     = 'after';

    const yesterday = 'yesterday';
    const today     = 'today';
    const tomorrow  = 'tomorrow';

    const year  = 'year';
    const month = 'month';

    const newYears      = 'newYears';
    const valentinesDay = 'valentinesDay';
    const halloween     = 'halloween';
    const thanksgiving  = 'thanksgiving';
    const christmas     = 'christmas';
    const newYearsEve   = 'newYearsEve';

    const summer = 'summer';
    const fall   = 'fall';
    const winter = 'winter';
    const spring = 'spring';


    // I think I want to put classes or objects as the values in this array.
    // Those will tell the code what further processing to do on this string.
    protected $patterns = [
        PatternMap::early     => NULL,
        PatternMap::late      => NULL,
        PatternMap::beginning => NULL,
        PatternMap::middle    => NULL,
        PatternMap::end       => NULL,
        PatternMap::between   => NULL,
        PatternMap::before    => NULL,
        PatternMap::after     => NULL,

        PatternMap::yesterday => NULL,
        PatternMap::today     => NULL,
        PatternMap::tomorrow  => NULL,


        PatternMap::year  => NULL,
        PatternMap::month => NULL,

        PatternMap::newYears      => NULL,
        PatternMap::valentinesDay => NULL,
        PatternMap::halloween     => NULL,
        PatternMap::thanksgiving  => NULL,
        PatternMap::christmas     => NULL,
        PatternMap::newYearsEve   => NULL,

        PatternMap::fall   => NULL,
        PatternMap::spring => NULL,
        PatternMap::summer => NULL,
        PatternMap::winter => NULL,


    ];

    protected $patternModifiers = [
        PatternMap::early     => NULL,
        PatternMap::late      => NULL,
        PatternMap::beginning => NULL,
        PatternMap::middle    => NULL,
        PatternMap::end       => NULL,
        PatternMap::between   => NULL,
        PatternMap::before    => NULL,
        PatternMap::after     => NULL,

        PatternMap::yesterday => NULL,
        PatternMap::today     => NULL,
        PatternMap::tomorrow  => NULL,

        PatternMap::year  => NULL,
        PatternMap::month => NULL,

        PatternMap::newYears      => NULL,
        PatternMap::valentinesDay => NULL,
        PatternMap::halloween     => NULL,
        PatternMap::thanksgiving  => NULL,
        PatternMap::christmas     => NULL,
        PatternMap::newYearsEve   => NULL,

        PatternMap::fall   => NULL,
        PatternMap::spring => NULL,
        PatternMap::summer => NULL,
        PatternMap::winter => NULL,
    ];

    /**
     * @var string $matchedPattern The index from the $patterns array that matches the input string.
     */
    protected $matchedPatternLabel = NULL;


    /**
     * PrimaryPattern constructor.
     *
     * @param array $overridePatterns Developer supplied array that allows new patterns to be added to NaturalDate at
     *                                 run time.
     *
     * @throws \Exception
     */
    public function __construct( array $overridePatterns ) {
        $this->initializePatternModifierObjects( $overridePatterns );
    }


    /**
     * @param array $overridePatterns The programmer has the option of overriding existing pattern modifiers, and/or adding their own.
     */
    protected function initializePatternModifierObjects( array $overridePatterns ) {
        $this->patternModifiers = [
            PatternMap::early   => new Early( $this->patterns[ PatternMap::early ] ),
            PatternMap::late    => new Late( $this->patterns[ PatternMap::late ] ),
            PatternMap::between => new Between( $this->patterns[ PatternMap::between ] ),
            PatternMap::year    => new Year( $this->patterns[ PatternMap::year ] ),
            PatternMap::month   => new Month( $this->patterns[ PatternMap::month ] ),

            PatternMap::yesterday => new Yesterday( $this->patterns[ PatternMap::yesterday ] ),
            PatternMap::today     => new Today( $this->patterns[ PatternMap::today ] ),
            PatternMap::tomorrow  => new Tomorrow( $this->patterns[ PatternMap::tomorrow ] ),

            PatternMap::newYears      => new NewYears( $this->patterns[ PatternMap::newYears ] ),
            PatternMap::valentinesDay => new ValentinesDay( $this->patterns[ PatternMap::valentinesDay ] ),

            PatternMap::halloween    => new Halloween( $this->patterns[ PatternMap::halloween ] ),
            PatternMap::thanksgiving => new Thanksgiving( $this->patterns[ PatternMap::thanksgiving ] ),
            PatternMap::christmas    => new Christmas( $this->patterns[ PatternMap::christmas ] ),
            PatternMap::newYearsEve  => new NewYearsEve( $this->patterns[ PatternMap::newYearsEve ] ),

            PatternMap::fall   => new Fall( $this->patterns[ PatternMap::fall ] ),
            PatternMap::spring => new Fall( $this->patterns[ PatternMap::spring ] ),
            PatternMap::summer => new Fall( $this->patterns[ PatternMap::summer ] ),
            PatternMap::winter => new Fall( $this->patterns[ PatternMap::winter ] ),
        ];

        foreach ( $overridePatterns as $tag => $patternModifier ):
            $this->patternModifiers[ $tag ] = $patternModifier;
        endforeach;
    }

    /**
     * Given an input string, this method loops through each patternModifier object, and returns the patternModifier
     * that first matches a pattern in the input string.
     *
     * @param string $input
     *
     * @return PatternModifier
     * @throws \MichaelDrennen\NaturalDate\Exceptions\NoMatchingPatternFound
     */
    public function setMatchedPattern( string $input ): PatternModifier {
        /**
         * @var \MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier $patternModifier
         */
        foreach ( $this->patternModifiers as $label => $patternModifier ):
            $matched = $patternModifier->match( $input );
            if ( TRUE === $matched ):
                $this->matchedPatternLabel = $label;
                return $patternModifier;
            endif;
        endforeach;

        throw new NoMatchingPatternFound( "No matching pattern found for input string: " . $input );
    }


}