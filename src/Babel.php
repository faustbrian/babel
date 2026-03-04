<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel;

use Cline\Babel\Concerns\AnalyzesCharacters;
use Cline\Babel\Concerns\ConvertsEncodings;
use Cline\Babel\Concerns\DetectsDirectionality;
use Cline\Babel\Concerns\DetectsEncodings;
use Cline\Babel\Concerns\DetectsScripts;
use Cline\Babel\Concerns\NormalizesStrings;
use Cline\Babel\Contracts\Babel as BabelContract;
use Stringable;

use function array_reverse;
use function grapheme_strlen;
use function grapheme_substr;
use function implode;
use function mb_strlen;

/**
 * Unicode-aware string encoding, conversion, and analysis.
 *
 * Provides a fluent API for working with character encodings, script detection,
 * text directionality, and string sanitization.
 *
 * @example
 * ```php
 * // Basic usage
 * Babel::from('Żółć')->toAscii(); // "Zolc"
 *
 * // Chained operations
 * Babel::from($input)->normalize()->removeBom()->toUtf8();
 *
 * // Script detection
 * Babel::from('Hello 世界')->containsAsian(); // true
 *
 * // Directionality
 * Babel::from('مرحبا')->isRtl(); // true
 * ```
 * @author Brian Faust <brian@cline.sh>
 */
final class Babel implements BabelContract, Stringable
{
    use AnalyzesCharacters;
    use ConvertsEncodings;
    use DetectsDirectionality;
    use DetectsEncodings;
    use DetectsScripts;
    use NormalizesStrings;

    /**
     * Create a new Babel instance.
     */
    public function __construct(
        private readonly ?string $value,
    ) {}

    /**
     * Get the string value.
     *
     * @example (string) Babel::from('Hello') // "Hello"
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }

    /**
     * Create a new Babel instance from a string value.
     *
     * @example Babel::from('Hello World')
     * @example Babel::from(null) // handles null gracefully
     */
    public static function from(?string $value): self
    {
        return new self($value);
    }

    /**
     * Get the raw string value.
     *
     * @example Babel::from('Hello')->value() // "Hello"
     */
    public function value(): ?string
    {
        return $this->value;
    }

    /**
     * Get string length in characters (not bytes).
     *
     * @example Babel::from('Żółć')->length() // 4
     * @example Babel::from('Hello')->length() // 5
     */
    public function length(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return mb_strlen($this->value, 'UTF-8');
    }

    /**
     * Get string length in bytes.
     *
     * @example Babel::from('Żółć')->bytes() // 7
     * @example Babel::from('Hello')->bytes() // 5
     */
    public function bytes(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return mb_strlen($this->value, '8bit');
    }

    /**
     * Check if string is empty or null.
     *
     * @phpstan-assert-if-false non-empty-string $this->value
     *
     * @example Babel::from('')->isEmpty() // true
     * @example Babel::from(null)->isEmpty() // true
     * @example Babel::from('Hello')->isEmpty() // false
     */
    public function isEmpty(): bool
    {
        return $this->value === null || $this->value === '';
    }

    /**
     * Check if string is not empty.
     *
     * @example Babel::from('Hello')->isNotEmpty() // true
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Split string into grapheme clusters.
     *
     * Grapheme clusters properly handle complex characters like emoji with
     * modifiers (👨‍👩‍👧) and combined characters.
     *
     * @return array<int, string>
     *
     * @example Babel::from('Hello')->graphemes() // ['H', 'e', 'l', 'l', 'o']
     * @example Babel::from('👨‍👩‍👧')->graphemes() // ['👨‍👩‍👧'] (single grapheme)
     */
    public function graphemes(): array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $graphemes = [];
        $length = grapheme_strlen($this->value);

        if ($length === false) {
            return [];
        }

        for ($i = 0; $i < $length; ++$i) {
            $grapheme = grapheme_substr($this->value, $i, 1);

            if ($grapheme === false) {
                continue;
            }

            $graphemes[] = $grapheme;
        }

        return $graphemes;
    }

    /**
     * Reverse string preserving grapheme clusters.
     *
     * Unlike simple byte/character reversal, this properly handles multi-byte
     * characters and grapheme clusters like emoji with modifiers.
     *
     * @example Babel::from('Hello')->reverse()->value() // "olleH"
     * @example Babel::from('café')->reverse()->value() // "éfac"
     */
    public function reverse(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $graphemes = $this->graphemes();
        $reversed = implode('', array_reverse($graphemes));

        return new self($reversed);
    }
}
