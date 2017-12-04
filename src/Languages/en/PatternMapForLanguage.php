<?php
namespace MichaelDrennen\NaturalDate\Languages\En;

use MichaelDrennen\NaturalDate\PatternMap;

class PatternMapForLanguage extends PatternMap {

    // I think I want to put classes or objects as the values in this array.
    // Those will tell the code what further processing to do on this string.
    protected $patterns = [
        PatternMap::early     => [ '/early\s*(.*)$/i' ],
        PatternMap::late      => [ '/late\s*(.*)$/i' ],
        PatternMap::beginning => [ '/beginning\s*(.*)$/i' ],
        PatternMap::middle    => [ '/middle\s*(.*)$/i' ],
        PatternMap::end       => [ '/end\s*(.*)$/i' ],
        PatternMap::between   => [ '/between\s*(.*)\s*[and|&|+]\s*(.*)$/i' ],
        PatternMap::before    => [ '/before\s*(.*)$/i' ],
        PatternMap::after     => [ '/after\s*(.*)$/i' ],

        PatternMap::year  => [ '/^(\d{4}|\'\d{2}|\d{2})$/' ],
        PatternMap::month => [
            '/^(january|february|march|april|june|july|august|september|october|november|december)$/i',
            '/^(january|february|march|april|june|july|august|september|october|november|december)\s*(\d{4}|\'\d{2}|\d{2})$/i',
            '/^(jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)$/i',
            '/^(jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)\s*(\d{4}|\'\d{2}|\d{2})$/i',
        ],


    ];
}