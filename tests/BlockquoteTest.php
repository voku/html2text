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

  public function testBlockquoteAdvanced()
  {
    $html = <<<'EOT'
<blockquote>
  <p>Before</p>
  <blockquote>

    <code>
    Foo bar baz
    </code>

    <blockquote>
      HTML symbols &amp;
    </blockquote>

  </blockquote>
  <p>After</p>
</blockquote>
<span>lall</span>
EOT;

    $expected = <<<'EOT'
> Before
>
>>  Foo bar baz
>>
>>>   HTML symbols &
>
> After
lall
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

  public function testMalformedHtmlBlockquotes()
  {
    $html = <<<'EOT'
<p>Before</p>

<blockquote>Foo1 foo1 foo1</lockquote>

<blockquot>Foo2 foo2 foo2</blockquote>

<blockquot>Bar bar bar</blockquot>

<blockquot>Before-After-1</blockquote>

<blockquote>Before-After-2</blockquote>

<blockquote>
  <blockquote>Before-After-3</blockquot>

  Before-After-4
</blockquote>

<p>After</p>
EOT;
    $expected = <<<'EOT'
Before

> Foo1 foo1 foo1 Foo2 foo2 foo2
Bar bar bar Before-After-1

> Before-After-2
Before-After-3 Before-After-4

After
EOT;
    $html2text = new Html2Text($html);
    $this->assertEquals($expected, $html2text->getText());
  }

  public function testBlockquoteWithAttribute()
  {
    $html = <<<'EOT'
<html>
<body>
  <blockquote type="cite">
    <div>
      <span>some quoted words</span>
    </div>
  </blockquote>
  <blockquote type="cite">
    <div>
      <span>second quote</span>
    </div>
  </blockquote>
</body>
</html>
EOT;

    $expected = <<<'EOT'
> some quoted words

> second quote
EOT;

    $html2text = new Html2Text($html);
    $this->assertEquals($expected, $html2text->getText());
  }
}
