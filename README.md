[![Build Status](https://travis-ci.org/voku/html2text.svg?branch=master)](https://travis-ci.org/voku/html2text)
[![codecov.io](http://codecov.io/github/voku/html2text/coverage.svg?branch=master)](http://codecov.io/github/voku/html2text?branch=master)
[![Coverage Status](https://coveralls.io/repos/voku/html2text/badge.svg)](https://coveralls.io/r/voku/html2text)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/html2text/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/html2text/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/d9030665de184a309797b32e036a2f77)](https://www.codacy.com/app/voku/html2text)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe/mini.png)](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe)
[![Dependency Status](https://www.versioneye.com/user/projects/55a91f3e306535002000013c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a91f3e306535002000013c)
[![Reference Status](https://www.versioneye.com/php/voku:html2text/reference_badge.svg?style=flat)](https://www.versioneye.com/php/voku:html2text/references)
[![Latest Stable Version](https://poser.pugx.org/voku/html2text/v/stable)](https://packagist.org/packages/voku/html2text) [![Total Downloads](https://poser.pugx.org/voku/html2text/downloads)](https://packagist.org/packages/voku/html2text) [![Latest Unstable Version](https://poser.pugx.org/voku/html2text/v/unstable)](https://packagist.org/packages/voku/html2text)
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

## History

This library started life on the blog of Jon Abernathy http://www.chuggnutt.com/html2text

A number of projects picked up the library and started using it - among those was RoundCube mail. They made a number of updates to it over time to suit their webmail client.

Now it has been extracted as a standalone library. Hopefully it can be of use to others.
