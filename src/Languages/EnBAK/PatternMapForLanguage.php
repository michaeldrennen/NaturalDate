<?php

namespace MichaelDrennen\NaturalDate\Languages\En;

use MichaelDrennen\NaturalDate\PatternMap;

class PatternMapForLanguage extends PatternMap {

    // I think I want to put classes or objects as the values in this array.
    // Those will tell the code what further processing to do on this string.
    protected $patterns = [
        PatternMap::early     => [ '/^early[\s]*(.*)$/i' ],
        PatternMap::late      => [ '/^late[\s]*(.*)$/i' ],
        PatternMap::beginning => [ '/^beginning(of)?[\s]*(.*)$/i' ],
        PatternMap::middle    => [ '/^middle[\s]*(of)?\s*(.*)$/i' ],
        PatternMap::end       => [ '/^end[\s]*(of)?\s*(.*)$/i' ],
        PatternMap::between   => [ '/between[\s]*(.*)(and|&|\+)\s*(.*)/i' ],
        PatternMap::before    => [ '/^before[\s]*(.*)$/i' ],
        PatternMap::after     => [ '/^after[\s]*(.*)$/i' ],

        PatternMap::yesterday => [ '/^yesterday$/i' ],
        PatternMap::today     => [ '/^today/i' ],
        PatternMap::tomorrow  => [ '/^tomorrow$/i' ],

        PatternMap::year          => [ '/^(\d{4}|\'\d{2}|\d{2})$/' ],
        PatternMap::month         => [
            '/^(january|february|march|april|june|july|august|september|october|november|december)$/i',
            '/^(january|february|march|april|june|july|august|september|october|november|december)\s*(\d{4}|\'\d{2}|\d{2})$/i',
            '/^(jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)$/i',
            '/^(jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)\s*(\d{4}|\'\d{2}|\d{2})$/i',
        ],
        PatternMap::newYears      => [
            '/^new years[\s]*(?:of)?[\s]*(\d{2,4})$/i',
            '/^new year\'s[\s]*(?:of)?[\s]*(\d{2,4})$/i',
            '/^new years\'[\s]*(?:of)?[\s]*(\d{2,4})$/i',
            '/^ny[\s]*(?:of)?[\s]*(\d{2,4})$/i', // "ny2017" or "ny 2017"
        ],
        PatternMap::valentinesDay => [
            '/^valentines day[\s]*(?:of)?[\s]*(.*)/i',
            '/^valentine\'s day[\s]*(?:of)?[\s]*(.*)/i',
        ],
        PatternMap::halloween     => [
            "/^halloween[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::thanksgiving  => [
            "/^thanksgiving[\s]*(?:of)?[\s]*(.*)/i",
            "/^turkey day[\s]*(?:of)?[\s]*(.*)/i",
            "/^thxgiving[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::christmas     => [
            "/^christmas[\s]*(?:of)?[\s]*(.*)/i",
            "/^xmas[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::newYearsEve   => [
            "/^new years eve[\s]*(?:of)?[\s]*(.*)/i",
            '/^new year\'s eve[\s]*(?:of)?[\s]*(.*)/i',
            '/^new years\' eve[\s]*(?:of)?[\s]*(.*)/i',
            "/^nye[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::fall          => [
            "/^fall[\s]*(?:of)?[\s]*(.*)/i",
            "/^autumn?[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::spring        => [
            "/^spring[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::summer        => [
            "/^summer[\s]*(?:of)?[\s]*(.*)/i",
        ],
        PatternMap::winter        => [
            "/^winter[\s]*(?:of)?[\s]*(.*)/i",
        ],

    ];
}