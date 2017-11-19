<?php

namespace voku\Html2Text\tests;

use \voku\Html2Text\Html2Text;

/**
 * Class SearchReplaceTest
 *
 * @package voku\Html2Text\tests
 */
class SearchReplaceTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @return array
   */
  public function searchReplaceDataProvider() {
    return array(
        'Bold' => array(
            'html'      => 'Hello, &quot;<b>world</b>&quot;!',
            'expected'  => 'Hello, "WORLD"!',
        ),
        'Strong' => array(
            'html'      => 'Hello, &quot;<strong>world</strong>&quot;!',
            'expected'  => 'Hello, "WORLD"!',
        ),
        'Italic' => array(
            'html'      => 'Hello, &quot;<i>world</i>&quot;!',
            'expected'  => 'Hello, "_world_"!',
        ),
        'Header' => array(
            'html'      => '<h1>Hello, world!</h1>',
            'expected'  => 'HELLO, WORLD!',
        ),
        'Table Header' => array(
            'html'      => '<th>Hello, World!</th>',
            'expected'  => 'HELLO, WORLD!',
        ),
        'Table Header and Content' => array(
            'html'      => '<h1>foo</h1><th>Hello, World!</th><br />test1<br />test2',
            'expected'  => "FOO\n\nHELLO, WORLD!\n\ntest1\ntest2",
        ),
    );
  }

  /**
   * @dataProvider searchReplaceDataProvider
   *
   * @param string $html
   * @param string $expected
   */
  public function testSearchReplace($html, $expected)
  {
    $html = new Html2Text($html);
    self::assertSame($expected, $html->getText());
  }
}
