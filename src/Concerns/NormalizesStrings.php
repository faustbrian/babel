<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Concerns;

use Cline\Babel\Babel;
use Cline\Babel\Exceptions\InvalidTransliterationRulesException;
use Cline\Babel\Exceptions\TransliterationFailedException;
use Normalizer;
use Transliterator;

use function is_string;
use function mb_strlen;
use function mb_substr;
use function mb_trim;
use function preg_replace;
use function str_starts_with;

/**
 * Provides string normalization and cleaning methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait NormalizesStrings
{
    /**
     * Apply Unicode normalization.
     *
     * @param int $form One of Normalizer::NFC, NFD, NFKC, NFKD
     *
     * @example Babel::from('café')->normalize()->value() // normalized form
     */
    public function normalize(int $form = Normalizer::NFC): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $normalized = Normalizer::normalize($this->value, $form);

        /** @var string $result */
        $result = $normalized !== false ? $normalized : $this->value;

        return new self($result);
    }

    /**
     * Remove byte-order mark (BOM).
     *
     * @example Babel::from("\xEF\xBB\xBFHello")->removeBom()->value() // "Hello"
     */
    public function removeBom(): self
    {
        if ($this->isEmpty()) {
            return $this;
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

        $value = $this->value;

        foreach ($boms as $bom) {
            if (str_starts_with((string) $value, $bom)) {
                $value = mb_substr((string) $value, mb_strlen($bom));

                break;
            }
        }

        return new self($value);
    }

    /**
     * Remove non-printable characters.
     *
     * @example Babel::from("Hello\x00World")->removeNonPrintable()->value() // "HelloWorld"
     */
    public function removeNonPrintable(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        // Keep tab, newline, carriage return - remove other control characters
        $cleaned = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $this->value);

        return new self($cleaned);
    }

    /**
     * Remove control characters.
     *
     * @example Babel::from("Hello\x07World")->removeControlChars()->value() // "HelloWorld"
     */
    public function removeControlChars(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $cleaned = (string) preg_replace('/\p{Cc}/u', '', $this->value);

        return new self($cleaned);
    }

    /**
     * Remove invisible/zero-width characters.
     *
     * @example Babel::from("Hello\u{200B}World")->removeInvisible()->value() // "HelloWorld"
     */
    public function removeInvisible(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $cleaned = (string) preg_replace('/[\x{200B}\x{200C}\x{200D}\x{FEFF}\x{2060}\x{180E}]/u', '', $this->value);

        return new self($cleaned);
    }

    /**
     * Remove emoji from string.
     *
     * @example Babel::from('Hello 👋 World')->removeEmoji()->value() // "Hello  World"
     */
    public function removeEmoji(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $cleaned = (string) preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $this->value);

        return new self($cleaned);
    }

    /**
     * Remove all characters from a specific script.
     *
     * @example Babel::from('Hello Привет')->removeScript('Cyrillic')->value() // "Hello "
     */
    public function removeScript(string $script): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $cleaned = (string) preg_replace('/\p{'.$script.'}/u', '', $this->value);

        return new self($cleaned);
    }

    /**
     * Apply custom transliteration rules.
     *
     * @example Babel::from('Żółć')->transliterate()->value() // "Zolc"
     * @example Babel::from('HELLO')->transliterate('Upper; Lower')->value() // "hello"
     */
    public function transliterate(string $rules = 'Any-Latin; Latin-ASCII'): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $transliterator = Transliterator::create($rules);

        if ($transliterator === null) {
            throw InvalidTransliterationRulesException::forRules($rules);
        }

        $result = $transliterator->transliterate($this->value);

        if ($result === false) {
            throw TransliterationFailedException::withRules($rules);
        }

        return new self($result);
    }

    /**
     * Remove diacritics/accents from characters.
     *
     * @example Babel::from('café')->removeDiacritics()->value() // "cafe"
     * @example Babel::from('Ñoño')->removeDiacritics()->value() // "Nono"
     */
    public function removeDiacritics(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        // NFD decomposes characters, then we remove combining diacritical marks
        $normalized = Normalizer::normalize($this->value, Normalizer::NFD);

        if (!is_string($normalized)) {
            return $this;
        }

        // Remove combining diacritical marks (Mn = Mark, nonspacing)
        $cleaned = (string) preg_replace('/\p{Mn}/u', '', $normalized);

        return new self($cleaned);
    }

    /**
     * Collapse multiple whitespace characters into single spaces.
     *
     * @example Babel::from('Hello    World')->collapseWhitespace()->value() // "Hello World"
     * @example Babel::from("Hello\t\n\tWorld")->collapseWhitespace()->value() // "Hello World"
     */
    public function collapseWhitespace(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $collapsed = (string) preg_replace('/\s+/u', ' ', $this->value);

        return new self(mb_trim($collapsed));
    }
}
