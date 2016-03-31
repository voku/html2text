<?php

namespace voku\Html2Text;

use voku\helper\UTF8;

/**
 * Class Html2Text
 *
 * Copyright (c) 2005-2007 Jon Abernathy <jon@chuggnutt.com>
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @package Html2Text
 */
class Html2Text
{

  /**
   * Contains the HTML content to convert.
   *
   * @var string
   */
  protected $html;

  /**
   * Contains the converted, formatted text.
   *
   * @var string
   */
  protected $text;

  /**
   * List of preg* regular expression patterns to search / replace
   *
   * @var array
   */
  protected $searchReplaceArray = array(
    // Non-legal carriage return
    "/\r/"                                           => '',
    // Newlines and tabs
    "/[\n\t]+/"                                      => ' ',
    // <head>
    '/<head\b[^>]*>.*?<\/head>/i'                    => '',
    // <script>s -- which strip_tags supposedly has problems with
    '/<script\b[^>]*>.*?<\/script>/i'                => '',
    // <style>s -- which strip_tags supposedly has problems with
    '/<style\b[^>]*>.*?<\/style>/i'                  => '',
    // <ul> and </ul>
    '/(<ul\b[^>]*>|<\/ul>)/i'                        => "\n\n",
    // <ol> and </ol>
    '/(<ol\b[^>]*>|<\/ol>)/i'                        => "\n\n",
    // <dl> and </dl>
    '/(<dl\b[^>]*>|<\/dl>)/i'                        => "\n\n",
    // <li> and </li>
    '/<li\b[^>]*>(.*?)<\/li>/i'                      => "* \\1\n",
    // <dd> and </dd>
    '/<dd\b[^>]*>(.*?)<\/dd>/i'                      => "\\1\n",
    // <dt> and </dt>
    '/<dt\b[^>]*>(.*?)<\/dt>/i'                      => '* \\1',
    // <li>
    '/<li\b[^>]*>/i'                                 => "\n* ",
    // <hr>
    '/<hr\b[^>]*>/i'                                 => "\n-------------------------\n",
    // <div>
    '/<div\b[^>]*>/i'                                => "<div>\n",
    // <table> and </table>
    '/(<table\b[^>]*>|<\/table>)/i'                  => "\n\n",
    // <tr> and </tr>
    '/(<tr\b[^>]*>|<\/tr>)/i'                        => "\n",
    // <td> and </td>
    '/<td\b[^>]*>(.*?)<\/td>/i'                      => "\\1\n",
    // img alt text
    '/<img(?:.*?)alt=("|\')(.*?)("|\')(?:.*?)>/i'    => 'image: \\1\\2\\3',
    // ... remove empty images ...
    '/image: ""/'                                    => '',
    // <span class="_html2text_ignore">...</span>
    '/<span class="_html2text_ignore">.+?<\/span>/i' => '',
  );

  /**
   * List of preg* regular expression patterns to search / replace
   *
   * @var array
   */
  protected $endSearchReplaceArray = array(
    // TM symbol in win-1252
    '/&#153;/i'      => '™',
    // m-dash in win-1252
    '/&#151;/i'      => '—',
    // ampersand: see converter()
    '/&(amp|#38);/i' => '|+|amp|+|',
    // runs of spaces, post-handling
    '/[ ]{2,}/'      => ' ',
  );

  /**
   * List of preg* regular expression patterns to search for
   * and replace using callback function.
   *
   * @var array
   */
  protected $callbackSearch = array(
      '/<(h)[123456]( [^>]*)?>(.*?)<\/h[123456]>/i',           // h1 - h6
      '/[ ]*<(p)( [^>]*)?>(.*?)<\/p>[ ]*/si',                  // <p> with surrounding whitespace.
      '/<(br)[^>]*>[ ]*/i',                                    // <br> with leading whitespace after the newline.
      '/<(b)( [^>]*)?>(.*?)<\/b>/i',                           // <b>
      '/<(strong)( [^>]*)?>(.*?)<\/strong>/i',                 // <strong>
      '/<(th)( [^>]*)?>(.*?)<\/th>/i',                         // <th> and </th>
      '/<(a) [^>]*href=("|\')([^"\']+)\2([^>]*)>(.*?)<\/a>/i', // <a href="">
      '/<(i)( [^>]*)?>(.*?)<\/i>/i',                           // <i>
      '/<(em)( [^>]*)?>(.*?)<\/em>/i',                         // <em>
  );

