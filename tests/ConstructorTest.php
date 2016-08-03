<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class ConstructorTest
 *
 * @package Html2Text
 */
class ConstructorTest extends \PHPUnit_Framework_TestCase
{
  public function testConstructor()
  {
    $html = 'Foo';
    $options = array('do_links' => 'none');
    $html2text = new Html2Text($html, $options);
    self::assertSame($html, $html2text->getText());

    $html2text = new Html2Text($html);
    self::assertSame($html, $html2text->getText());
  }

  public function testLegacyConstructor()
  {
    $html = 'Foo';
    $options = array('do_links' => 'none');

    $html2text = new Html2Text($html, false, $options);
    self::assertSame($html, $html2text->getText());
  }

  public function testLegacyConstructorThrowsExceptionWhenFromFileIsTrue()
  {
    $html = 'Foo';
    $options = array('do_links' => 'none');

    $this->setExpectedException('InvalidArgumentException');
    /** @noinspection PhpUnusedLocalVariableInspection */
    $html2text = new Html2Text($html, true, $options);
  }
}
