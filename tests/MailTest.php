<?php

namespace voku\Html2Text\tests;

use voku\helper\UTF8;
use voku\Html2Text\Html2Text;

/**
 * Class BasicTest
 *
 * @package Html2Text
 */
class MailTest extends \PHPUnit_Framework_TestCase
{

  public function testHtmlToText1()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test1Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test1Html.txt'), $text);
  }

  public function testHtmlToText2()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test2Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test2Html.txt'), $text);
  }

  public function testHtmlToText3()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test3Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test3Html.txt'), $text);
  }

  public function testHtmlToText4()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test4Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test4Html.txt'), $text);
  }

  public function testHtmlToText5()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test5Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test5Html.txt'), $text);
  }

  public function testHtmlToText6()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test6Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test6Html.txt'), $text);
  }

  public function testHtmlToText7()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test7Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test7Html.txt'), $text);
  }

  public function testHtmlToText8()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test8Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test8Html.txt'), $text);
  }

  public function testHtmlToText9()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test9Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals($this->file_get_contents(__DIR__ . '/fixtures/test9Html.txt'), $text);
  }

  protected function file_get_contents($filename)
  {
    $string = UTF8::file_get_contents($filename);

    return $this->normalizeString($string);
  }

  protected function normalizeString($string)
  {
    return str_replace(array("\r\n", "\r"), "\n", $string);
  }
}
