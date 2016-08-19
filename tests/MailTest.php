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

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test1Html.txt'), $text);
  }

  public function testHtmlToText2()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test2Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test2Html.txt'), $text);
  }

  public function testHtmlToText3()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test3Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test3Html.txt'), $text);
  }

  public function testHtmlToText4()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test4Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test4Html.txt'), $text);
  }

  public function testHtmlToText5()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test5Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test5Html.txt'), $text);
  }

  public function testHtmlToText6()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test6Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test6Html.txt'), $text);
  }

  public function testHtmlToText7()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test7Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test7Html.txt'), $text);
  }

  public function testHtmlToText8()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test8Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test8Html.txt'), $text);
  }

  public function testHtmlToText9()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test9Html.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test9Html.txt'), $text);
  }

  public function testHtmlToText10()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/test10Html.html');

    $html2text = new Html2Text(
        $html,
        false,
        array(
            'directConvert' => true,
            'do_links' => 'markdown',
            'do_links_ignore' => 'javascript:|mailto:',
            'elements'        => array(
                'pre'    => array(
                    'prepend' => '```php' . "\n",
                    'append'  => "\n" . '```',
                ),
                'h5'     => array(
                    'case'    => Html2Text::OPTION_NONE,
                    'prepend' => "\n\n",
                    'append'  => "\n\n",
                ),
            )
        )
    );

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/test10Html.txt'), $text);
  }

  public function testHtmlToTextMsOffice()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/msoffice.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/msoffice.txt'), $text);
  }

  public function testHtmlToTextNbsp()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/nbsp.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/nbsp.txt'), $text);
  }

  public function testHtmlToTextNonBreakingSpace()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/non-breaking-spaces.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/non-breaking-spaces.txt'), $text);
  }

  public function testHtmlToTextTable()
  {
    $html = UTF8::file_get_contents(__DIR__ . '/fixtures/table.html');

    $html2text = new Html2Text($html, false, array('directConvert' => true));

    $text = $html2text->getText();

    self::assertSame($this->file_get_contents(__DIR__ . '/fixtures/table.txt'), $text);
  }

  /**
   * @param string $filename
   *
   * @return string
   */
  protected function file_get_contents($filename)
  {
    $string = UTF8::file_get_contents($filename);

    return $this->normalizeString($string);
  }

  /**
   * @param string $string
   *
   * @return string
   */
  protected function normalizeString($string)
  {
    return str_replace(array("\r\n", "\r"), "\n", $string);
  }
}
