[![Build Status](https://travis-ci.org/voku/html2text.svg?branch=master)](https://travis-ci.org/voku/html2text)
[![codecov.io](http://codecov.io/github/voku/html2text/coverage.svg?branch=master)](http://codecov.io/github/voku/html2text?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/html2text/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/html2text/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/47caa384f390472cbff1f1d46c86fd8e)](https://www.codacy.com/app/voku/CssToInlineStyles)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe/mini.png)](https://insight.sensiolabs.com/projects/81471116-0fb1-442b-a78f-7555ee585ebe)
[![Dependency Status](https://www.versioneye.com/user/projects/55a91f3e306535002000013c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55a91f3e306535002000013c)
[![Reference Status](https://www.versioneye.com/php/voku:html2text/reference_badge.svg?style=flat)](https://www.versioneye.com/php/voku:html2text/references)
[![Total Downloads](https://poser.pugx.org/voku/html2text/downloads)](https://packagist.org/packages/voku/html2text)
[![License](https://poser.pugx.org/voku/html2text/license.svg)](https://packagist.org/packages/voku/html2text)

# Html2Text

WARNING: this is only a Extended-Fork of "https://github.com/mtibben/html2text/"

A PHP library for converting HTML to formatted plain text.

## Basic Usage
```php
$html = new \voku\Html2Text\Html2Text('Hello, &quot;<b>world</b>&quot;');

echo $html->getText();  // Hello, "WORLD"
```

## History

This library started life on the blog of Jon Abernathy http://www.chuggnutt.com/html2text

A number of projects picked up the library and started using it - among those was RoundCube mail. They made a number of updates to it over time to suit their webmail client.

Now it has been extracted as a standalone library. Hopefully it can be of use to others.
