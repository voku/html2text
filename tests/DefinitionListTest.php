<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class DefinitionListTest
 *
 * @internal
 */
final class DefinitionListTest extends \PHPUnit\Framework\TestCase
{
    public function testDefinitionList()
    {
        $html = <<< EOT
<dl>
  <dt>Definition Term:</dt>
  <dd>Definition Description<dd>
</dl>
EOT;
        $expected = <<<EOT
  DEFINITION TERM:
  Definition Description
EOT;

        $html2text = new Html2Text($html);
        $output = $html2text->getText();

        static::assertSame($this->normalizeString($expected), $output);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function normalizeString($string)
    {
        return \str_replace(["\r\n", "\r"], "\n", $string);
    }
}
