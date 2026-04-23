<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class UppercaseTagsTest
 *
 * Tests that uppercase HTML tags are handled correctly.
 *
 * @internal
 */
final class UppercaseTagsTest extends \PHPUnit\Framework\TestCase
{
    public function testUppercaseParagraph()
    {
        $html2text = new Html2Text('<P>Test string</P>');
        static::assertSame('Test string', $html2text->getText());
    }

    public function testUppercaseHeading()
    {
        $html2text = new Html2Text('<H1>Test heading</H1>');
        static::assertSame('TEST HEADING', $html2text->getText());
    }

    public function testMixedCaseTags()
    {
        $html2text = new Html2Text('<P>paragraph</P><H2>heading two</H2><P>another paragraph</P>');
        static::assertSame("paragraph\n\nHEADING TWO\n\nanother paragraph", $html2text->getText());
    }

    public function testUppercaseBold()
    {
        $html2text = new Html2Text('<B>bold text</B>');
        static::assertSame('BOLD TEXT', $html2text->getText());
    }

    public function testUppercaseAnchor()
    {
        $html2text = new Html2Text('<A href="http://example.com">link text</A>');
        static::assertSame('link text [http://example.com]', $html2text->getText());
    }

    public function testUppercaseBr()
    {
        $html2text = new Html2Text('line one<BR>line two');
        static::assertSame("line one\nline two", $html2text->getText());
    }
}
