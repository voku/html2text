<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class StrToUpperTest
 *
 * @package Html2Text
 */
class StrToUpperTest extends \PHPUnit\Framework\TestCase
{
  public function testToUpper()
  {
    $html = <<<EOT
<h1>Will be UTF-8 (äöüèéилčλ) uppercased</h1>
<p>Will remain lowercased</p>
EOT;
    $expected = <<<EOT
WILL BE UTF-8 (ÄÖÜÈÉИЛČΛ) UPPERCASED

Will remain lowercased
EOT;

    $html2text = new Html2Text($html);
    $output = $html2text->getText();

    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $output);
  }
}
