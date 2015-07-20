<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class ParagraphBreakTest
 *
 * @package voku\Html2Text\tests
 */
class ParagraphBreakTest extends \PHPUnit_Framework_TestCase
{
  public function testParagraphBreak()
  {
    $html = <<<EOT
Before
<p>
    This is a paragraph
</p>
After
EOT;
    $expected = <<<EOT
Before

This is a paragraph

After
EOT;

    $html2text = new Html2Text($html);
    $this->assertEquals($expected, $html2text->getText());
  }
}