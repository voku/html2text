<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class TableTest
 *
 * @package Html2Text
 */
class UppercaseTest extends \PHPUnit\Framework\TestCase
{
  public function testUppercase()
  {
    $html = <<<'EOT'
<table>
  <tr>
    <th>Heading 1</th>
    <td>Data 1</td>
  </tr>
  <tr>
    <th>Heading 2</th>
    <td>Data 2</td>
  </tr>
</table>
EOT;

    $expected = <<<'EOT'
HEADING 1
Data 1

HEADING 2
Data 2
EOT;

    $html2text = new Html2Text($html);
    self::assertSame(str_replace(["\n", "\r\n", "\r"], "\n", $expected), $html2text->getText());
  }

  public function testNoUppercase()
  {
    $html = <<<'EOT'
<table>
  <tr>
    <th>Heading 1</th>
    <td>Data 1</td>
  </tr>
  <tr>
    <th>Heading 2</th>
    <td>Data 2</td>
  </tr>
</table>
EOT;

    $expected = <<<'EOT'
HEADING 1
Data 1

HEADING 2
Data 2
EOT;

    $html2text = new Html2Text($html, ['do_upper' => false]);
    self::assertSame(str_replace(["\n", "\r\n", "\r"], "\n", $expected), $html2text->getText());
  }
}