  /**
   * List of preg* regular expression patterns to search for in PRE body
   *
   * @var array
   */
  protected $preSearchReplaceArray = array(
      "/\n/"           => '<br>',
      "/\t/"           => '&nbsp;&nbsp;',
      '/ /'            => '&nbsp;',
      '/<pre\b[^>]*>/' => '',
      '/<\/pre>/'      => '',
  );

  /**
   * Temporary workspace used during PRE processing.
   *
   * @var string
   */
  protected $preContent = '';

  /**
   * Contains the base URL that relative links should resolve to.
   *
   * @var string
   */
  protected $baseurl = '';

  /**
   * Indicates whether content in the $html variable has been converted yet.
   *
   * @var boolean
   * @see $html, $text
   */
  protected $converted = false;

  /**
   * Contains URL addresses from links to be rendered in plain text.
   *
   * @var array
   * @see buildLinkList()
   */
  protected $linkList = array();

  /**
   * Various configuration options (able to be set in the constructor)
   *
   * @var array
   */
  protected $options = array(
    //
    // "do_upper" ------------>
    //
    // Convert strong and bold to uppercase?
    'do_upper'       => true,
    //
    // "do_underscores" ------------>
    //
    // Surround emphasis and italics with underscores?
    'do_underscores' => true,
    //
    // "do_links ------------>
    //
    // 'none'
    // 'inline' (show links inline)
    // 'nextline' (show links on the next line)
    // 'table' (if a table of link URLs should be listed after the text.
    // 'bbcode' (show links as bbcode)
    'do_links'       => 'inline',
    //
    // "width ------------>
    //
    //  Maximum width of the formatted text, in columns.
    //  Set this value to 0 (or less) to ignore word wrapping
    //  and not constrain text to a fixed-width column.
    'width'          => 70,
  );

  /**
   * __construct
   *
   * @param string $html    Source HTML
   * @param array  $options Set configuration options
   */
  public function __construct($html = '', $options = array())
  {
    if (!is_array($options)) {
      // for backwards compatibility
      call_user_func_array(array($this, 'legacyConstruct'), func_get_args());
    } else {
      $this->html = $html;
      $this->options = array_merge($this->options, $options);
    }
  }

  /**
   * Set the source HTML
   *
   * @param string $html HTML source content
   */
  public function setHtml($html)
  {
    $this->html = $html;
    $this->converted = false;
  }

  /**
   * set_html
   *
   * @param string     $html
   * @param bool|false $from_file
   *
   * @deprecated
   */
  public function set_html($html, $from_file = false)
  {
    if ($from_file) {
      throw new \InvalidArgumentException('Argument from_file no longer supported');
    }

    $this->setHtml($html);
  }

  /**
   * @deprecated
   */
  public function p()
  {
    $this->print_text();
  }

  /**
   * @deprecated
   */
  public function print_text()
  {
    print $this->getText();
  }

  /**
   * Returns the text, converted from HTML.
   *
   * @return string
   */
  public function getText()
  {
    if (!$this->converted) {
      $this->convert();
    }

    return $this->text;
  }

  /**
   * @deprecated
   */
  public function get_text()
  {
    return $this->getText();
  }

