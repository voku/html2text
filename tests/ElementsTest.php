<?php

namespace Html2Text;

use voku\Html2Text\Html2Text;

class ElementsTest extends \PHPUnit_Framework_TestCase
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
        array(
            'elements' => array(
                'h1' => array('case' => Html2Text::OPTION_NONE, 'prepend' => "\nAAA "),
                'h4' => array('case' => Html2Text::OPTION_NONE, 'append' => " BBB\n"),
                'h6' => array('case' => Html2Text::OPTION_NONE, 'prepend' => "\nAAA ", 'append' => " BBB\n"),
                'li' => array('prepend' => "\n\t- "),
            ),
        )
    );

    self::assertEquals($this->normalizeString($expected), $html2text->getText());
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
        array(
            'width'    => 0,
            'elements' => array(
                'h1' => array('case' => Html2Text::OPTION_NONE, 'replace' => array('AAA', 'BBB')),
                'li' => array('replace' => array('•', '')),
            ),
        )
    );

    self::assertEquals($this->normalizeString($expected), $html2text->getText());
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

    self::assertEquals($expected, $html2text->getText());
  }

  /**
   * @see testTrimSpaces
   * @return array
   */
  public function getSpacesData()
  {
    return array(
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), '<Strong>Bold with space: </Strong>Rest of text'),
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), '<STRONG>Bold with space: </STRONG>Rest of text'),
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), '<strong>Bold with space: </strong>Rest of text'),
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), '<b>Bold with space: </b>Rest of text'),
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), ' <p> <b>Bold with space: </b>Rest of text </p> '),
        array($this->normalizeString('BOLD WITH SPACE: Rest of text'), ' <p> <b>Bold    with  space:  </b>Rest  of    text  </p> '),
    );
  }

  protected function normalizeString($string)
  {
    return str_replace(array("\r\n", "\r"), "\n", $string);
  }
}
