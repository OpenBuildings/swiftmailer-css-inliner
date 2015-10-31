# Swiftmailer Css Inliner Plugin

[![Build Status](https://travis-ci.org/OpenBuildings/swiftmailer-css-inliner.svg)](https://travis-ci.org/OpenBuildings/swiftmailer-css-inliner)
[![Code Coverage](https://scrutinizer-ci.com/g/OpenBuildings/swiftmailer-css-inliner/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/OpenBuildings/swiftmailer-css-inliner/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/OpenBuildings/swiftmailer-css-inliner/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/OpenBuildings/swiftmailer-css-inliner/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/swiftmailer-css-inliner/v/stable.png)](https://packagist.org/packages/openbuildings/swiftmailer-css-inliner)

A swiftmailer plugin that inlines the css (in style tags) into the specific html tags. Uses [CssToInlineStyles](https://github.com/tijsverkoyen/CssToInlineStyles). Works for both html body or html parts.

## Usage

```php
use Openbuildings\Swiftmailer\CssInlinerPlugin;

$mailer = Swift_Mailer::newInstance();

$mailer->registerPlugin(new CssInlinerPlugin());
```

You can set custom parameters by providing your own CssToInlineStyles object. Like this

```php
use Openbuildings\Swiftmailer\CssInlinerPlugin;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

$mailer = Swift_Mailer::newInstance();

$converter = new CssToInlineStyles();
$converter->setUseInlineStylesBlock(false);

$mailer->registerPlugin(new CssInlinerPlugin($converter));
```

## License

Copyright (c) 2015, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.