  /**
   * convert HTML into Text
   */
  protected function convert()
  {
    $this->linkList = array();

    // clean the string from non-UTF8 chars
    // & remove UTF8-BOM
    // & normalize whitespace
    $text = UTF8::clean($this->html, true, true, false);

    $text = UTF8::trim($text);

    $this->converter($text);

    if (count($this->linkList) > 0) {
      $text .= "\n\nLinks:\n------\n";
      foreach ($this->linkList as $i => $url) {
        $text .= '[' . ($i + 1) . '] ' . $url . "\n";
      }
    }

    // normalize whitespace, again
    $text = UTF8::normalize_whitespace($text);

    // don't use tabs
    $text = preg_replace("/\t/", '  ', $text);

    // trim every line
    $textArray = explode("\n", $text);
    array_walk($textArray, array('self', 'trimCallback'));
    $text = implode("\n", $textArray);

    // convert "space"-replacer into space
    $text = str_replace('|+|space|+|', ' ', $text);

    // remove leading/ending empty lines
    $text = UTF8::trim($text, "\n");

    $this->text = $text;

    $this->converted = true;
  }

  /**
   * converter
   *
   * @param string $text
   */
  protected function converter(&$text)
  {
    static $searchReplaceArrayKeys = null;
    static $searchReplaceArrayValues = null;

    static $endSearchReplaceArrayKeys = null;
    static $endSearchReplaceArrayValues = null;

    $searchReplaceArrayKeys = ($searchReplaceArrayKeys === null ? array_keys($this->searchReplaceArray) : $searchReplaceArrayKeys);
    $searchReplaceArrayValues = ($searchReplaceArrayValues === null ? array_values($this->searchReplaceArray) : $searchReplaceArrayValues);

    $endSearchReplaceArrayKeys = ($endSearchReplaceArrayKeys === null ? array_keys($this->endSearchReplaceArray) : $endSearchReplaceArrayKeys);
    $endSearchReplaceArrayValues = ($endSearchReplaceArrayValues === null ? array_values($this->endSearchReplaceArray) : $endSearchReplaceArrayValues);

    // convert <BLOCKQUOTE> (before PRE!)
    $this->convertBlockquotes($text);

    // convert <PRE>
    $this->convertPre($text);

    // run our defined tags search-and-replace
    $text = preg_replace($searchReplaceArrayKeys, $searchReplaceArrayValues, $text);

    // run our defined tags search-and-replace with callback
    $text = preg_replace_callback($this->callbackSearch, array($this, 'pregCallback'), $text);

    // strip any other HTML tags
    $text = preg_replace('/(<(\/|!)?\w+[^>]*>)|(<!--.*?-->)/s', '', $text);

    // run our defined entities/characters search-and-replace
    $text = preg_replace($endSearchReplaceArrayKeys, $endSearchReplaceArrayValues, $text);

    // replace known html entities
    $text = UTF8::html_entity_decode($text);

    // replace html entities which represent UTF-8 codepoints.
    $text = preg_replace_callback("/&#\d{2,5};/", array($this, 'entityCallback'), $text);

    // remove unknown/unhandled entities (this cannot be done in search-and-replace block)
    $text = preg_replace('/&[a-zA-Z0-9]{2,6};/', '', $text);

    // convert "|+|amp|+|" into "&", need to be done after handling of unknown entities
    // this properly handles situation of "&amp;quot;" in input string
    $text = str_replace('|+|amp|+|', '&', $text);

    // normalise empty lines
    $text = preg_replace("/\n\s+\n/", "\n\n", $text);
    $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

    // remove empty lines at the beginning and ending of the converted html
    // e.g.: can be produced by eg. P tag on the beginning or at the ending
    $text = UTF8::trim($text, "\n");

    if ($this->options['width'] > 0) {
      $text = wordwrap($text, $this->options['width']);
    }
  }

