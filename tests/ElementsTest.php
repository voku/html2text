<?php

namespace Html2Text;

use voku\Html2Text\Html2Text;

/**
 * Class ElementsTest
 *
 * @package Html2Text
 */
class ElementsTest extends \PHPUnit\Framework\TestCase
{

  public function testPrependAndAppend()
  {
    $html = <<<EOT
  <h1>Should have "AAA " prepended</h1>
  <h4>Should have " BBB" appended</h4>
  <h6>Should have "AAA " prepended and " BBB" appended</h6>
  <li>Dash instead of asterisk</li>
EOT;

    $expected = <<<EOT
AAA Should have "AAA " prepended

Should have " BBB" appended BBB

AAA Should have "AAA " prepended and " BBB" appended BBB

- Dash instead of asterisk
EOT;

    $html2text = new Html2Text(
        $html,
        [
            'elements' => [
                'h1' => ['case' => Html2Text::OPTION_NONE, 'prepend' => "\nAAA "],
                'h4' => ['case' => Html2Text::OPTION_NONE, 'append' => " BBB\n"],
                'h6' => ['case' => Html2Text::OPTION_NONE, 'prepend' => "\nAAA ", 'append' => " BBB\n"],
                'li' => ['prepend' => "\n\t- "],
            ],
        ]
    );

    self::assertSame($this->normalizeString($expected), $html2text->getText());
  }

  public function testReplace()
  {
    $html = <<<EOT
  <h1>Should have "AAA" changed to BBB</h1>
   <li>• Custom bullet should be removed</li>
EOT;

    $expected = <<<EOT
Should have "BBB" changed to BBB

* Custom bullet should be removed
EOT;

    $html2text = new Html2Text(
        $html,
        [
            'width'    => 0,
            'elements' => [
                'h1' => ['case' => Html2Text::OPTION_NONE, 'replace' => ['AAA', 'BBB']],
                'li' => ['replace' => ['•', '']],
            ],
        ]
    );

    self::assertSame($this->normalizeString($expected), $html2text->getText());

    // -----------

    $html = '<h1>Should have "AAA" changed to BBB</h1><ul><li>• Custom bullet should be removed</li></ul><img src="tux.png" alt="The Linux Tux" />';
    $expected = 'SHOULD HAVE "BBB" CHANGED TO BBB' . "\n\n" . '- Custom bullet should be removed |' . "\n\n" . '[IMAGE]: "The Linux Tux"';

    $html2text = new Html2Text(
        $html,
        [
            'width'    => 0,
            'elements' => [
                'h1' => [
                    'case'    => Html2Text::OPTION_UPPERCASE,
                    'replace' => ['AAA', 'BBB'],
                ],
                'li' => [
                    'case'    => Html2Text::OPTION_NONE,
                    'replace' => ['•', ''],
                    'prepend' => '- ',
                    'append'  => ' |',
                ],
            ],
        ]
    );

    $html2text->setPrefixForImages('[IMAGE]: ');
    self::assertSame($this->normalizeString($expected), $html2text->getText());
  }

  /**
   * @dataProvider getSpacesData
   *
   * @param $expected
   * @param $html
   */
  public function testTrimSpaces($expected, $html)
  {
    $html2text = new Html2Text($html);

    self::assertSame($expected, $html2text->getText());
  }

  /**
   * @see testTrimSpaces
   * @return array
   */
  public function getSpacesData()
  {
    return [
        [
            $this->normalizeString('BOLD WITH SPACE: Rest of text'),
            '<Strong>Bold with space: </Strong>Rest of text',
        ],
        [
            $this->normalizeString('BOLD WITH SPACE: Rest of text'),
            '<STRONG>Bold with space: </STRONG>Rest of text',
        ],
        [
            $this->normalizeString('BOLD WITH SPACE: Rest of text'),
            '<strong>Bold with space: </strong>Rest of text',
        ],
        [$this->normalizeString('BOLD WITH SPACE: Rest of text'), '<b>Bold with space: </b>Rest of text'],
        [
            $this->normalizeString('BOLD WITH SPACE: Rest of text'),
            ' <p> <b>Bold with space: </b>Rest of text </p> ',
        ],
        [
            $this->normalizeString('BOLD WITH SPACE: Rest of text'),
            ' <p> <b>Bold    with  space:  </b>Rest  of    text  </p> ',
        ],
    ];
  }

  /**
   * @param $string
   *
   * @return string
   */
  protected function normalizeString($string)
  {
    return str_replace(["\r\n", "\r"], "\n", $string);
  }
}
