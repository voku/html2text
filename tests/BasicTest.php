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

    $this->assertEquals('Hello, "WORLD"', $html->getText());
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
        'Zero is not empty' => array(
            'html'      => '0',
            'expected'  => '0',
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
    $this->assertEquals($expected, $html->getText());
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
}
