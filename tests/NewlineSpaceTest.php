<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class NewlineSpaceTest
 *
 * @package voku\Html2Text\tests
 */
class NewlineSpaceTest extends \PHPUnit_Framework_TestCase
{
  public function testNewlineTabSpace()
  {
    $html = <<<EOT
<p>Will be a line</p>
<p>
  Will be a line
</p>
<p>This is some text<br/>with a break in the middle but no indent</p>
EOT;
    $expected = <<<EOT
Will be a line

Will be a line

This is some text
with a break in the middle but no indent
EOT;
    $html2text = new Html2Text($html);
    self::assertEquals($expected, $html2text->getText());
  }
}
