<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Concerns;

use Cline\Babel\Babel;

use function array_keys;
use function count;
use function mb_strpos;
use function mb_trim;
use function preg_match;
use function str_starts_with;

/**
 * Provides character analysis methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait AnalyzesCharacters
{
    /**
     * Common homoglyph pairs (Latin lookalikes from other scripts).
     *
     * @var array<string, string>
     */
    private static array $homoglyphs = [
        'а' => 'a', // Cyrillic
        'е' => 'e',
        'о' => 'o',
        'р' => 'p',
        'с' => 'c',
        'х' => 'x',
        'у' => 'y',
        'А' => 'A',
        'В' => 'B',
        'Е' => 'E',
        'К' => 'K',
        'М' => 'M',
        'Н' => 'H',
        'О' => 'O',
        'Р' => 'P',
        'С' => 'C',
        'Т' => 'T',
        'Х' => 'X',
        'Ү' => 'Y',
        'ο' => 'o', // Greek
        'Ο' => 'O',
        'Α' => 'A',
        'Β' => 'B',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Ι' => 'I',
        'Κ' => 'K',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ρ' => 'P',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Χ' => 'X',
    ];

    /**
     * Check if string contains non-printable characters.
     *
     * @example Babel::from("Hello\x00World")->containsNonPrintable() // true
     */
    public function containsNonPrintable(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // Non-printable: control characters except tab, newline, carriage return
        return (bool) preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $this->value);
    }

    /**
     * Check if string contains control characters.
     *
     * @example Babel::from("Hello\x07World")->containsControlChars() // true
     */
    public function containsControlChars(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Cc}/u', $this->value);
    }

    /**
     * Check if string contains only whitespace.
     *
     * @example Babel::from("   \t\n")->isWhitespace() // true
     * @example Babel::from("Hello")->isWhitespace() // false
     */
    public function isWhitespace(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return mb_trim($this->value) === '';
    }

    /**
     * Check if string contains invisible/zero-width characters.
     *
     * @example Babel::from("Hello\u{200B}World")->containsInvisible() // true
     */
    public function containsInvisible(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // Zero-width characters:
        // U+200B: Zero Width Space
        // U+200C: Zero Width Non-Joiner
        // U+200D: Zero Width Joiner
        // U+FEFF: Zero Width No-Break Space (BOM)
        // U+2060: Word Joiner
        // U+180E: Mongolian Vowel Separator
        return (bool) preg_match('/[\x{200B}\x{200C}\x{200D}\x{FEFF}\x{2060}\x{180E}]/u', $this->value);
    }

    /**
     * Check if string contains homoglyphs (look-alike characters from different scripts).
     *
     * @example Babel::from('Hеllo')->containsHomoglyphs() // true (Cyrillic 'е')
     */
    public function containsHomoglyphs(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        foreach (array_keys(self::$homoglyphs) as $homoglyph) {
            if (mb_strpos($this->value, $homoglyph) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if string contains characters from multiple scripts (potential spoofing).
     *
     * @example Babel::from('Hello Привет')->containsMixedScripts() // true
     * @example Babel::from('Hello World')->containsMixedScripts() // false
     */
    public function containsMixedScripts(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        $scripts = [];

        // Check for major scripts (excluding Common which includes numbers, punctuation)
        if (preg_match('/\p{Latin}/u', $this->value)) {
            $scripts[] = 'Latin';
        }

        if (preg_match('/\p{Cyrillic}/u', $this->value)) {
            $scripts[] = 'Cyrillic';
        }

        if (preg_match('/\p{Greek}/u', $this->value)) {
            $scripts[] = 'Greek';
        }

        if (preg_match('/\p{Arabic}/u', $this->value)) {
            $scripts[] = 'Arabic';
        }

        if (preg_match('/\p{Hebrew}/u', $this->value)) {
            $scripts[] = 'Hebrew';
        }

        if (preg_match('/\p{Han}/u', $this->value)) {
            $scripts[] = 'Han';
        }

        if (preg_match('/\p{Hiragana}|\p{Katakana}/u', $this->value)) {
            $scripts[] = 'Japanese';
        }

        if (preg_match('/\p{Hangul}/u', $this->value)) {
            $scripts[] = 'Hangul';
        }

        if (preg_match('/\p{Devanagari}/u', $this->value)) {
            $scripts[] = 'Devanagari';
        }

        if (preg_match('/\p{Bengali}/u', $this->value)) {
            $scripts[] = 'Bengali';
        }

        if (preg_match('/\p{Tamil}/u', $this->value)) {
            $scripts[] = 'Tamil';
        }

        if (preg_match('/\p{Thai}/u', $this->value)) {
            $scripts[] = 'Thai';
        }

        if (preg_match('/\p{Armenian}/u', $this->value)) {
            $scripts[] = 'Armenian';
        }

        if (preg_match('/\p{Georgian}/u', $this->value)) {
            $scripts[] = 'Georgian';
        }

        return count($scripts) > 1;
    }

    /**
     * Check if string contains a byte-order mark (BOM).
     *
     * @example Babel::from("\xEF\xBB\xBFHello")->hasBom() // true
     * @example Babel::from('Hello')->hasBom() // false
     */
    public function hasBom(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // UTF-8 BOM: EF BB BF
        // UTF-16 BE BOM: FE FF
        // UTF-16 LE BOM: FF FE
        // UTF-32 BE BOM: 00 00 FE FF
        // UTF-32 LE BOM: FF FE 00 00
        $boms = [
            "\xEF\xBB\xBF",     // UTF-8
            "\xFE\xFF",         // UTF-16 BE
            "\xFF\xFE",         // UTF-16 LE
            "\x00\x00\xFE\xFF", // UTF-32 BE
            "\xFF\xFE\x00\x00", // UTF-32 LE
        ];

        foreach ($boms as $bom) {
            if (str_starts_with($this->value, $bom)) {
                return true;
            }
        }

        return false;
    }
}
