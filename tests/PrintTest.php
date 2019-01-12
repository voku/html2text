<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class PrintTest
 *
 * @internal
 */
final class PrintTest extends \PHPUnit\Framework\TestCase
{
    const EXPECTED = 'Hello, "WORLD"';

    const TEST_HTML = 'Hello, &quot;<b>world</b>&quot;';

    /**
     * @var Html2Text
     */
    protected $html;

    protected function setUp()
    {
        $this->html = new Html2Text(self::TEST_HTML);
        $this->expectOutputString(self::EXPECTED);
    }

    public function testPrintText()
    {
        echo $this->html->getText();
    }
}
