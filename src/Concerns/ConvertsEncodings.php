<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Concerns;

use Cline\Babel\Babel;
use Cline\Babel\Exceptions\EncodingConversionFailedException;
use Cline\Babel\Exceptions\TransliterationFailedException;
use Transliterator;

use const ENT_HTML5;
use const ENT_QUOTES;

use function html_entity_decode;
use function htmlspecialchars;
use function iconv;
use function mb_check_encoding;
use function mb_convert_encoding;
use function mb_detect_encoding;
use function mb_strtolower;
use function mb_trim;
use function preg_quote;
use function preg_replace;

/**
 * Provides encoding conversion methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait ConvertsEncodings
{
    /**
     * Convert to ASCII with transliteration.
     *
     * @example Babel::from('Żółć')->toAscii() // "Zolc"
     * @example Babel::from('Café')->toAscii() // "Cafe"
     */
    public function toAscii(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII');

        if ($transliterator === null) {
            // Fallback to iconv
            $result = iconv(
                mb_detect_encoding($this->value, null, true) ?: 'UTF-8',
                'ASCII//TRANSLIT//IGNORE',
                $this->value,
            );

            return $result !== false ? $result : throw EncodingConversionFailedException::fromEncodings('UTF-8', 'ASCII');
        }

        $result = $transliterator->transliterate($this->value);

        return $result !== false ? $result : throw TransliterationFailedException::withRules('Any-Latin; Latin-ASCII');
    }

    /**
     * Convert to UTF-8 from detected or specified encoding.
     *
     * @example Babel::from($isoString)->toUtf8('ISO-8859-1')
     */
    public function toUtf8(?string $from = null): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        $from ??= mb_detect_encoding($this->value, null, true) ?: 'UTF-8';

        if ($from === 'UTF-8' && mb_check_encoding($this->value, 'UTF-8')) {
            return $this->value;
        }

        $result = mb_convert_encoding($this->value, 'UTF-8', $from);

        return $result !== false ? $result : throw EncodingConversionFailedException::fromEncodings($from, 'UTF-8');
    }

    /**
     * Convert to ISO-8859-1 (Latin-1) with transliteration.
     *
     * @example Babel::from('Żółć')->toLatin1() // "Zolc"
     */
    public function toLatin1(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        // First transliterate to ASCII-compatible characters
        $transliterator = Transliterator::create('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove');

        if ($transliterator !== null) {
            $transliterated = $transliterator->transliterate($this->value);

            if ($transliterated !== false) {
                return $transliterated;
            }
        }

        // Fallback to iconv
        $result = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $this->value);

        return $result !== false ? $result : $this->value;
    }

    /**
     * Convert to a specific encoding.
     *
     * @example Babel::from('Hello')->toEncoding('UTF-16')
     */
    public function toEncoding(string $to, ?string $from = null): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        $from ??= mb_detect_encoding($this->value, null, true) ?: 'UTF-8';

        $result = mb_convert_encoding($this->value, $to, $from);

        return $result !== false ? $result : throw EncodingConversionFailedException::fromEncodings($from, $to);
    }

    /**
     * Convert special characters to HTML entities.
     *
     * @example Babel::from('<script>')->toHtmlEntities() // "&lt;script&gt;"
     */
    public function toHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML5): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        return htmlspecialchars($this->value, $flags, 'UTF-8');
    }

    /**
     * Decode HTML entities back to characters.
     *
     * @example Babel::from('&lt;script&gt;')->fromHtmlEntities()->value() // "<script>"
     */
    public function fromHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML5): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        return new self(html_entity_decode($this->value, $flags, 'UTF-8'));
    }

    /**
     * Convert to URL-safe slug.
     *
     * @example Babel::from('Hello World!')->toSlug() // "hello-world"
     * @example Babel::from('Żółć zażółć')->toSlug() // "zolc-zazolc"
     */
    public function toSlug(string $separator = '-'): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        // Transliterate to ASCII
        $ascii = $this->toAscii();

        if ($ascii === null) {
            return null;
        }

        // Convert to lowercase
        $slug = mb_strtolower($ascii, 'UTF-8');

        // Replace non-alphanumeric characters with separator
        $slug = (string) preg_replace('/[^a-z0-9]+/', $separator, $slug);

        // Remove leading/trailing separators
        return mb_trim($slug, $separator);
    }

    /**
     * Convert to safe filename.
     *
     * @example Babel::from('My File (1).txt')->toFilename() // "my_file_1.txt"
     */
    public function toFilename(string $separator = '_'): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        // Transliterate to ASCII
        $ascii = $this->toAscii();

        if ($ascii === null) {
            return null;
        }

        // Convert to lowercase
        $filename = mb_strtolower($ascii, 'UTF-8');

        // Keep alphanumeric, dots, and hyphens
        $filename = (string) preg_replace('/[^a-z0-9.\-]+/', $separator, $filename);

        // Remove leading/trailing separators
        $filename = mb_trim($filename, $separator);

        // Remove multiple consecutive separators
        return (string) preg_replace('/'.preg_quote($separator, '/').'+ /', $separator, $filename);
    }

    /**
     * Convert to XML 1.0 safe string.
     * Removes characters not allowed in XML 1.0.
     *
     * @example Babel::from("Hello\x00World")->toXmlSafe() // "HelloWorld"
     */
    public function toXmlSafe(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        // XML 1.0 allowed characters: #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF]
        return (string) preg_replace(
            '/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            '',
            $this->value,
        );
    }
}
