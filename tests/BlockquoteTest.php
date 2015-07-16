<?php

namespace voku\Html2Text\tests;

use \voku\Html2Text\Html2Text;

/**
 * Class BlockquoteTest
 *
 * @package Html2Text
 */
class BlockquoteTest extends \PHPUnit_Framework_TestCase
{
  public function testBlockquote()
  {
    $html = <<<'EOT'
<p>Before</p>
<blockquote>

Foo bar baz


HTML symbols &amp;

</blockquote>
<p>After</p>
EOT;

    $expected = <<<'EOT'
Before

> Foo bar baz HTML symbols &

After
EOT;

    $html2text = new Html2Text($html);
    $this->assertEquals($expected, $html2text->getText());
  }

  public function testMultipleBlockquotes()
  {
    $html = <<<'EOT'
<p>Before</p>
<blockquote>Foo foo foo</blockquote>
<blockquote>Foo foo foo</blockquote>
<blockquote>Bar bar bar</blockquote>
<p>After</p>
EOT;
    $expected = <<<'EOT'
Before

> Foo foo foo

> Foo foo foo

> Bar bar bar

After
EOT;
    $html2text = new Html2Text($html);
    $this->assertEquals($expected, $html2text->getText());
  }
}
