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

    self::assertEquals("Image: \"This is our cool logo\"\nImage: \"This is our cool logo\"", $html->getText());
  }

  public function testEditImagePreText()
  {
    $html = new Html2Text("<img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt=\"This is our cool logo\" />\n    <br/>\n\n    <img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt='This is our cool logo' data-foo=\"bar\">");
    $html->setPrefixForImages('Bild: ');

    self::assertEquals("Bild: \"This is our cool logo\"\nBild: \"This is our cool logo\"", $html->getText());
  }

  public function testComplexImageTagButWithoutAltContent()
  {
    $html = '<figure id="post-243293" class="align-none media-243293"><img src="https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface.png" alt="" srcset="https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface.png 668w, https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface-239x300.png 239w" sizes="(max-width: 668px) 100vw, 668px"></figure>';
    $expected = '';

    $html = new Html2Text($html);
    $html->setPrefixForImages('Bild: ');

    self::assertEquals($expected, $html->getText());
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
            'expected' => 'Image: "An example image" [http://example.com/example.jpg]',
        ),
        'With alt, and title tags'         => array(
            'html'     => '<img src="http://example.com/example.jpg" alt="An example image" title="Should be ignored">',
            'expected' => 'Image: "An example image" [http://example.com/example.jpg]',
        ),
        'With alt tag, wrapped in text'    => array(
            'html'     => 'xx <img src="http://example.com/example.jpg" alt="An example image"> xx',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ),
        'With alt tag, wrapped in text v2'    => array(
            'html'     => 'xx<img src="http://example.com/example.jpg" alt="An example image">xx',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ),
        'With alt tag, wrapped in tags'    => array(
            'html'     => '<span>xx</span><img src="http://example.com/example.jpg" alt="An example image"><span>xx</span>',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ),
        'With italics'                     => array(
            'html'     => '<img src="shrek.jpg" alt="the ogrelord" /> Blah <i>blah</i> blah',
            'expected' => 'Image: "the ogrelord" Blah _blah_ blah',
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
