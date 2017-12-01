<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class BasicTest
 *
 * @package Html2Text
 */
class BasicTest extends \PHPUnit\Framework\TestCase
{

  /**
   * @var string
   */
  public $inputLink = '<a href="http://example.com">Link text</a>';

  public function testBasicUsageInReadme()
  {
    $html = new Html2Text('Hello, &quot;<b>world</b>&quot;');

    self::assertSame('Hello, "WORLD"', $html->getText());
  }

  /**
   * testDoLinksInline
   */
  public function testDoLinksInline()
  {
    $expected_output = <<<EOT
Link text [http://example.com]
EOT;

    $html2text = new Html2Text($this->inputLink, ['do_links' => 'inline']);
    $output = $html2text->getText();

    self::assertSame($expected_output, $output);
  }

  public function testNewLines()
  {
    $html = <<<EOT
<p>Between this and</p>
<p>this paragraph there should be only one newline</p>
<h1>and this also goes for headings</h1>
<h1 style="color: red;">test</h1>
test
<br>
lall
EOT;
    $expected = <<<EOT
Between this and

this paragraph there should be only one newline

AND THIS ALSO GOES FOR HEADINGS

TEST

test
lall
EOT;
    $html2text = new Html2Text($html);
    $output = $html2text->getText();
    self::assertSame(str_replace(["\n", "\r\n", "\r"], "\n", $expected), $output);
  }

  /**
   * testDoLinksNone
   */
  public function testDoLinksNone()
  {
    $expected_output = <<<EOT
Link text
EOT;

    $html2text = new Html2Text($this->inputLink, ['do_links' => 'none']);
    $output = $html2text->getText();

    self::assertSame($output, $expected_output);
  }

  /**
   * @return array
   */
  public function basicDataProvider()
  {
    return [
        'Readme usage'                                                                 => [
            'html'     => 'Hello, &quot;<b>world</b>&quot;',
            'expected' => 'Hello, "WORLD"',
        ],
        'No stripslashes on HTML content'                                              => [
          // HTML content does not escape slashes, therefore nor should we.
          'html'     => 'Hello, \"<b>world</b>\"',
          'expected' => 'Hello, \"WORLD\"',
        ],
        'Zero is not empty'                                                            => [
            'html'     => '0',
            'expected' => '0',
        ],
        'Paragraph with whitespace wrapping it'                                        => [
            'html'     => 'Foo <p>Bar</p> Baz',
            'expected' => "Foo\n\nBar\n\nBaz",
        ],
        'Paragraph text with linebreak flat'                                           => [
            'html'     => '<p>Foo<br/>Bar</p>',
            'expected' => "Foo\nBar",
        ],
        'Paragraph text with linebreak formatted with newline'                         => [
            'html'     => "\n<p>\n    Foo<br/>\n    Bar\n</p>\n",
            'expected' => "Foo\nBar",
        ],
        'Paragraph text with linebreak formatted whth newline, but without whitespace' => [
            'html'     => "<p>Foo<br/>\nBar</p>\n\n<p>lall</p>\n",
            'expected' => "Foo\nBar\n\nlall",
        ],
        'Paragraph text with linebreak formatted with indentation'                     => [
            'html'     => "\n<p>\n    Foo<br/>Bar\n</p>\nlall\n",
            'expected' => "Foo\nBar\n\nlall",
        ],
        '<br /> within <strong> prevents <strong> from being converted'                => [
            'html'     => '<strong>This would<br />not be converted.</strong><strong>But this would, though</strong>',
            'expected' => "THIS WOULDNOT BE CONVERTED.\nTHIS WOULDNOT BE CONVERTED.BUT THIS WOULD, THOUGH",
        ],
    ];
  }

  /**
   * @dataProvider basicDataProvider
   *
   * @param string $html
   * @param string $expected
   */
  public function testBasic($html, $expected)
  {
    $html = new Html2Text($html);
    self::assertSame($expected, $html->getText());
  }
}
