## Table of Contents

1. [Overview](#doc-docs-readme)
2. [Character Analysis](#doc-docs-character-analysis)
3. [Conversion](#doc-docs-conversion)
4. [Directionality](#doc-docs-directionality)
5. [Normalization](#doc-docs-normalization)
6. [Script Detection](#doc-docs-script-detection)
<a id="doc-docs-readme"></a>

## Requirements

Babel requires PHP 8.2+ with the following extensions:
- `ext-intl` (ICU transliteration)
- `ext-mbstring` (multibyte string handling)
- `ext-iconv` (encoding conversion)

## Installation

Install Babel with composer:

```bash
composer require cline/babel
```

## Basic Usage

Create a Babel instance from any string:

```php
use Cline\Babel\Babel;

$babel = Babel::from('Héllo Wörld');
```

### Fluent Transformations

Chain methods for complex transformations:

```php
$slug = Babel::from('Héllo Wörld!')
    ->toAscii();  // "Hello World!"
```

### Null Safety

Babel handles null values gracefully:

```php
$babel = Babel::from(null);
$babel->isEmpty();     // true
$babel->toAscii();     // null
$babel->isUtf8();      // true (empty is valid UTF-8)
```

### Immutability

All transformation methods return new instances:

```php
$original = Babel::from('Café');
$ascii = $original->toAscii();

$original->value();  // "Café" (unchanged)
$ascii;              // "Cafe"
```

## Quick Examples

### Convert to ASCII

```php
Babel::from('Żółć')->toAscii();           // "Zolc"
Babel::from('北京')->toAscii();            // "bei jing"
Babel::from('Привет')->toAscii();          // "Privet"
```

### Detect Scripts

```php
Babel::from('Hello 世界')->containsChinese();   // true
Babel::from('Привет мир')->containsCyrillic();  // true
Babel::from('مرحبا')->isRtl();                  // true
```

### Clean Strings

```php
Babel::from("Hello\x00World")->removeNonPrintable()->value();  // "HelloWorld"
Babel::from('Hello 👋')->removeEmoji()->value();                // "Hello "
```

### Grapheme Operations

```php
// Split into grapheme clusters
Babel::from('Hello')->graphemes();  // ['H', 'e', 'l', 'l', 'o']
Babel::from('café')->graphemes();   // ['c', 'a', 'f', 'é']

// Reverse preserving graphemes
Babel::from('café')->reverse()->value();  // "éfac"
```

### Create Slugs

```php
Babel::from('Héllo Wörld!')->toSlug();  // "hello-world"
```

## Next Steps

- **[Conversion](#doc-docs-conversion)** - Encoding conversion methods
- **[Script Detection](#doc-docs-script-detection)** - Detect scripts and character sets
- **[Directionality](#doc-docs-directionality)** - RTL/LTR detection
- **[Character Analysis](#doc-docs-character-analysis)** - Analyze string contents
- **[Normalization](#doc-docs-normalization)** - Clean and normalize strings

<a id="doc-docs-character-analysis"></a>

## Non-Printable Characters

Detect characters that don't render visibly:

```php
use Cline\Babel\Babel;

// Null byte
Babel::from("Hello\x00World")->containsNonPrintable();  // true

// Bell character
Babel::from("Alert\x07!")->containsNonPrintable();      // true

// Normal text
Babel::from('Hello World')->containsNonPrintable();     // false

// Note: tabs and newlines are considered printable
Babel::from("Hello\tWorld\n")->containsNonPrintable();  // false
```

## Control Characters

Detect ASCII control characters (C0 and C1):

```php
// Null byte
Babel::from("Hello\x00World")->containsControlChars();  // true

// Escape character
Babel::from("Hello\x1BWorld")->containsControlChars();  // true

// Bell
Babel::from("Hello\x07World")->containsControlChars();  // true

// Normal text with whitespace
Babel::from("Hello\nWorld")->containsControlChars();    // true (newline is control)
```

## Whitespace Detection

Check if string contains only whitespace:

```php
// Spaces only
Babel::from('   ')->isWhitespace();            // true

// Tabs and newlines
Babel::from("\t\n\r")->isWhitespace();         // true

// Mixed content
Babel::from(' Hello ')->isWhitespace();        // false

// Empty string
Babel::from('')->isWhitespace();               // true

// Unicode whitespace
Babel::from("\u{00A0}")->isWhitespace();       // true (non-breaking space)
```

## Invisible Characters

Detect zero-width and invisible Unicode characters often used for text manipulation:

```php
// Zero-width space (U+200B)
Babel::from("Hello\u{200B}World")->containsInvisible();  // true

// Zero-width non-joiner (U+200C)
Babel::from("Hello\u{200C}World")->containsInvisible();  // true

// Zero-width joiner (U+200D)
Babel::from("Hello\u{200D}World")->containsInvisible();  // true

// Byte order mark (U+FEFF)
Babel::from("\u{FEFF}Hello")->containsInvisible();       // true

// Word joiner (U+2060)
Babel::from("Hello\u{2060}World")->containsInvisible();  // true

// Normal text
Babel::from('Hello World')->containsInvisible();         // false
```

## Homoglyph Detection

Detect characters that look similar to common Latin characters but are from different scripts (potential security issue):

```php
// Cyrillic 'а' looks like Latin 'a'
Babel::from('pаypal')->containsHomoglyphs();    // true (Cyrillic а)

// Cyrillic 'о' looks like Latin 'o'
Babel::from('gооgle')->containsHomoglyphs();    // true (Cyrillic о)

// Pure Latin
Babel::from('paypal')->containsHomoglyphs();    // false

// Pure Cyrillic (not homoglyphs, just Cyrillic)
Babel::from('Привет')->containsHomoglyphs();    // false
```

### Common Homoglyphs

| Latin | Cyrillic | Greek |
|-------|----------|-------|
| a | а (U+0430) | α (U+03B1) |
| c | с (U+0441) | - |
| e | е (U+0435) | ε (U+03B5) |
| o | о (U+043E) | ο (U+03BF) |
| p | р (U+0440) | ρ (U+03C1) |
| x | х (U+0445) | χ (U+03C7) |

## Mixed Script Detection

Detect strings containing characters from multiple scripts (potential spoofing/phishing indicator):

```php
// Mixed Latin and Cyrillic
Babel::from('Hello Привет')->containsMixedScripts();  // true

// Mixed Latin and Chinese
Babel::from('Hello 世界')->containsMixedScripts();     // true

// Mixed Latin and Arabic
Babel::from('Hello مرحبا')->containsMixedScripts();    // true

// Single script (pure Latin)
Babel::from('Hello World')->containsMixedScripts();   // false

// Single script (pure Cyrillic)
Babel::from('Привет мир')->containsMixedScripts();    // false
```

## BOM Detection

Check if string starts with a byte-order mark:

```php
// UTF-8 BOM
Babel::from("\xEF\xBB\xBFHello")->hasBom();    // true

// UTF-16 BE BOM
Babel::from("\xFE\xFFHello")->hasBom();        // true

// UTF-16 LE BOM
Babel::from("\xFF\xFEHello")->hasBom();        // true

// No BOM
Babel::from('Hello')->hasBom();                // false
```

## String Metrics

Get basic string measurements:

```php
$babel = Babel::from('Héllo 世界');

// Character count (not bytes)
$babel->length();   // 8

// Byte count
$babel->bytes();    // 13 (UTF-8 encoded)

// Check emptiness
$babel->isEmpty();     // false
$babel->isNotEmpty();  // true
```

## Use Cases

### Security Validation

```php
function isSafeUsername(string $username): bool
{
    $babel = Babel::from($username);

    return !$babel->containsHomoglyphs()
        && !$babel->containsInvisible()
        && !$babel->containsControlChars();
}
```

### Input Sanitization Check

```php
function needsSanitization(string $input): bool
{
    $babel = Babel::from($input);

    return $babel->containsNonPrintable()
        || $babel->containsInvisible()
        || $babel->containsControlChars();
}
```

### Display Validation

```php
function isDisplayable(string $text): bool
{
    $babel = Babel::from($text);

    return !$babel->containsNonPrintable()
        && !$babel->containsControlChars();
}
```

### Homoglyph Attack Detection

```php
function detectPunycodeThreat(string $domain): bool
{
    $babel = Babel::from($domain);

    // Domain contains mixed scripts with Latin lookalikes
    return $babel->containsLatin() && $babel->containsHomoglyphs();
}

// Examples
detectPunycodeThreat('google.com');    // false
detectPunycodeThreat('gооgle.com');    // true (Cyrillic о)
detectPunycodeThreat('pаypal.com');    // true (Cyrillic а)
```

<a id="doc-docs-conversion"></a>

## ASCII Conversion

Convert any Unicode string to ASCII with intelligent transliteration:

```php
use Cline\Babel\Babel;

// European characters
Babel::from('Żółć')->toAscii();      // "Zolc"
Babel::from('Ñoño')->toAscii();      // "Nono"
Babel::from('Ümläüt')->toAscii();    // "Umlaut"

// Asian characters (romanization)
Babel::from('北京')->toAscii();       // "bei jing"
Babel::from('東京')->toAscii();       // "dong jing"
Babel::from('서울')->toAscii();       // "seoul"

// Cyrillic
Babel::from('Москва')->toAscii();    // "Moskva"
Babel::from('Привет')->toAscii();    // "Privet"

// Greek
Babel::from('Αθήνα')->toAscii();     // "Athena"
```

## UTF-8 Conversion

Convert strings to UTF-8 from detected or specified encoding:

```php
// Auto-detect source encoding
$utf8 = Babel::from($legacyString)->toUtf8();

// Specify source encoding
$utf8 = Babel::from($latin1String)->toUtf8('ISO-8859-1');
```

## Latin-1 Conversion

Convert to ISO-8859-1 (Latin-1) with transliteration for unsupported characters:

```php
Babel::from('Café résumé')->toLatin1();  // Preserves accents
Babel::from('北京')->toLatin1();          // Transliterates to ASCII first
```

If you need the result to remain UTF-8 (for example for `json_encode()`), use:

```php
Babel::from('Häagen 北京')->toLatin1TransliteratedUtf8();
// "Häagen bei jing"
```

## Custom Encoding

Convert to any supported encoding:

```php
// Convert to Windows-1252
$win = Babel::from($text)->toEncoding('Windows-1252');

// Specify source encoding
$result = Babel::from($text)->toEncoding('UTF-16', 'UTF-8');
```

## HTML Entities

Convert special characters to HTML entities and back:

```php
// Encode
Babel::from('<script>"alert"</script>')
    ->toHtmlEntities();
// "&lt;script&gt;&quot;alert&quot;&lt;/script&gt;"

// Decode
Babel::from('&lt;div&gt;')
    ->fromHtmlEntities()
    ->value();
// "<div>"
```

### Custom Flags

```php
use const ENT_QUOTES;
use const ENT_HTML5;
use const ENT_XML1;

// HTML5 with quotes (default)
Babel::from($text)->toHtmlEntities(ENT_QUOTES | ENT_HTML5);

// XML compatible
Babel::from($text)->toHtmlEntities(ENT_QUOTES | ENT_XML1);
```

## URL Slugs

Create URL-safe slugs from any string:

```php
Babel::from('Hello World!')->toSlug();           // "hello-world"
Babel::from('Héllo Wörld')->toSlug();            // "hello-world"
Babel::from('  Multiple   Spaces  ')->toSlug();  // "multiple-spaces"

// Custom separator
Babel::from('Hello World')->toSlug('_');         // "hello_world"
```

## Safe Filenames

Create filesystem-safe filenames:

```php
Babel::from('My Document (Final).pdf')->toFilename();
// "my_document_final.pdf"

Babel::from('Report 2024/01/15')->toFilename();
// "report_2024_01_15"

// Custom separator
Babel::from('Hello World.txt')->toFilename('-');
// "hello-world.txt"
```

## XML-Safe Strings

Remove characters invalid in XML 1.0:

```php
Babel::from("Hello\x00World")->toXmlSafe();  // "HelloWorld"
Babel::from("Tab\tNewline\n")->toXmlSafe();  // "Tab\tNewline\n" (preserved)
```

## Error Handling

Conversion methods throw exceptions on failure:

```php
use Cline\Babel\Exceptions\EncodingException;

try {
    $result = Babel::from($text)->toEncoding('INVALID-ENCODING');
} catch (EncodingException $e) {
    // Handle invalid encoding
}
```

<a id="doc-docs-directionality"></a>

## Overview

Some languages read right-to-left (RTL), such as Arabic, Hebrew, and Persian. Babel provides methods to detect text direction for proper UI rendering and layout decisions.

## Check RTL Dominance

Determine if text is predominantly right-to-left:

```php
use Cline\Babel\Babel;

// Arabic text
Babel::from('مرحبا بالعالم')->isRtl();      // true

// Hebrew text
Babel::from('שלום עולם')->isRtl();          // true

// English text
Babel::from('Hello World')->isRtl();        // false

// Mixed (depends on dominant direction)
Babel::from('Hello مرحبا World')->isRtl();  // false (more LTR chars)
```

## Check for RTL Presence

Check if any RTL characters exist (regardless of dominance):

```php
// Pure RTL
Babel::from('مرحبا')->containsRtl();         // true

// Mixed content
Babel::from('Hello مرحبا')->containsRtl();   // true

// Pure LTR
Babel::from('Hello World')->containsRtl();   // false

// Numbers only (neutral)
Babel::from('12345')->containsRtl();         // false
```

## Get Text Direction

Get the dominant direction as a string value:

```php
// Left-to-right
Babel::from('Hello World')->direction();     // "ltr"

// Right-to-left
Babel::from('مرحبا بالعالم')->direction();    // "rtl"

// Mixed directions
Babel::from('Hello مرحبا World مرحبا')->direction();  // "mixed"

// Neutral (numbers, punctuation only)
Babel::from('12345')->direction();           // "neutral"
Babel::from('!!!')->direction();             // "neutral"

// Empty
Babel::from('')->direction();                // "neutral"
Babel::from(null)->direction();              // "neutral"
```

## Return Values

The `direction()` method returns one of four values:

| Value | Description |
|-------|-------------|
| `ltr` | Predominantly left-to-right text |
| `rtl` | Predominantly right-to-left text |
| `mixed` | Significant characters in both directions |
| `neutral` | Only neutral characters (numbers, punctuation, whitespace) |

## Use Cases

### HTML Direction Attribute

```php
function getHtmlDir(string $content): string
{
    return Babel::from($content)->direction() === 'rtl' ? 'rtl' : 'ltr';
}

// Usage
$dir = getHtmlDir($userComment);
echo "<p dir=\"{$dir}\">{$userComment}</p>";
```

### Dynamic Layout

```php
function getTextAlignment(string $text): string
{
    $direction = Babel::from($text)->direction();

    return match ($direction) {
        'rtl' => 'right',
        'ltr' => 'left',
        default => 'left',  // Default for mixed/neutral
    };
}
```

### Bidirectional Text Warning

```php
function hasBidiContent(string $text): bool
{
    return Babel::from($text)->direction() === 'mixed';
}

// Alert users about potential rendering issues
if (hasBidiContent($message)) {
    $warning = 'This message contains mixed-direction text.';
}
```

### Form Input Validation

```php
function validateArabicInput(string $input): bool
{
    $babel = Babel::from($input);

    // Must be Arabic and RTL dominant
    return $babel->containsArabic() && $babel->isRtl();
}
```

## RTL Scripts

The following Unicode scripts are considered right-to-left:

- Arabic
- Hebrew
- Syriac
- Thaana (Maldivian)
- N'Ko
- Samaritan
- Mandaic

## Notes

- Numbers and punctuation are considered **neutral** and don't affect direction
- Common characters (spaces, basic punctuation) are also neutral
- The `mixed` direction is returned when both RTL and LTR characters are present in significant amounts
- Empty strings and null values return `neutral`

<a id="doc-docs-normalization"></a>

## Unicode Normalization

Apply Unicode normalization forms for consistent string representation:

```php
use Cline\Babel\Babel;
use Normalizer;

// Default: NFC (Canonical Decomposition, followed by Canonical Composition)
Babel::from('café')->normalize();

// NFD: Canonical Decomposition
Babel::from('café')->normalize(Normalizer::NFD);

// NFKC: Compatibility Decomposition, followed by Canonical Composition
Babel::from('ﬁ')->normalize(Normalizer::NFKC);  // "fi"

// NFKD: Compatibility Decomposition
Babel::from('①')->normalize(Normalizer::NFKD);  // "1"
```

### Normalization Forms

| Form | Description | Use Case |
|------|-------------|----------|
| NFC | Composed characters | Default, web content |
| NFD | Decomposed characters | Sorting, searching |
| NFKC | Compatibility composed | Search normalization |
| NFKD | Compatibility decomposed | Maximum decomposition |

## Remove BOM

Strip byte-order marks from the beginning of strings:

```php
// UTF-8 BOM
Babel::from("\xEF\xBB\xBFHello")->removeBom()->value();  // "Hello"

// UTF-16 BE BOM
Babel::from("\xFE\xFFHello")->removeBom()->value();      // "Hello"

// UTF-16 LE BOM
Babel::from("\xFF\xFEHello")->removeBom()->value();      // "Hello"

// No BOM (unchanged)
Babel::from('Hello')->removeBom()->value();              // "Hello"
```

## Remove Non-Printable Characters

Strip characters that don't render visibly (preserves tabs, newlines, carriage returns):

```php
// Null byte
Babel::from("Hello\x00World")->removeNonPrintable()->value();  // "HelloWorld"

// Bell character
Babel::from("Hello\x07World")->removeNonPrintable()->value();  // "HelloWorld"

// Preserves whitespace
Babel::from("Hello\tWorld\n")->removeNonPrintable()->value();  // "Hello\tWorld\n"
```

## Remove Control Characters

Strip all ASCII control characters (including tabs and newlines):

```php
// Removes all control chars
Babel::from("Hello\tWorld\n")->removeControlChars()->value();  // "HelloWorld"

// Null and bell
Babel::from("Hello\x00\x07World")->removeControlChars()->value();  // "HelloWorld"
```

## Remove Invisible Characters

Strip zero-width and invisible Unicode characters:

```php
// Zero-width space
Babel::from("Hello\u{200B}World")->removeInvisible()->value();  // "HelloWorld"

// Zero-width non-joiner
Babel::from("Hello\u{200C}World")->removeInvisible()->value();  // "HelloWorld"

// Zero-width joiner
Babel::from("Hello\u{200D}World")->removeInvisible()->value();  // "HelloWorld"

// Byte order mark (inline)
Babel::from("Hello\u{FEFF}World")->removeInvisible()->value();  // "HelloWorld"

// Word joiner
Babel::from("Hello\u{2060}World")->removeInvisible()->value();  // "HelloWorld"
```

## Remove Emoji

Strip emoji characters from strings:

```php
Babel::from('Hello 👋 World 🌍')->removeEmoji()->value();
// "Hello  World "

Babel::from('Great job! 🎉👏')->removeEmoji()->value();
// "Great job! "

// No emoji (unchanged)
Babel::from('Hello World')->removeEmoji()->value();
// "Hello World"
```

## Remove Script

Strip all characters from a specific Unicode script:

```php
// Remove Cyrillic
Babel::from('Hello Привет World')->removeScript('Cyrillic')->value();
// "Hello  World"

// Remove Han (Chinese)
Babel::from('Hello 世界 World')->removeScript('Han')->value();
// "Hello  World"

// Remove Arabic
Babel::from('Hello مرحبا World')->removeScript('Arabic')->value();
// "Hello  World"
```

## Remove Diacritics

Strip accent marks and diacritical marks from characters:

```php
// Accented characters
Babel::from('café')->removeDiacritics()->value();    // "cafe"
Babel::from('Ñoño')->removeDiacritics()->value();    // "Nono"
Babel::from('naïve')->removeDiacritics()->value();   // "naive"

// Note: some characters like Polish 'ł' are distinct letters, not diacritics
Babel::from('Żółć')->removeDiacritics()->value();    // "Zołc"

// Plain ASCII unchanged
Babel::from('Hello')->removeDiacritics()->value();   // "Hello"
```

## Collapse Whitespace

Normalize multiple whitespace characters into single spaces:

```php
// Multiple spaces
Babel::from('Hello    World')->collapseWhitespace()->value();
// "Hello World"

// Mixed whitespace (tabs, newlines)
Babel::from("Hello\t\n\tWorld")->collapseWhitespace()->value();
// "Hello World"

// Trims leading/trailing whitespace
Babel::from('  Hello World  ')->collapseWhitespace()->value();
// "Hello World"
```

## Custom Transliteration

Apply ICU transliteration rules for advanced transformations:

```php
// Default: Any-Latin; Latin-ASCII
Babel::from('Żółć')->transliterate()->value();              // "Zolc"
Babel::from('北京')->transliterate()->value();               // "bei jing"
Babel::from('Москва')->transliterate()->value();            // "Moskva"

// Case conversion
Babel::from('HELLO')->transliterate('Upper; Lower')->value();  // "hello"
Babel::from('hello')->transliterate('Lower; Title')->value();  // "Hello"

// Custom rules
Babel::from('café')->transliterate('NFD; [:Nonspacing Mark:] Remove; NFC')->value();
// "cafe"
```

### Error Handling

```php
use Cline\Babel\Exceptions\TransliterationException;

try {
    Babel::from('text')->transliterate('Invalid-Rules');
} catch (TransliterationException $e) {
    // Handle invalid transliteration rules
}
```

## Chaining Transformations

Combine multiple cleaning operations:

```php
$cleaned = Babel::from($dirtyInput)
    ->removeBom()
    ->removeInvisible()
    ->removeNonPrintable()
    ->normalize()
    ->value();
```

## Use Cases

### File Content Processing

```php
function cleanFileContent(string $content): string
{
    return Babel::from($content)
        ->removeBom()
        ->removeNonPrintable()
        ->normalize()
        ->value() ?? '';
}
```

### User Input Sanitization

```php
function sanitizeUserInput(string $input): string
{
    return Babel::from($input)
        ->removeInvisible()
        ->removeControlChars()
        ->normalize()
        ->value() ?? '';
}
```

### Emoji-Free Content

```php
function stripEmoji(string $text): string
{
    return Babel::from($text)
        ->removeEmoji()
        ->value() ?? '';
}
```

### Preparing for Search

```php
function normalizeForSearch(string $query): string
{
    return Babel::from($query)
        ->normalize(Normalizer::NFKC)
        ->transliterate('Any-Latin; Latin-ASCII; Lower')
        ->value() ?? '';
}
```

<a id="doc-docs-script-detection"></a>

## Contains Methods

Check if a string contains characters from specific scripts:

### Asian Scripts

```php
use Cline\Babel\Babel;

// Any Asian script (Han, Hiragana, Katakana, Hangul)
Babel::from('Hello 世界')->containsAsian();      // true
Babel::from('Hello World')->containsAsian();     // false

// Chinese (Han script)
Babel::from('北京欢迎你')->containsChinese();     // true
Babel::from('こんにちは')->containsChinese();     // false (Japanese only)

// Japanese (Hiragana, Katakana, or Han)
Babel::from('こんにちは')->containsJapanese();    // true (Hiragana)
Babel::from('カタカナ')->containsJapanese();      // true (Katakana)
Babel::from('日本')->containsJapanese();          // true (Han/Kanji)

// Korean (Hangul)
Babel::from('안녕하세요')->containsKorean();       // true
Babel::from('한글')->containsKorean();            // true
```

### European Scripts

```php
// Cyrillic
Babel::from('Привет мир')->containsCyrillic();    // true
Babel::from('Москва')->containsCyrillic();        // true

// Greek
Babel::from('Αθήνα')->containsGreek();            // true
Babel::from('Ελλάδα')->containsGreek();           // true

// Latin
Babel::from('Hello')->containsLatin();            // true
Babel::from('Café')->containsLatin();             // true
Babel::from('北京')->containsLatin();              // false
```

### Middle Eastern Scripts

```php
// Arabic
Babel::from('مرحبا بالعالم')->containsArabic();    // true
Babel::from('السلام')->containsArabic();          // true

// Hebrew
Babel::from('שלום עולם')->containsHebrew();       // true
Babel::from('ישראל')->containsHebrew();           // true
```

### South Asian Scripts

```php
// Devanagari (Hindi, Sanskrit, Marathi)
Babel::from('नमस्ते')->containsDevanagari();       // true
Babel::from('Hello नमस्ते')->containsDevanagari(); // true

// Bengali
Babel::from('বাংলা')->containsBengali();           // true

// Tamil
Babel::from('தமிழ்')->containsTamil();             // true
```

### Southeast Asian Scripts

```php
// Thai
Babel::from('สวัสดี')->containsThai();             // true
Babel::from('ประเทศไทย')->containsThai();          // true

// Vietnamese (Latin with diacritics)
Babel::from('Việt Nam')->containsVietnamese();    // true
Babel::from('Xin chào')->containsVietnamese();    // true
```

### Caucasian Scripts

```php
// Armenian
Babel::from('Հայdelays')->containsArmenian();     // true

// Georgian
Babel::from('საქართველო')->containsGeorgian();     // true
```

### Other Scripts

```php
// Emoji
Babel::from('Hello 👋 World')->containsEmoji();   // true
Babel::from('Hello World')->containsEmoji();      // false
```

### Generic Script Detection

Check for any Unicode script by name:

```php
Babel::from('Здравствуйте')->containsScript('Cyrillic');  // true
Babel::from('Γειά σου')->containsScript('Greek');         // true
Babel::from('नमस्ते')->containsScript('Devanagari');      // true
```

## Exclusive Methods

Check if a string contains **only** characters from specific sets:

### Latin Only

```php
Babel::from('Hello World')->isLatin();     // true
Babel::from('Hello 123')->isLatin();       // true (includes common chars)
Babel::from('Héllo')->isLatin();           // true (includes accented)
Babel::from('Hello 世界')->isLatin();       // false (mixed)
```

### Numeric Only

```php
Babel::from('12345')->isNumeric();         // true
Babel::from('123.45')->isNumeric();        // false (period)
Babel::from('123abc')->isNumeric();        // false (letters)
```

### Alphanumeric Only

```php
Babel::from('Hello123')->isAlphanumeric();      // true
Babel::from('Hello World')->isAlphanumeric();   // false (space)
Babel::from('Hello_123')->isAlphanumeric();     // false (underscore)
```

### Single Script Only

Check if string contains only one specific script:

```php
Babel::from('Привет')->isScript('Cyrillic');           // true
Babel::from('Hello Привет')->isScript('Cyrillic');     // false (mixed)
Babel::from('12345')->isScript('Common');              // true (numbers are Common)
```

## Encoding Detection

Detect and validate string encodings:

```php
// Detect encoding
Babel::from('Hello')->detect();              // "ASCII"
Babel::from('Héllo')->detect();              // "UTF-8"
Babel::from(null)->detect();                 // null

// Check specific encodings
Babel::from('Hello')->isUtf8();              // true
Babel::from('Hello')->isAscii();             // true
Babel::from('Héllo')->isAscii();             // false

// Validate encoding
Babel::from($text)->isValidEncoding('UTF-8');         // true/false
Babel::from($text)->isValidEncoding('ISO-8859-1');    // true/false
```

## Use Cases

### Content Moderation

```php
function requiresTranslation(string $content): bool
{
    $babel = Babel::from($content);

    return $babel->containsChinese()
        || $babel->containsJapanese()
        || $babel->containsKorean()
        || $babel->containsCyrillic()
        || $babel->containsArabic();
}
```

### Form Validation

```php
function validateUsername(string $username): bool
{
    $babel = Babel::from($username);

    // Only allow Latin alphanumeric
    return $babel->isAlphanumeric() && $babel->isLatin();
}
```

### Localization Detection

```php
function detectLanguageHint(string $text): ?string
{
    $babel = Babel::from($text);

    return match (true) {
        $babel->containsJapanese() => 'ja',
        $babel->containsKorean() => 'ko',
        $babel->containsChinese() => 'zh',
        $babel->containsArabic() => 'ar',
        $babel->containsHebrew() => 'he',
        $babel->containsCyrillic() => 'ru',
        $babel->containsGreek() => 'el',
        $babel->containsThai() => 'th',
        $babel->containsVietnamese() => 'vi',
        $babel->containsDevanagari() => 'hi',
        $babel->containsBengali() => 'bn',
        $babel->containsTamil() => 'ta',
        $babel->containsArmenian() => 'hy',
        $babel->containsGeorgian() => 'ka',
        default => null,
    };
}
```
