<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Contracts;

use Normalizer;

use const ENT_HTML5;
use const ENT_QUOTES;

/**
 * @author Brian Faust <brian@cline.sh>
 */
interface Babel
{
    /**
     * Get the string value.
     */
    public function __toString(): string;

    /**
     * Create a new Babel instance from a string value.
     */
    public static function from(?string $value): self;

    // =========================================================================
    // CONVERSION
    // =========================================================================

    /**
     * Convert to ASCII with transliteration.
     */
    public function toAscii(): ?string;

    /**
     * Convert to UTF-8 from detected or specified encoding.
     */
    public function toUtf8(?string $from = null): ?string;

    /**
     * Convert to ISO-8859-1 (Latin-1) with transliteration.
     */
    public function toLatin1(): ?string;

    /**
     * Convert to a specific encoding.
     */
    public function toEncoding(string $to, ?string $from = null): ?string;

    /**
     * Convert special characters to HTML entities.
     */
    public function toHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML5): ?string;

    /**
     * Decode HTML entities back to characters.
     */
    public function fromHtmlEntities(int $flags = ENT_QUOTES | ENT_HTML5): self;

    /**
     * Convert to URL-safe slug.
     */
    public function toSlug(string $separator = '-'): ?string;

    /**
     * Convert to safe filename.
     */
    public function toFilename(string $separator = '_'): ?string;

    /**
     * Convert to XML 1.0 safe string.
     */
    public function toXmlSafe(): ?string;

    // =========================================================================
    // ENCODING DETECTION
    // =========================================================================

    /**
     * Detect the string's encoding.
     */
    public function detect(): ?string;

    /**
     * Check if string is valid UTF-8.
     */
    public function isUtf8(): bool;

    /**
     * Check if string contains only ASCII characters.
     */
    public function isAscii(): bool;

    /**
     * Check if string is valid for a specific encoding.
     */
    public function isValidEncoding(string $encoding): bool;

    // =========================================================================
    // SCRIPT DETECTION - CONTAINS
    // =========================================================================

    /**
     * Check if string contains Asian characters (Han, Hiragana, Katakana, Hangul).
     */
    public function containsAsian(): bool;

    /**
     * Check if string contains Chinese characters (Han script).
     */
    public function containsChinese(): bool;

    /**
     * Check if string contains Japanese characters (Hiragana, Katakana, or Han).
     */
    public function containsJapanese(): bool;

    /**
     * Check if string contains Korean characters (Hangul).
     */
    public function containsKorean(): bool;

    /**
     * Check if string contains Cyrillic characters.
     */
    public function containsCyrillic(): bool;

    /**
     * Check if string contains Arabic characters.
     */
    public function containsArabic(): bool;

    /**
     * Check if string contains Hebrew characters.
     */
    public function containsHebrew(): bool;

    /**
     * Check if string contains Greek characters.
     */
    public function containsGreek(): bool;

    /**
     * Check if string contains Thai characters.
     */
    public function containsThai(): bool;

    /**
     * Check if string contains Devanagari characters (Hindi, Sanskrit, etc.).
     */
    public function containsDevanagari(): bool;

    /**
     * Check if string contains Bengali characters.
     */
    public function containsBengali(): bool;

    /**
     * Check if string contains Tamil characters.
     */
    public function containsTamil(): bool;

    /**
     * Check if string contains Vietnamese characters (Latin with unique diacritics).
     */
    public function containsVietnamese(): bool;

    /**
     * Check if string contains Armenian characters.
     */
    public function containsArmenian(): bool;

    /**
     * Check if string contains Georgian characters.
     */
    public function containsGeorgian(): bool;

    /**
     * Check if string contains Latin characters.
     */
    public function containsLatin(): bool;

    /**
     * Check if string contains emoji.
     */
    public function containsEmoji(): bool;

    /**
     * Check if string contains a specific Unicode script.
     */
    public function containsScript(string $script): bool;

    // =========================================================================
    // SCRIPT DETECTION - EXCLUSIVE
    // =========================================================================

    /**
     * Check if string contains only Latin characters and common punctuation.
     */
    public function isLatin(): bool;

    /**
     * Check if string contains only numeric characters.
     */
    public function isNumeric(): bool;

    /**
     * Check if string contains only alphanumeric characters.
     */
    public function isAlphanumeric(): bool;

    /**
     * Check if string contains only characters from a specific script.
     */
    public function isScript(string $script): bool;

    // =========================================================================
    // DIRECTIONALITY
    // =========================================================================

    /**
     * Check if string is predominantly right-to-left.
     */
    public function isRtl(): bool;

    /**
     * Check if string contains any right-to-left characters.
     */
    public function containsRtl(): bool;

    /**
     * Get the dominant text direction.
     *
     * @return string 'ltr' | 'rtl' | 'mixed' | 'neutral'
     */
    public function direction(): string;

    // =========================================================================
    // CHARACTER ANALYSIS
    // =========================================================================

    /**
     * Check if string contains non-printable characters.
     */
    public function containsNonPrintable(): bool;

    /**
     * Check if string contains control characters.
     */
    public function containsControlChars(): bool;

    /**
     * Check if string contains only whitespace.
     */
    public function isWhitespace(): bool;

    /**
     * Check if string contains invisible/zero-width characters.
     */
    public function containsInvisible(): bool;

    /**
     * Check if string contains homoglyphs (look-alike characters).
     */
    public function containsHomoglyphs(): bool;

    /**
     * Check if string contains characters from multiple scripts (potential spoofing).
     */
    public function containsMixedScripts(): bool;

    /**
     * Check if string contains a byte-order mark (BOM).
     */
    public function hasBom(): bool;

    // =========================================================================
    // NORMALIZATION & CLEANING
    // =========================================================================

    /**
     * Apply Unicode normalization.
     */
    public function normalize(int $form = Normalizer::NFC): self;

    /**
     * Remove byte-order mark (BOM).
     */
    public function removeBom(): self;

    /**
     * Remove non-printable characters.
     */
    public function removeNonPrintable(): self;

    /**
     * Remove control characters.
     */
    public function removeControlChars(): self;

    /**
     * Remove invisible/zero-width characters.
     */
    public function removeInvisible(): self;

    /**
     * Remove emoji from string.
     */
    public function removeEmoji(): self;

    /**
     * Remove all characters from a specific script.
     */
    public function removeScript(string $script): self;

    /**
     * Apply custom transliteration rules.
     */
    public function transliterate(string $rules = 'Any-Latin; Latin-ASCII'): self;

    /**
     * Remove diacritics/accents from characters.
     */
    public function removeDiacritics(): self;

    /**
     * Collapse multiple whitespace characters into single spaces.
     */
    public function collapseWhitespace(): self;

    // =========================================================================
    // UTILITIES
    // =========================================================================

    /**
     * Get the raw string value.
     */
    public function value(): ?string;

    /**
     * Get string length in characters (not bytes).
     */
    public function length(): int;

    /**
     * Get string length in bytes.
     */
    public function bytes(): int;

    /**
     * Check if string is empty or null.
     */
    public function isEmpty(): bool;

    /**
     * Check if string is not empty.
     */
    public function isNotEmpty(): bool;

    /**
     * Split string into grapheme clusters.
     *
     * @return array<int, string>
     */
    public function graphemes(): array;

    /**
     * Reverse string preserving grapheme clusters.
     */
    public function reverse(): self;
}
