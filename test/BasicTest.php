<?php

namespace voku\Html2Text\tests;

use \voku\helper\UTF8;
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

    $this->assertEquals('Hello, "WORLD"', $html->getText());
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

  public function testHtmlToText1()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test1Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test1Html.txt'), $text);
  }

  public function testHtmlToText2()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test2Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test2Html.txt'), $text);
  }

  public function testHtmlToText3()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test3Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test3Html.txt'), $text);
  }

  public function testHtmlToText4()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test4Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test4Html.txt'), $text);
  }

  public function testHtmlToText5()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test5Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test5Html.txt'), $text);
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
}
