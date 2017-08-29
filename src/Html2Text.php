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

  const OPTION_UPPERCASE = 'optionUppercase';
  const OPTION_LOWERCASE = 'optionLowercase';
  const OPTION_UCFIRST   = 'optionUcfirst';
  const OPTION_TITLE     = 'optionTitle';
  const OPTION_NONE      = 'optionNone';

  /**
   * @var array
   */
  private static $caseModeMapping = array(
      self::OPTION_LOWERCASE => \MB_CASE_LOWER,
      self::OPTION_UPPERCASE => \MB_CASE_UPPER,
      self::OPTION_UCFIRST   => \MB_CASE_LOWER,
      self::OPTION_TITLE     => \MB_CASE_TITLE,
  );

  /**
   * default options
   *
   * @var array
   */
  private static $defaultOptions = array(
      'do_links'        => 'inline',
      'do_links_ignore' => 'javascript:|mailto:|#',
      'width'           => 0,
      'elements'        => array(
          'h1'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n\n",
          ),
          'h2'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n\n",
          ),
          'h3'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n\n",
          ),
          'h4'     => array(
              'case'  => self::OPTION_UPPERCASE,
              'colon' => false,
          ),
          'h5'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n\n",
          ),
          'h6'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n\n",
          ),
          'th'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\t\t",
              'append'  => "\n",
          ),
          'strong' => array(
              'case' => self::OPTION_UPPERCASE,
          ),
          'b'      => array(
              'case' => self::OPTION_UPPERCASE,
          ),
          'dt'     => array(
              'case'    => self::OPTION_UPPERCASE,
              'prepend' => "\n\n",
              'append'  => "\n",
          ),
          'dd'     => array(
              'prepend' => "\t* ",
              'append'  => "\n",
          ),
          'code'   => array(
              'prepend' => "\n\n```",
              'append'  => "```\n\n",
          ),
          'del'    => array(
              'prepend' => '~~',
              'append'  => '~~',
          ),
          'li'     => array(
              'prepend' => "\t* ",
              'append'  => "\n",
          ),
          'i'      => array(
              'prepend' => '_',
              'append'  => '_',
          ),
          'em'     => array(
              'prepend' => '_',
              'append'  => '_',
          ),
          'pre'    => array(
              'prepend' => '',
              'append'  => '',
          ),
      ),
  );

  /**
   * List of preg* regular expression patterns to search / replace.
   *
   * @var array
   */
  protected static $searchReplaceArray = array(
    // Non-legal carriage return
    "/\r/"                                           => '',
    // Windows carriage return
    "/\r\n/"                                         => "\n",
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
    // <span class="_html2text_ignore">...</span>
    '/<span class="_html2text_ignore">.+?<\/span>/i' => '',
  );

  /**
   * List of preg* regular expression patterns to search for
   * and replace using callback function.
   *
   * @var array
   */
  protected static $callbackSearch = array(
    // <h1> / <h2> / <h3> / <h4> / <h5> / <h6> and </h1> / </h2> / </h3> / </h4> / </h5> / </h6>
    '/<(?<element>h[123456])(?: [^>]*)?>(?<value>.*?)<\/h[123456]>/i',
    // <p> and </p> with surrounding whitespace.
    '/[ ]*<(?<element>p)(?: [^>]*)?>(?<value>.*?)<\/p>[ ]*/si',
    // <li> and </li>
    '/<(?<element>li)\b[^>]*>(?<value>.*?)<\/li>/i',
    // <b> and </b>
    '/<(?<element>b)(?: [^>]*)?>(?<value>.*?)<\/b>/i',
    // <strong> and </strong>
    '/<(?<element>strong)(?: [^>]*)?>(?<value>.*?)<\/strong>/i',
    // <dt> and </dt>
    '/<(?<element>dt)(?: [^>]*)?>(?<value>.*?)<\/dt>/i',
    // <dd> and </dd>
    '/<(?<element>dd)(?: [^>]*)?>(?<value>.*?)<\/dd>/i',
    // <th> and </th>
    '/<(?<element>th)(?: [^>]*)?>(?<value>.*?)<\/th>/i',
    // <a href=""> and </a>
    '/<(?<element>a) [^>]*href=("|\')([^"\']+)\2([^>]*)>(.*?)<\/a>/i',
    // <i> and </i>
    '/<(?<element>i)(?: [^>]*)?>(?<value>.*?)<\/i>/i',
    // <em> and </em>
    '/<(?<element>em)(?: [^>]*)?>(?<value>.*?)<\/em>/i',
    // <del> and </del>
    '/<(?<element>del)(?: [^>]*)?>(?<value>.*?)<\/del>/i',
    // <code> and </code>
    '/<(?<element>code)(?: [^>]*)?>(?<value>.*?)<\/code>/i',
    // <br> with leading whitespace after the newline.
    '/<(?<element>br)[^>]*>[ ]*/i',
    // <img alt="" src="">
    '/<(?<element>img)(?:.*?)alt=["|\'](?<alt>.*?)["|\'](?:.*?)src=["|\'](?<src>.*?)["|\'](?:.*?)>/i',
    // <img src="" alt="">
    '/<(?<element>img)(?:.*?)src=["|\'](?<src>.*?)["|\'](?:.*?)alt=["|\'](?<alt>.*?)["|\'](?:.*?)>/i',
  );

  /**
   * List of preg* regular expression patterns to search / replace | helper.
   *
   * @var array
   */
  protected static $helperSearchReplaceArray = array(
    // TM symbol in win-1252
    '/&#153;/'  => '™',
    // m-dash in win-1252
    '/&#151;/'  => '—',
    // runs of spaces, post-handling
    '/&nbsp;/i' => '|+|_html2text_space|+|',
    // convert more spaces into one space
    '/[ ]{2,}/' => ' ',
  );

  /**
   * List of preg* regular expression patterns to search / replace | replace some placeholder at the end.
   *
   * @var array
   */
  protected $endSearchReplaceArray = array(
    // replace the image-placeholder-description
    '/\[\[_html2text_image\]\]/' => 'Image: ',
    // replace the link-list-placeholder-description
    '/\[\[_html2text_links\]\]/' => "\n\nLinks:\n------\n",
  );

  /**
   * Replace the default "\n\nLinks:\n------\n"-prefix for link-lists.
   *
   * @param string $string
   */
  public function setPrefixForLinks($string)
  {
    $this->endSearchReplaceArray['/\[\[_html2text_links\]\]/'] = $string;
  }

  /**
   * Replace the default "Image: "-prefix for images.
   *
   * @param string $string
   */
  public function setPrefixForImages($string)
  {
    $this->endSearchReplaceArray['/\[\[_html2text_image\]\]/'] = $string;
  }

  /**
   * List of preg* regular expression patterns to search for in PRE body.
   *
   * @var array
   */
  protected static $preSearchReplaceArray = array(
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
  protected $baseUrl = '';

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
   * Various configuration options. (able to be set in the constructor)
   *
   * ----------------------
   *
   * do_links:
   *
   * 'none'
   * 'inline' (show links inline)
   * 'markdown' (show links as markdown)
   * 'nextline' (show links on the next line)
   * 'table' (if a table of link URLs should be listed after the text.
   * 'bbcode' (show links as bbcode)
   *
   * ----------------------
   *
   * width:
   *
   * Maximum width of the formatted text, in columns.
   * Set this value to 0 (or less) to ignore word wrapping and not constrain text to a fixed-width column.
   *
   * ----------------------
   *
   * case:
   *
   * e.g.: "SeCtion Title"
   *
   * - uppercase: "SECTION TITLE" // Html2Text::OPTION_UPPERCASE
   * - lowercase: "section title" // Html2Text::OPTION_LOWERCASE
   * - ucfirst: "Section title" // Html2Text::OPTION_UCFIRST
   * - title: "Section Title" // Html2Text::OPTION_TITLE
   * - none: "SeCtion Title"  // Html2Text::OPTION_NONE
   * ---------------------->
   * colon:
   * add a colon at the end "Section Title:"
   * ---------------------->
   *
   * @var array
   */
  protected $options;

  /**
   * __construct
   *
   * @param string $html    source HTML
   * @param array  $options set configuration options
   */
  public function __construct($html = '', $options = array())
  {
    if (!is_array($options)) {
      // for backwards compatibility
      call_user_func_array(array($this, 'legacyConstruct'), func_get_args());
    } else {
      $this->html = $html;
      $this->options = array_replace_recursive(self::$defaultOptions, $options);
    }
  }

  /**
   * Set the source HTML.
   *
   * @param string $html HTML source content
   */
  public function setHtml($html)
  {
    $this->html = $html;
    $this->converted = false;
  }

  /**
   * Set the source HTML.
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
   * @deprecated
   */
  public function get_text()
  {
    return $this->getText();
  }

  /**
   * Returns the text, converted from HTML.
   *
   * @return string
   */
  public function getText()
  {
    if ($this->converted === false) {
      $this->convert();
    }

    return $this->text;
  }

  /**
   * Convert HTML into Text.
   */
  protected function convert()
  {
    $this->linkList = array();

    $endSearchReplaceArrayKeys = array_keys($this->endSearchReplaceArray);
    $endSearchReplaceArrayValues = array_values($this->endSearchReplaceArray);

    // Clean the string from non-UTF8 chars & remove UTF8-BOM & normalize whitespace.
    $text = UTF8::clean($this->html, true, true, false);

    $this->converter($text);

    // Normalize whitespace, once again.
    $text = UTF8::normalize_whitespace($text);

    // Add the link-list, if needed.
    if (count($this->linkList) > 0) {
      $text .= '[[_html2text_links]]';
      foreach ($this->linkList as $i => $url) {
        $text .= '[' . ($i + 1) . '] ' . $url . "\n";
      }
    }

    // Trim every line.
    $textArray = explode("\n", $text);
    array_walk($textArray, array('self', 'trimCallback'));
    $text = implode("\n", $textArray);

    // Convert "space"-replacer into space.
    $text = str_replace('|+|_html2text_space|+|', ' ', $text);

    // Replace some placeholder at the end.
    $text = preg_replace($endSearchReplaceArrayKeys, $endSearchReplaceArrayValues, $text);

    // Normalise empty lines.
    $text = preg_replace("/\n\s+\n/", "\n\n", $text);
    $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

    // If max length of line is defined, then use "wordwrap".
    if ($this->options['width'] > 0) {
      $text = UTF8::wordwrap($text, $this->options['width']);
    }

    // Remove leading/ending empty lines/spaces.
    $text = trim($text);

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
    $searchReplaceArrayKeys = array_keys(self::$searchReplaceArray);
    $searchReplaceArrayValues = array_values(self::$searchReplaceArray);

    $helperSearchReplaceArrayKeys = array_keys(self::$helperSearchReplaceArray);
    $helperSearchReplaceArrayValues = array_values(self::$helperSearchReplaceArray);

    // Convert <BLOCKQUOTE> tags. (before PRE!)
    $this->convertBlockquotes($text);

    // Convert <PRE> tags.
    $this->convertPre($text);

    // Collapse space between tags to avoid left over whitespace.
    $text = preg_replace('/>\s+</', '><', $text);

    // Convert special-chars like "&#39;" into plain-chars like "'".
    $text = htmlspecialchars_decode($text, ENT_QUOTES);

    // Run our defined tags search-and-replace.
    $text = preg_replace($searchReplaceArrayKeys, $searchReplaceArrayValues, $text);

    // Run our defined tags search-and-replace with callback.
    $text = preg_replace_callback(self::$callbackSearch, array($this, 'pregCallback'), $text);

    // Strip any other HTML tags.
    $text = preg_replace('/<(?:\/|!)?\w+[^>]*>|<!--.*?-->/s', '', $text);

    // Run our defined entities/characters search-and-replace.
    $text = preg_replace($helperSearchReplaceArrayKeys, $helperSearchReplaceArrayValues, $text);

    // Replace known html entities + UTF-8 codepoints.
    $text = UTF8::html_entity_decode($text);

    // Normalise empty lines.
    $text = preg_replace("/[\n]{3,}/", "\n\n", $text);

    // Remove empty lines at the beginning and ending of the converted html
    // e.g.: can be produced by eg. P tag on the beginning or at the ending
    $text = trim($text);
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

        // Convert preg offsets from bytes to characters.
        $m[1] = UTF8::strlen(substr($originalText, 0, $m[1]));

        if ($m[0][0] === '<' && $m[0][1] === '/') {
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
            $body = preg_replace('/((?:^|\n)>*)/', '\\1> ', $body);

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
   * Convert "<pre>"-tags.
   *
   * @param string $text
   */
  protected function convertPre(&$text)
  {
    static $preSearchReplaceArrayKeys = null;
    static $preSearchReplaceArrayVales = null;

    if ($preSearchReplaceArrayKeys === null) {
      $preSearchReplaceArrayKeys = ($preSearchReplaceArrayKeys === null ? array_keys(self::$preSearchReplaceArray) : $preSearchReplaceArrayKeys);
      $preSearchReplaceArrayVales = ($preSearchReplaceArrayVales === null ? array_values(self::$preSearchReplaceArray) : $preSearchReplaceArrayVales);
    }

    // Get the content of PRE element.
    while (preg_match('/<pre[^>]*>(.*)<\/pre>/ismU', $text, $matches)) {

      // Replace br tags with newlines to prevent the search-and-replace callback from killing whitespace.
      $this->preContent = preg_replace('/(<br\b[^>]*>)/i', "\n", $matches[1]);

      // Use options (append, prepend, ...) for the current "pre"-tag.
      $this->preContent = $this->convertElement('<pre>' . $this->preContent . '</pre>', 'pre');

      // Run our defined tags search-and-replace with callback.
      $this->preContent = preg_replace_callback(
          self::$callbackSearch,
          array($this, 'pregCallback'),
          $this->preContent
      );

      // convert the content
      $this->preContent = sprintf(
          '<div><br>%s<br></div>',
          preg_replace($preSearchReplaceArrayKeys, $preSearchReplaceArrayVales, $this->preContent)
      );

      // replace the content
      $text = str_replace($matches[0], $this->preContent, $text);

      // free some memory
      $this->preContent = '';
    }
  }

  /**
   * Sets a base URL to handle relative links.
   *
   * @param string $baseUrl
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }

  /**
   * Set the base-url.
   *
   * @param $baseUrl
   *
   * @deprecated
   */
  public function set_base_url($baseUrl)
  {
    $this->setBaseUrl($baseUrl);
  }

  /**
   * Callback function for array_walk use.
   *
   * @param $string
   */
  protected function trimCallback(&$string)
  {
    $string = trim($string);
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
    // init
    $element = strtolower($matches['element']);

    switch ($matches['element']) {
      case 'p':
        // Replace newlines with spaces.
        $para = str_replace("\n", ' ', $matches['value']);

        // Add trailing newlines for this para.
        return "\n\n" . $para . "\n\n";
      case 'img':

        $useSrc = (
            $matches['src']
            &&
            strpos($matches['src'], 'cid:') === false
            &&
            (
                strpos($matches['src'], 'http://') === 0
                ||
                strpos($matches['src'], 'https://') === 0
                ||
                strpos($matches['src'], '//') === 0
            )
        );

        if ($useSrc === true && $matches['alt'] && $matches['src']) {
          return ' [[_html2text_image]]"' . $matches['alt'] . '" [' . $matches['src'] . '] ';
        }

        if ($matches['alt']) {
          return ' [[_html2text_image]]"' . $matches['alt'] . '" ';
        }

        return '';
      case 'br':
        return "\n";
      case 'a':

        // override the link method
        $linkOverride = null;
        if (preg_match('/_html2text_link_(\w+)/', $matches[4], $linkOverrideMatch)) {
          $linkOverride = $linkOverrideMatch[1];
        }

        // remove spaces in URL (#1487805)
        $url = str_replace(' ', '', $matches[3]);

        return $this->buildLinkList($url, $matches[5], $linkOverride);
    }

    if (preg_match('/h[123456]/', $matches['element'])) {
      return $this->convertElement($matches['value'], $matches['element']);
    }

    if (array_key_exists($element, $this->options['elements'])) {
      return $this->convertElement($matches['value'], $matches['element']);
    }

    return '';
  }

  /**
   * Get options for the given element.
   *
   * @param $element
   *
   * @return array|null
   */
  private function getOptionsForElement($element)
  {
    // init
    $element = strtolower($element);

    if (!array_key_exists($element, $this->options['elements'])) {
      return null;
    }

    return $this->options['elements'][$element];
  }

  /**
   * @param string $str
   * @param string $element
   *
   * @return string
   */
  private function convertElement($str, $element)
  {
    $options = $this->getOptionsForElement($element);
    if (!$options) {
      return $str;
    }

    if (isset($options['case']) && $options['case'] != self::OPTION_NONE) {
      $mode = self::$caseModeMapping[$options['case']];

      // string can contain HTML tags
      $chunks = preg_split('/(<[^>]*>)/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

      // convert only the text between HTML tags
      foreach ($chunks as $i => &$chunk) {
        if ($chunk[0] !== '<') {
          $chunk = UTF8::html_entity_decode($str);
          $chunk = mb_convert_case($chunk, $mode, 'UTF-8');
          $chunk = htmlspecialchars_decode($chunk, ENT_QUOTES);
        }
      }

      $str = implode($chunks);

      if ($options['case'] == self::OPTION_UCFIRST) {
        $str = UTF8::ucfirst($str);
      }
    }

    if (isset($options['replace']) && $options['replace']) {

      if (isset($options['replace'][2])) {
        $delimiter = $options['replace'][2];
      } else {
        $delimiter = '@';
      }

      $str = preg_replace($delimiter . $options['replace'][0] . $delimiter, $options['replace'][1], $str);
    }


    if (isset($options['prepend']) && $options['prepend']) {
      $str = $options['prepend'] . $str;
    }

    if (isset($options['append']) && $options['append']) {
      $str .= $options['append'];
    }

    return $str;
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

    // ignored link types
    if (preg_match('!^(?:' . $this->options['do_links_ignore'] . ')!i', $link)) {
      return $display;
    }

    if ($linkMethod === 'none') {
      return ' ' . $display . ' ';
    }

    if (preg_match('!^(?:[a-z][a-z0-9.+-]+:)!i', $link)) {
      $url = $link;
    } else {
      $url = $this->baseUrl;
      if (UTF8::substr($link, 0, 1) !== '/') {
        $url .= '/';
      }
      $url .= $link;
    }

    if ($linkMethod === 'table') {

      //
      // table
      //
      if (($index = array_search($url, $this->linkList, true)) === false) {
        $index = count($this->linkList);
        $this->linkList[] = $url;
      }

      return ' ' . $display . ' [' . ($index + 1) . '] ';

    }

    if ($linkMethod === 'nextline') {

      // Shorten output when "target url" and "display url" match.
      if ($url === $display) {
        return ' ' . $url . ' ';
      }

      //
      // nextline
      //
      return ' ' . $display . "\n" . '[' . $url . '] ';

    }

    if ($linkMethod === 'markdown') {

      //
      // markdown
      //
      return ' [' . $display . '](' . $url . ') ';

    }

    if ($linkMethod === 'bbcode') {

      //
      // bbcode
      //
      return ' [url=' . $url . ']' . $display . '[/url] ';

    }

    // Shorten output when "target url" and "display url" match.
    if ($url === $display) {
      return ' ' . $url . ' ';
    }

    //
    // inline (default)
    //
    return ' ' . $display . ' [' . $url . '] ';
  }

  /**
   * Legacy construct helper.
   *
   * @param string     $html
   * @param bool|false $fromFile
   * @param array      $options
   */
  private function legacyConstruct($html = '', $fromFile = false, array $options = array())
  {
    $this->set_html($html, $fromFile);
    $this->options = array_replace_recursive(self::$defaultOptions, $options);
  }
}
