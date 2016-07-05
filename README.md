[![Build Status](https://travis-ci.org/voku/html2text.svg?branch=master)](https://travis-ci.org/voku/html2text)
[![codecov.io](http://codecov.io/github/voku/html2text/coverage.svg?branch=master)](http://codecov.io/github/voku/html2text?branch=master)
[![Coverage Status](https://coveralls.io/repos/voku/html2text/badge.svg)](https://coveralls.io/r/voku/html2text)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/html2text/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/html2text/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/d9030665de184a309797b32e036a2f77)](https://www.codacy.com/app/voku/html2text)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe/mini.png)](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe)
[![Dependency Status](https://www.versioneye.com/user/projects/55a91f3e306535002000013c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a91f3e306535002000013c)
[![Reference Status](https://www.versioneye.com/php/voku:html2text/reference_badge.svg?style=flat)](https://www.versioneye.com/php/voku:html2text/references)
[![Latest Stable Version](https://poser.pugx.org/voku/html2text/v/stable)](https://packagist.org/packages/voku/html2text) 
[![Total Downloads](https://poser.pugx.org/voku/html2text/downloads)](https://packagist.org/packages/voku/html2text) 
[![Latest Unstable Version](https://poser.pugx.org/voku/html2text/v/unstable)](https://packagist.org/packages/voku/html2text)
[![PHP 7 ready](http://php7ready.timesplinter.ch/voku/html2text/badge.svg)](https://travis-ci.org/voku/html2text)
[![License](https://poser.pugx.org/voku/html2text/license)](https://packagist.org/packages/voku/html2text)

# Html2Text

WARNING: this is only a Maintained-Fork of "https://github.com/mtibben/html2text/"

A PHP library for converting HTML to formatted plain text.

## Installation

The recommended installation way is through [Composer](https://getcomposer.org).

```bash
$ composer require voku/html2text
```

## Basic Usage
```php
$html = new \voku\Html2Text\Html2Text('Hello, &quot;<b>world</b>&quot;');

echo $html->getText();  // Hello, "WORLD"
```

## Extended Usage

Each element (h1, li, div, etc) can have the following options:

* 'case' => convert case (```Html2Text::OPTION_NONE, Html2Text::OPTION_UPPERCASE, Html2Text::OPTION_LOWERCASE , Html2Text::OPTION_UCFIRST, Html2Text::OPTION_TITLE```)
* 'prepend' => prepend a string
* 'append' => append a string

For example:
```php
$html = '<h1>Should have "AAA" changed to BBB</h1><ul><li>• Custom bullet should be removed</li></ul><img alt="The Linux Tux" src="tux.png" />';
$expected = 'SHOULD HAVE "BBB" CHANGED TO BBB' . "\n\n" . '- Custom bullet should be removed |' . "\n\n" . '[IMAGE]: "The Linux Tux"';

$html2text = new Html2Text(
    $html,
    array(
        'width'    => 0,
        'elements' => array(
            'h1' => array(
              'case' => Html2Text::OPTION_UPPERCASE, 
              'replace' => array('AAA', 'BBB')),
            'li' => array(
              'case' => Html2Text::OPTION_NONE, 
              'replace' => array('•', ''), 
              'prepend' => "- ",
              'append' => " |",
            ),
        ),
    )
);

$html2text->setPrefixForImages('[IMAGE]: ');
$html2text->setPrefixForLinks('[LINKS]: ');
$html2text->getText(); // === $expected
```

## Live Demo
- [HTML](https://suckup.de/2016/01/was-habe-ich-als-fachinformatiker-bisher-gelernt/) | [TEXT](https://moelleken.org/url_to_text.php?url=https://suckup.de/2016/01/was-habe-ich-als-fachinformatiker-bisher-gelernt/)

- https://moelleken.org/url_to_text.php?url=https://ADD_YOUR_URL_HERE

## History

This library started life on the blog of Jon Abernathy http://www.chuggnutt.com/html2text

A number of projects picked up the library and started using it - among those was RoundCube mail. They made a number of updates to it over time to suit their webmail client.

Now it has been extracted as a standalone library. Hopefully it can be of use to others.
