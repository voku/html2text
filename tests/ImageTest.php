<?php

namespace Html2Text;

use voku\Html2Text\Html2Text;

/**
 * Class ImageTest
 *
 * @package Html2Text
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
  public function testShowAltText()
  {
    $html = new Html2Text("<img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt=\"This is our cool logo\" />\n    <br/>\n\n    <img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt='This is our cool logo' data-foo=\"bar\">");

    self::assertEquals("image: \"This is our cool logo\"\nimage: 'This is our cool logo'", $html->getText());
  }

  /**
   * @return array
   */
  public function testImageDataProvider()
  {
    return array(
        'Without alt tag'                  => array(
            'html'     => '<img src="http://example.com/example.jpg">',
            'expected' => '',
        ),
        'Without alt tag, wrapped in text' => array(
            'html'     => 'xx<img src="http://example.com/example.jpg">xx',
            'expected' => 'xxxx',
        ),
        'With alt tag'                     => array(
            'html'     => '<img src="http://example.com/example.jpg" alt="An example image">',
            'expected' => 'image: "An example image"',
        ),
        'With alt, and title tags'         => array(
            'html'     => '<img src="http://example.com/example.jpg" alt="An example image" title="Should be ignored">',
            'expected' => 'image: "An example image"',
        ),
        'With alt tag, wrapped in text'    => array(
            'html'     => 'xx <img src="http://example.com/example.jpg" alt="An example image"> xx',
            'expected' => 'xx image: "An example image" xx',
        ),
        'With italics'                     => array(
            'html'     => '<img src="shrek.jpg" alt="the ogrelord" /> Blah <i>blah</i> blah',
            'expected' => 'image: "the ogrelord" Blah _blah_ blah',
        ),
    );
  }

  /**
   * @dataProvider testImageDataProvider
   *
   * @param string $html
   * @param string $expected
   */
  public function testImages($html, $expected)
  {
    $html2text = new Html2Text($html);
    $output = $html2text->getText();

    self::assertEquals($expected, $output);
  }
}