  /**
   * Helper function for BLOCKQUOTE body conversion.
   *
   * @param string $text HTML content
   */
  protected function convertBlockquotes(&$text)
  {
    if (preg_match_all('/<\/*blockquote[^>]*>/i', $text, $matches, PREG_OFFSET_CAPTURE)) {

      // init
      $originalText = $text;
      $start = 0;
      $taglen = 0;
      $level = 0;
      $diff = 0;

      foreach ($matches[0] as $m) {

        // convert preg offsets from bytes to characters
        $m[1] = UTF8::strlen(substr($originalText, 0, $m[1]));

        if ($m[0][0] == '<' && $m[0][1] == '/') {
          $level--;

          if ($level < 0) {
            // malformed HTML: go to next blockquote
            $level = 0;
          } elseif ($level > 0) {
            // skip inner blockquote
          } else {
            $end = $m[1];

            $len = $end - $taglen - $start;

            // get blockquote content
            $body = UTF8::substr($text, $start + $taglen - $diff, $len);

            // set text width
            $pWidth = $this->options['width'];
            if ($this->options['width'] > 0) {
              $this->options['width'] -= 2;
            }

            // convert blockquote content
            $this->converter($body);

            // add citation markers
            $body = preg_replace('/((^|\n)>*)/', '\\1> ', UTF8::trim($body));

            // create PRE block
            $body = '<pre>' . UTF8::htmlspecialchars($body) . '</pre>';

            // re-set text width
            $this->options['width'] = $pWidth;

            // replace content
            $text = UTF8::substr($text, 0, $start - $diff) . $body . UTF8::substr($text, $end + UTF8::strlen($m[0]) - $diff);

            $diff += $len + $taglen + UTF8::strlen($m[0]) - UTF8::strlen($body);
            unset($body);
          }
        } else {
          if ($level == 0) {
            $start = $m[1];

            $taglen = UTF8::strlen($m[0]);
          }
          $level++;
        }
      }
    }
  }

  /**
   * convert "<pre>"-tags
   *
   * @param string $text
   */
  protected function convertPre(&$text)
  {
    static $preSearchReplaceArrayKeys = null;
    static $preSearchReplaceArrayVales = null;

    $preSearchReplaceArrayKeys = ($preSearchReplaceArrayKeys === null ? array_keys($this->preSearchReplaceArray) : $preSearchReplaceArrayKeys);
    $preSearchReplaceArrayVales = ($preSearchReplaceArrayVales === null ? array_values($this->preSearchReplaceArray) : $preSearchReplaceArrayVales);

    // get the content of PRE element
    while (preg_match('/<pre[^>]*>(.*)<\/pre>/ismU', $text, $matches)) {
      // Replace br tags with newlines to prevent the search-and-replace callback from killing whitespace.
      $this->preContent = preg_replace('/(<br\b[^>]*>)/i', "\n", $matches[1]);

      // run our defined tags search-and-replace with callback
      $this->preContent = preg_replace_callback(
          $this->callbackSearch,
          array($this, 'pregCallback'),
          $this->preContent
      );

      // prevent html2text from trimming some spaces
      $this->preContent = str_replace(' ', '|+|space|+|', $this->preContent);

      // convert the content
      $this->preContent = sprintf(
          '<div><br>%s<br></div>',
          preg_replace($preSearchReplaceArrayKeys, $preSearchReplaceArrayVales, $this->preContent)
      );

      // replace the content (use callback because content can contain $0 variable)
      $text = preg_replace_callback(
          '/<pre[^>]*>.*<\/pre>/ismU',
          array($this, 'pregPreCallback'),
          $text,
          1
      );

      // free memory
      $this->preContent = '';
    }
  }

  /**
   * Sets a base URL to handle relative links.
   *
   * @param string $baseurl
   */
  public function setBaseUrl($baseurl)
  {
    $this->baseurl = $baseurl;
  }

  /**
   * set base-url
   *
   * @param $baseurl
   *
   * @deprecated
   */
  public function set_base_url($baseurl)
  {
    $this->setBaseUrl($baseurl);
  }

  /**
   * Callback function for array_walk use.
   *
   * @param $string
   */
  protected function trimCallback(&$string)
  {
    $string = UTF8::trim($string);
  }

