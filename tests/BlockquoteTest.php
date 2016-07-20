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
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
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
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
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
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
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

> Foo1 foo1 foo1Foo2 foo2 foo2
Bar bar barBefore-After-1

> Before-After-2
Before-After-3 Before-After-4

After
EOT;
    $html2text = new Html2Text($html);
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
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
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
  }

  /**
   * @return array
   */
  public function blockquoteDataProvider()
  {
    return array(
        'Basic blockquote' => array(
            'html' => <<<EOT
<p>Before</p>
<blockquote>

Foo bar baz


HTML symbols &amp;

</blockquote>
<p>After</p>
EOT
            ,
            'expected' => <<<EOT
Before

> Foo bar baz HTML symbols &

After
EOT
            ,
        ),
        'Multiple blockquotes in text' => array(
            'html' => <<<EOF
<p>Highlights from today&rsquo;s <strong>Newlyhired Game</strong>:</p><blockquote><p><strong>Sean:</strong> What came first, Blake&rsquo;s first <em>Chief Architect position</em> or Blake&rsquo;s first <em>girlfriend</em>?</p> </blockquote> <blockquote> <p><strong>Sean:</strong> Devin, Bryan spent almost five years of his life slaving away for this vampire squid wrapped around the face of humanity&hellip;<br/><strong>Devin:</strong> Goldman Sachs?<br/><strong>Sean:</strong> Correct!</p> </blockquote> <blockquote> <p><strong>Sean:</strong> What was the name of the girl Zhu took to prom three months ago?<br/><strong>John:</strong> What?<br/><strong>Derek (from the audience):</strong> Destiny!<br/><strong>Zhu:</strong> Her name is Jolene. She&rsquo;s nice. I like her.</p></blockquote><p>I think the audience is winning.&nbsp; - Derek</p>
EOF
            ,
            'expected' => <<<EOF
Highlights from today’s NEWLYHIRED GAME:

> SEAN: What came first, Blake’s first _Chief Architect position_ or Blake’s first _girlfriend_?

> SEAN: Devin, Bryan spent almost five years of his life slaving away for this vampire squid wrapped around the face of humanity…
> DEVIN: Goldman Sachs?
> SEAN: Correct!

> SEAN: What was the name of the girl Zhu took to prom three months ago?
> JOHN: What?
> DEREK (FROM THE AUDIENCE): Destiny!
> ZHU: Her name is Jolene. She’s nice. I like her.

I think the audience is winning.  - Derek
EOF
        ),
        'Multibyte strings before blockquote' => array(
            'html' => <<<EOF
“Hello”

<blockquote>goodbye</blockquote>

EOF
            ,
            'expected' => <<<EOF
“Hello”

> goodbye
EOF
        )
    );
  }

  /**
   * @dataProvider blockquoteDataProvider
   *
   * @param string $html
   * @param string $expected
   */
  public function testBlockquoteViaDataProvider($html, $expected) {
    $html2text = new Html2Text($html);
    self::assertSame(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
  }
}
