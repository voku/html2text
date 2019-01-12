<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class ListItemsTest
 *
 * @internal
 */
final class ListItemsTest extends \PHPUnit\Framework\TestCase
{
    public function testLargeOrderedList()
    {
        $input = <<<EOT
<ol>
    <li>one</li>
    <li>two</li>
    <li>three</li>
    <li>four</li>
    <li>five</li>
    <li>six</li>
    <li>seven</li>
    <li>eight</li>
    <li>nine</li>
    <li>ten</li>
    <li>eleven</li>
</ol>
EOT;
        $expected_output = <<<EOT
* one
* two
* three
* four
* five
* six
* seven
* eight
* nine
* ten
* eleven
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }

    public function testMultiLevelUnorderedList()
    {
        $input = <<<EOT
<ul>
  <li>Coffee</li>
  <li>Tea
    <ul>
      <li>Black tea</li>
      <li>Green tea</li>
      <ul>
        <li>Green tea: foo</li>
        <li>Green tea: bar</li>
      </ul>
    </ul>
  </li>
  <li>Milk</li>
</ul>
EOT;
        $expected_output = <<<EOT
* Coffee
Tea

* Black tea
* Green tea

* Green tea: foo
* Green tea: bar

* Milk
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }

    public function testMultiLineOrderedList()
    {
        $input = <<<EOT
<ol>
    <li>this is a really long line, and it should be split into two lines. let's hope it is</li>
    <li>two</li>
</ol>
EOT;
        $expected_output = <<<EOT
* this is a really long line, and it should be split into two lines. let's hope it is
* two
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }

    public function testMultiLineUnorderedList()
    {
        $input = <<<EOT
<ul>
    <li>this is a really long line, and it should be split into two lines. let's hope it is</li>
    <li>two</li>
</ul>
EOT;
        $expected_output = <<<EOT
* this is a really long line, and it should be split into two lines. let's hope it is
* two
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }

    public function testOrderedList()
    {
        $input = <<<EOT
<ol>
    <li>one</li>
    <li>two</li>
    <li></li>
</ol>
<p>lall</p>
<ol>
    <li>foo</li>
    <li>bar</li>
    <li></li>
</ol>
<ol>
    <li>one</li>
    <li>two</li>
    <li>three</li>
</ol>
foo
EOT;
        $expected_output = <<<EOT
* one
* two
*

lall

* foo
* bar
*

* one
* two
* three

foo
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }

    public function testUnorderedList()
    {
        $input = <<<EOT
<ul>
    <li>one</li>
    <li>two</li>
    <li></li>
</ul>
EOT;
        $expected_output = <<<EOT
* one
* two
*
EOT;

        $html2text = new Html2Text($input);
        $output = $html2text->getText();

        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected_output), $output);
    }
}