  /**
   * Callback function for preg_replace_callback use.
   *
   * @param  array $matches PREG matches
   *
   * @return string
   */
  protected function pregCallback($matches)
  {
    switch (UTF8::strtolower($matches[1])) {
      case 'p':
        // Replace newlines with spaces.
        $para = str_replace("\n", ' ', $matches[3]);
        // Trim trailing and leading whitespace within the tag.
        $para = trim($para);
        // Add trailing newlines for this para.
        return "\n\n" . $para . "\n\n";
      case 'br':
        return "\n";
      case 'b':
      case 'strong':
        return $this->toupper($matches[3]);
      case 'th':
        return $this->toupper($matches[3] . "\n");
      case 'h':
        return $this->toupper("\n\n" . $matches[3] . "\n\n");
      case 'i':
      case 'em':
        $subject = $matches[3];
        if ($this->options['do_underscores'] === true) {
          $subject = '_' . $subject . '_';
        }
        return $subject;
      case 'a':

        // override the link method
        $linkOverride = null;
        if (preg_match('/_html2text_link_(\w+)/', $matches[4], $linkOverrideMatch)) {
          $linkOverride = $linkOverrideMatch[1];
        }

        // remove spaces in URL (#1487805)
        $url = str_replace(' ', '', $matches[3]);

        return $this->buildLinkList($url, $matches[5], $linkOverride);
      default:
        return '';
    }
  }

  /**
   * Callback function for preg_replace_callback use.
   *
   * @param  array $matches PREG matches
   *
   * @return string
   */
  protected function entityCallback(&$matches)
  {
    // Convert from HTML-ENTITIES to UTF-8
    return mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
  }

  /**
   * "strtoupper" function with HTML tags and entities handling.
   *
   * @param  string $str Text to convert
   *
   * @return string Converted text
   */
  protected function toupper($str)
  {
    if ($this->options['do_upper'] !== true) {
      return $str;
    }

    // string can contain HTML tags
    $chunks = preg_split('/(<[^>]*>)/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    // convert toupper only the text between HTML tags
    foreach ($chunks as $i => &$chunk) {
      if ($chunk[0] != '<') {
        $chunk = UTF8::strtoupper($chunk);
      }
    }

    return implode($chunks);
  }

  /**
   * Helper function called by preg_replace() on link replacement.
   *
   * Maintains an internal list of links to be displayed at the end of the
   * text, with numeric indices to the original point in the text they
   * appeared. Also makes an effort at identifying and handling absolute
   * and relative links.
   *
   * @param  string      $link    URL of the link
   * @param  string      $display Part of the text to associate number with
   * @param  string|null $linkOverride
   *
   * @return string
   */
  protected function buildLinkList($link, $display, $linkOverride = null)
  {
    if ($linkOverride) {
      $linkMethod = $linkOverride;
    } else {
      $linkMethod = $this->options['do_links'];
    }

    if ($linkMethod == 'none') {
      return $display;
    }

    // ignored link types
    if (preg_match('!^(javascript:|mailto:|#)!i', $link)) {
      return $display;
    }

    if (preg_match('!^([a-z][a-z0-9.+-]+:)!i', $link)) {
      $url = $link;
    } else {
      $url = $this->baseurl;
      if (UTF8::substr($link, 0, 1) != '/') {
        $url .= '/';
      }
      $url .= $link;
    }

    if ($linkMethod == 'table') {
      if (($index = array_search($url, $this->linkList, true)) === false) {
        $index = count($this->linkList);
        $this->linkList[] = $url;
      }

      return $display . ' [' . ($index + 1) . ']';
    } elseif ($linkMethod == 'nextline') {
      return $display . "\n[" . $url . ']';
    } elseif ($linkMethod == 'bbcode') {
      return '[url=' . $url . ']' . $display . '[/url]';
    } else {
      // link_method defaults to inline
      return $display . ' [' . $url . ']';
    }
  }

  /**
   * Callback function for preg_replace_callback use in PRE content handler.
   *
   * @return string
   */
  protected function pregPreCallback()
  {
    return $this->preContent;
  }

  /**
   * legacy construct helper
   *
   * @param string     $html
   * @param bool|false $fromFile
   * @param array      $options
   */
  private function legacyConstruct($html = '', $fromFile = false, array $options = array())
  {
    $this->set_html($html, $fromFile);
    $this->options = array_merge($this->options, $options);
  }
}
