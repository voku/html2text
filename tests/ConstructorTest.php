<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class ConstructorTest
 *
 * @internal
 */
final class ConstructorTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $html = 'Foo';
        $options = ['do_links' => 'none'];
        $html2text = new Html2Text($html, $options);
        static::assertSame($html, $html2text->getText());

        $html2text = new Html2Text($html);
        static::assertSame($html, $html2text->getText());
    }

    public function testLegacyConstructor()
    {
        $html = 'Foo';
        $options = ['do_links' => 'none'];

        $html2text = new Html2Text($html, $options);
        static::assertSame($html, $html2text->getText());
    }
}
