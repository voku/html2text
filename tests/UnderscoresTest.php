<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class UnderscoresTest
 *
 * @internal
 */
final class UnderscoresTest extends \PHPUnit\Framework\TestCase
{
    public function testNoUnderscores()
    {
        $html = <<<'EOT'
<html>
  <body>
    <p>An <i>extremely</i> important <em>emphasis</em>.</p>
  </body>
</html>
EOT;

        $expected = <<<'EOT'
An _extremely_ important _emphasis_.
EOT;

        $html2text = new Html2Text($html);
        static::assertSame($expected, $html2text->getText());
    }

    public function testUnderscores()
    {
        $html = <<<'EOT'
<html>
  <body>
    <p>An <i>extremely</i> important <em>emphasis</em>.</p>
  </body>
</html>
EOT;

        $expected = <<<'EOT'
An _extremely_ important _emphasis_.
EOT;

        $html2text = new Html2Text($html);
        static::assertSame($expected, $html2text->getText());
    }
}
