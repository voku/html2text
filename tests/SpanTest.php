<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class SpanTest
 *
 * @internal
 */
final class SpanTest extends \PHPUnit\Framework\TestCase
{
    public function testIgnoreSpans()
    {
        $html = <<< EOT
Outside<span class="_html2text_ignore">Inside</span>
EOT;
        $expected = <<<EOT
Outside
EOT;

        $html2text = new Html2Text($html);
        $output = $html2text->getText();

        static::assertSame($expected, $output);
    }
}
