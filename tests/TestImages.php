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
    $html = new Html2Text('<img id="head" class="header" src="imgs/logo.png" alt="This is our cool logo" />
    <br/>
    <img id="head" class="header" src="imgs/logo.png" alt=\'This is our cool logo\' data-foo="bar">
    ');

    $this->assertEquals('image: "This is our cool logo"
image: \'This is our cool logo\'', $html->getText());
  }
}
