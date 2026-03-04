<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Concerns;

use Cline\Babel\Babel;

use function preg_match;
use function preg_match_all;

/**
 * Provides text directionality detection methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait DetectsDirectionality
{
    /**
     * Check if string is predominantly right-to-left.
     *
     * @example Babel::from('مرحبا بك')->isRtl() // true
     * @example Babel::from('Hello')->isRtl() // false
     */
    public function isRtl(): bool
    {
        return $this->direction() === 'rtl';
    }

    /**
     * Check if string contains any right-to-left characters.
     *
     * @example Babel::from('Hello مرحبا')->containsRtl() // true
     * @example Babel::from('Hello World')->containsRtl() // false
     */
    public function containsRtl(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // RTL scripts: Arabic, Hebrew, Syriac, Thaana, NKo, etc.
        return (bool) preg_match('/[\p{Arabic}\p{Hebrew}\p{Syriac}\p{Thaana}\p{Nko}]/u', $this->value);
    }

    /**
     * Get the dominant text direction.
     *
     * @return string 'ltr' | 'rtl' | 'mixed' | 'neutral'
     *
     * @example Babel::from('Hello')->direction() // "ltr"
     * @example Babel::from('مرحبا')->direction() // "rtl"
     * @example Babel::from('Hello مرحبا')->direction() // "mixed"
     * @example Babel::from('123')->direction() // "neutral"
     */
    public function direction(): string
    {
        if ($this->isEmpty()) {
            return 'neutral';
        }

        // Count RTL and LTR characters
        $rtlCount = (int) preg_match_all('/[\p{Arabic}\p{Hebrew}\p{Syriac}\p{Thaana}\p{Nko}]/u', $this->value);
        $ltrCount = (int) preg_match_all('/[\p{Latin}\p{Greek}\p{Cyrillic}\p{Armenian}\p{Georgian}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{Thai}\p{Lao}\p{Myanmar}\p{Khmer}\p{Ethiopic}]/u', $this->value);

        if ($rtlCount === 0 && $ltrCount === 0) {
            return 'neutral';
        }

        if ($rtlCount > 0 && $ltrCount > 0) {
            // If both exist, determine dominant direction
            $total = $rtlCount + $ltrCount;
            $rtlRatio = $rtlCount / $total;

            // If more than 70% RTL, it's RTL dominant
            if ($rtlRatio > 0.7) {
                return 'rtl';
            }

            // If less than 30% RTL, it's LTR dominant
            if ($rtlRatio < 0.3) {
                return 'ltr';
            }

            return 'mixed';
        }

        return $rtlCount > 0 ? 'rtl' : 'ltr';
    }
}
