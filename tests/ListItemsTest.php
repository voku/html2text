<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class ListItemsTest
 *
 * @package voku\Html2Text\tests
 */
class ListItemsTest extends \PHPUnit_Framework_TestCase
{

  public function testUnorderedList()
  {
    $input = <<<EOT
<ul>
    <li>one</li>
    <li>two</li>
    <li></li>
</ul>
EOT;
    $expected_output = <<<EOT
* one
* two
*
EOT;

    $html2text = new Html2Text($input);
    $output = $html2text->get_text();

    $this->assertEquals($expected_output, $output);
  }

  public function testOrderedList()
  {
    $input = <<<EOT
<ol>
    <li>one</li>
    <li>two</li>
    <li></li>
</ol>
<ol>
    <li>one</li>
    <li>two</li>
    <li></li>
</ol>
<ol>
    <li>one</li>
    <li>two</li>
    <li>three</li>
</ol>
EOT;
    $expected_output = <<<EOT
1. one
2. two
3.

1. one
2. two
3.

1. one
2. two
3. three
EOT;

    $html2text = new Html2Text($input);
    $output = $html2text->get_text();

    $this->assertEquals($expected_output, $output);
  }

  public function testLargeList()
  {
    $input = <<<EOT
<ol>
    <li>one</li>
    <li>two</li>
    <li>three</li>
    <li>four</li>
    <li>five</li>
    <li>six</li>
    <li>seven</li>
    <li>eight</li>
    <li>nine</li>
    <li>ten</li>
    <li>eleven</li>
</ol>
EOT;
    $expected_output = <<<EOT
1. one
2. two
3. three
4. four
5. five
6. six
7. seven
8. eight
9. nine
10. ten
11. eleven
EOT;

    $html2text = new Html2Text($input);
    $output = $html2text->get_text();

    $this->assertEquals($expected_output, $output);
  }

  public function testMultiLineUnorderedList()
  {
    $input = <<<EOT
<ul>
    <li>this is a really long line, and it should be split into two lines. let's hope it is</li>
    <li>two</li>
</ul>
EOT;
    $expected_output = <<<EOT
* this is a really long line, and it should be split into two lines.
let's hope it is
* two
EOT;

    $html2text = new Html2Text($input);
    $output = $html2text->get_text();

    $this->assertEquals($expected_output, $output);
  }

  public function testMultiLineOrderedList()
  {
    $input = <<<EOT
<ol>
    <li>this is a really long line, and it should be split into two lines. let's hope it is</li>
    <li>two</li>
</ol>
EOT;
    $expected_output = <<<EOT
1. this is a really long line, and it should be split into two
lines. let's hope it is
2. two
EOT;

    $html2text = new Html2Text($input);
    $output = $html2text->get_text();

    $this->assertEquals($expected_output, $output);
  }
}