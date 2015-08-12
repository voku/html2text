<?php

namespace voku\Html2Text\tests;

use \voku\helper\UTF8;
use \voku\Html2Text\Html2Text;

/**
 * Class BasicTest
 *
 * @package Html2Text
 */
class MailTest extends \PHPUnit_Framework_TestCase
{

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

  public function testHtmlToText6()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test6Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test6Html.txt'), $text);
  }

  public function testHtmlToText7()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/test7Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertEquals(UTF8::file_get_contents(__DIR__ . '/test7Html.txt'), $text);
  }
}
