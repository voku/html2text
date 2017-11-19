<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class PrintTest
 *
 * @package Html2Text
 */
class PrintTest extends \PHPUnit\Framework\TestCase
{
  const TEST_HTML = 'Hello, &quot;<b>world</b>&quot;';
  const EXPECTED  = 'Hello, "WORLD"';

  /**
   * @var Html2Text
   */
  protected $html;

  public function setUp()
  {
    $this->html = new Html2Text(self::TEST_HTML);
    $this->expectOutputString(self::EXPECTED);
  }

  public function testPrint_text()
  {
    echo $this->html->getText();
  }
}
