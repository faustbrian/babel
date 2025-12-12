[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

Unicode-aware string encoding, conversion, and analysis for PHP with a fluent API. Features script detection, directionality analysis, character analysis, and intelligent transliteration.

## Requirements

> **Requires [PHP 8.2+](https://php.net/releases/)** with `ext-intl`, `ext-mbstring`, and `ext-iconv`

## Installation

```bash
composer require cline/babel
```

## Documentation

- **[Getting Started](https://docs.cline.sh/babel/getting-started/)** - Installation and basic usage
- **[Conversion](https://docs.cline.sh/babel/conversion/)** - Encoding conversion methods
- **[Script Detection](https://docs.cline.sh/babel/script-detection/)** - Detect scripts and character sets
- **[Directionality](https://docs.cline.sh/babel/directionality/)** - RTL/LTR detection
- **[Character Analysis](https://docs.cline.sh/babel/character-analysis/)** - Analyze string contents
- **[Normalization](https://docs.cline.sh/babel/normalization/)** - Clean and normalize strings

## Quick Examples

```php
use Cline\Babel\Babel;

// Convert to ASCII with transliteration
Babel::from('Żółć')->toAscii();           // "Zolc"
Babel::from('北京')->toAscii();            // "bei jing"
Babel::from('Привет')->toAscii();          // "Privet"

// Detect scripts
Babel::from('Hello 世界')->containsChinese();   // true
Babel::from('Привет мир')->containsCyrillic();  // true
Babel::from('مرحبا')->isRtl();                  // true

// Clean strings
Babel::from("Hello\x00World")->removeNonPrintable()->value();  // "HelloWorld"
Babel::from('Hello 👋')->removeEmoji()->value();                // "Hello "

// Create slugs
Babel::from('Héllo Wörld!')->toSlug();  // "hello-world"
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/babel/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/babel.svg
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/babel.svg

[link-tests]: https://github.com/faustbrian/babel/actions
[link-packagist]: https://packagist.org/packages/cline/babel
[link-downloads]: https://packagist.org/packages/cline/babel
[link-security]: https://github.com/faustbrian/babel/security
[link-maintainer]: https://github.com/faustbrian
[link-contributors]: ../../contributors
