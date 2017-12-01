<?php

namespace Html2Text;

use voku\Html2Text\Html2Text;

/**
 * Class ImageTest
 *
 * @package Html2Text
 */
class ImageTest extends \PHPUnit\Framework\TestCase
{
  public function testShowAltText()
  {
    $html = new Html2Text("<img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt=\"This is our cool logo\" />\n    <br/>\n\n    <img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt='This is our cool logo' data-foo=\"bar\">");

    self::assertSame("Image: \"This is our cool logo\"\nImage: \"This is our cool logo\"", $html->getText());
  }

  public function testShowAltTextWithUtf8()
  {
    $html = '
    <img src="/img/background.jpg" alt="Computer Keyboard - Głównie JavaScript"/>
    <h1><a href="https://foobar">Głównie JavaScript</a></h1>
    ';

    $expected = "Image: \"Computer Keyboard - Głównie JavaScript\"\n\nGŁÓWNIE JAVASCRIPT [https://foobar]";

    $html = new Html2Text($html);

    self::assertSame($expected, $html->getText());
  }

  public function testEditImagePreText()
  {
    $html = new Html2Text("<img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt=\"This is our cool logo\" />\n    <br/>\n\n    <img id=\"head\" class=\"header\" src=\"imgs/logo.png\" alt='This is our cool logo' data-foo=\"bar\">");
    $html->setPrefixForImages('Bild: ');

    self::assertSame("Bild: \"This is our cool logo\"\nBild: \"This is our cool logo\"", $html->getText());
  }

  public function testComplexImageTagButWithoutAltContent()
  {
    $html = '<figure id="post-243293" class="align-none media-243293"><img src="https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface.png" alt="" srcset="https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface.png 668w, https://css-tricks.com/wp-content/uploads/2016/07/giphy-interface-239x300.png 239w" sizes="(max-width: 668px) 100vw, 668px"></figure>';
    $expected = '';

    $html = new Html2Text($html);
    $html->setPrefixForImages('Bild: ');

    self::assertSame($expected, $html->getText());
  }

  /**
   * @return array
   */
  public function imageDataProvider()
  {
    return [
        'Without alt tag'                  => [
            'html'     => '<img src="http://example.com/example.jpg">',
            'expected' => '',
        ],
        'Without alt tag, wrapped in text' => [
            'html'     => 'xx<img src="http://example.com/example.jpg">xx',
            'expected' => 'xxxx',
        ],
        'With alt tag'                     => [
            'html'     => '<img src="http://example.com/example.jpg" alt="An example image">',
            'expected' => 'Image: "An example image" [http://example.com/example.jpg]',
        ],
        'With alt, and title tags'         => [
            'html'     => '<img src="http://example.com/example.jpg" alt="An example image" title="Should be ignored">',
            'expected' => 'Image: "An example image" [http://example.com/example.jpg]',
        ],
        'With alt tag, wrapped in text'    => [
            'html'     => 'xx <img src="http://example.com/example.jpg" alt="An example image"> xx',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ],
        'With alt tag, wrapped in text v2' => [
            'html'     => 'xx<img src="http://example.com/example.jpg" alt="An example image">xx',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ],
        'With alt tag, wrapped in tags'    => [
            'html'     => '<span>xx</span><img src="http://example.com/example.jpg" alt="An example image"><span>xx</span>',
            'expected' => 'xx Image: "An example image" [http://example.com/example.jpg] xx',
        ],
        'With italics'                     => [
            'html'     => '<img src="shrek.jpg" alt="the ogrelord" /> Blah <i>blah</i> blah',
            'expected' => 'Image: "the ogrelord" Blah _blah_ blah',
        ],
    ];
  }

  /**
   * @dataProvider imageDataProvider
   *
   * @param string $html
   * @param string $expected
   */
  public function testImages($html, $expected)
  {
    $html2text = new Html2Text($html);
    $output = $html2text->getText();

    self::assertSame($expected, $output);
  }
}
