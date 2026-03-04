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
