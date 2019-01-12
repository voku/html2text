<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class HtmlCharsTest
 *
 * @internal
 */
final class HtmlCharsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function provideSymbols()
    {
        // A variety of symbols that either used to have special handling
        // or still does.
        return [
            // Non-breaking space, not a regular one.
            //array('&nbsp;', ' '),
            ['&gt;', '>'],
            ['&lt;', '<'],
            ['&copy;', '©'],
            ['&#169;', '©'],
            ['&trade;', '™'],
            // The TM symbol in Windows-1252, invalid in HTML...
            ['&#153;', '™'],
            // Correct TM symbol numeric code
            ['&#8482;', '™'],
            ['&reg;', '®'],
            ['&#174;', '®'],
            ['&mdash;', '—'],
            // The m-dash in Windows-1252, invalid in HTML...
            ['&#151;', '—'],
            // Correct m-dash numeric code
            ['&#8212;', '—'],
            ['&bull;', '•'],
            ['&pound;', '£'],
            ['&#163;', '£'],
            ['&euro;', '€'],
            ['&amp;', '&'],
        ];
    }

    public function testLaquoAndRaquo()
    {
        $html = 'This library name is &laquo;Html2Text&raquo;';
        $expected = 'This library name is «Html2Text»';

        $html2text = new Html2Text($html);
        static::assertSame($expected, $html2text->getText());
    }

    /**
     * @dataProvider provideSymbols
     *
     * @param $entity
     * @param $symbol
     */
    public function testSymbol($entity, $symbol)
    {
        $html = "${entity} signs should be UTF-8 symbols";
        $expected = "${symbol} signs should be UTF-8 symbols";

        $html2text = new Html2Text($html);
        static::assertSame($expected, $html2text->getText());
    }
}
