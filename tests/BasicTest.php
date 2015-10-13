<?php

namespace voku\Html2Text\tests;

use \voku\Html2Text\Html2Text;

/**
 * Class BasicTest
 *
 * @package Html2Text
 */
class BasicTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var string
   */
  public $inputLink = '<a href="http://example.com">Link text</a>';

  public function testBasicUsageInReadme()
  {
    $html = new Html2Text('Hello, &quot;<b>world</b>&quot;');

    self::assertEquals('Hello, "WORLD"', $html->getText());
  }

  /**
   * testDoLinksInline
   */
  public function testDoLinksInline()
  {
    $expected_output = <<<EOT
Link text [http://example.com]
EOT;

    $html2text = new Html2Text($this->inputLink, false, array('do_links' => 'inline'));
    $output = $html2text->getText();

    self::assertEquals($expected_output, $output);
  }

  /**
   * testDoLinksNone
   */
  public function testDoLinksNone()
  {
    $expected_output = <<<EOT
Link text
EOT;

    $html2text = new Html2Text($this->inputLink, false, array('do_links' => 'none'));
    $output = $html2text->getText();

    self::assertEquals($output, $expected_output);
  }

  /**
   * @return array
   */
  public function basicDataProvider() {
    return array(
        'Readme usage' => array(
            'html'      => 'Hello, &quot;<b>world</b>&quot;',
            'expected'  => 'Hello, "WORLD"',
        ),
        'No stripslashes on HTML content' => array(
            // HTML content does not escape slashes, therefore nor should we.
            'html'      => 'Hello, \"<b>world</b>\"',
            'expected'  => 'Hello, \"WORLD\"',
        ),
        'Zero is not empty' => array(
            'html'      => '0',
            'expected'  => '0',
        ),
        'Paragraph with whitespace wrapping it' => array(
            'html'      => 'Foo <p>Bar</p> Baz',
            'expected'  => "Foo\n\nBar\n\nBaz",
        ),
        'Paragraph text with linebreak flat' => array(
            'html'      => "<p>Foo<br/>Bar</p>",
            'expected'  => "Foo\nBar"
        ),
        'Paragraph text with linebreak formatted with newline' => array(
            'html'      => "\n<p>\n    Foo<br/>\n    Bar\n</p>\n",
            'expected'  => "Foo\nBar"
        ),
        'Paragraph text with linebreak formatted whth newline, but without whitespace' => array(
            'html'      => "<p>Foo<br/>\nBar</p>\n\n<p>lall</p>\n",
            'expected'  => "Foo\nBar\n\nlall"
        ),
        'Paragraph text with linebreak formatted with indentation' => array(
            'html'      => "\n<p>\n    Foo<br/>Bar\n</p>\nlall\n",
            'expected'  => "Foo\nBar\n\nlall"
        ),
    );
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
    self::assertEquals($expected, $html->getText());
  }
}