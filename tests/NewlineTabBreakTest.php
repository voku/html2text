<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class NewlineTabBreakTest
 *
 * @internal
 */
final class NewlineTabBreakTest extends \PHPUnit\Framework\TestCase
{
    public function testNewlineTabBreak()
    {
        $html = <<<EOT
<p>Will be a line</p>
<p>Will be 
a line</p>
<p>
    This is some text
    all on one line
</p>
<p>
    This is some text<br/>
    with a break in the middle
 </p>
<p>This is some text<br/>with a break in the middle but no indent</p>
EOT;
        $expected = <<<EOT
Will be a line

Will be a line

This is some text all on one line

This is some text
with a break in the middle

This is some text
with a break in the middle but no indent
EOT;

        $html2text = new Html2Text($html);
        static::assertSame(\str_replace(["\n", "\r\n", "\r"], "\n", $expected), $html2text->getText());
    }
}
