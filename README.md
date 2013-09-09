# Swiftmailer Css Inliner Plugin 

[![Build Status](https://travis-ci.org/OpenBuildings/swiftmailer-css-inliner.png?branch=master)](https://travis-ci.org/OpenBuildings/swiftmailer-css-inliner)
[![Coverage Status](https://coveralls.io/repos/OpenBuildings/swiftmailer-css-inliner/badge.png?branch=master)](https://coveralls.io/r/OpenBuildings/swiftmailer-css-inliner?branch=master)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/swiftmailer-css-inliner/v/stable.png)](https://packagist.org/packages/openbuildings/swiftmailer-css-inliner)

A swiftmailer plugin that inlines the css (in style tags) into the specific html tags. Uses [CssToInlineStyles](https://github.com/tijsverkoyen/CssToInlineStyles). Works for both html body or html parts.

## Usage

```php
$mailer = Swift_Mailer::newInstance();

$mailer->registerPLugin(new CssInlinerPlugin());
```

## License

Copyright (c) 2013, OpenBuildings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.