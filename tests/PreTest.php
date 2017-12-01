<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class PreTest
 *
 * @package Html2Text
 */
class PreTest extends \PHPUnit\Framework\TestCase
{
  public function testPre()
  {
    $html = <<<'EOT'
<p>Before</p>
<pre>

Foo bar baz


HTML symbols &amp;

</pre>
<p>After</p>
EOT;

    $expected = <<<'EOT'
Before

Foo bar baz

HTML symbols &

After
EOT;

    $html2text = new Html2Text($html);
    self::assertSame(str_replace(["\n", "\r\n", "\r"], "\n", $expected), $html2text->getText());
  }

  public function testPreNew()
  {
    $html = <<<EOT
<pre>
some<br />  indented<br />  text<br />    on<br />    several<br />  lines<br />
</pre>
EOT;

    $expected = <<<EOT
some
  indented
  text
    on
    several
  lines
EOT;

    $html2text = new Html2Text($html);
    self::assertSame(str_replace(["\n", "\r\n", "\r"], "\n", $expected), $html2text->getText());
  }
}
