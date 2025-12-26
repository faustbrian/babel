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

/**
 * Provides script detection methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait DetectsScripts
{
    /**
     * Check if string contains Asian characters (Han, Hiragana, Katakana, Hangul).
     *
     * @example Babel::from('Hello 世界')->containsAsian() // true
     * @example Babel::from('Hello')->containsAsian() // false
     */
    public function containsAsian(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Han}|\p{Hiragana}|\p{Katakana}|\p{Hangul}/u', $this->value);
    }

    /**
     * Check if string contains Chinese characters (Han script).
     *
     * @example Babel::from('你好')->containsChinese() // true
     */
    public function containsChinese(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Han}/u', $this->value);
    }

    /**
     * Check if string contains Japanese characters (Hiragana, Katakana, or Han).
     *
     * @example Babel::from('こんにちは')->containsJapanese() // true
     * @example Babel::from('カタカナ')->containsJapanese() // true
     */
    public function containsJapanese(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Hiragana}|\p{Katakana}/u', $this->value);
    }

    /**
     * Check if string contains Korean characters (Hangul).
     *
     * @example Babel::from('안녕하세요')->containsKorean() // true
     */
    public function containsKorean(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Hangul}/u', $this->value);
    }

    /**
     * Check if string contains Cyrillic characters.
     *
     * @example Babel::from('Привет')->containsCyrillic() // true
     */
    public function containsCyrillic(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Cyrillic}/u', $this->value);
    }

    /**
     * Check if string contains Arabic characters.
     *
     * @example Babel::from('مرحبا')->containsArabic() // true
     */
    public function containsArabic(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Arabic}/u', $this->value);
    }

    /**
     * Check if string contains Hebrew characters.
     *
     * @example Babel::from('שלום')->containsHebrew() // true
     */
    public function containsHebrew(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Hebrew}/u', $this->value);
    }

    /**
     * Check if string contains Greek characters.
     *
     * @example Babel::from('Γειά σου')->containsGreek() // true
     */
    public function containsGreek(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Greek}/u', $this->value);
    }

    /**
     * Check if string contains Thai characters.
     *
     * @example Babel::from('สวัสดี')->containsThai() // true
     */
    public function containsThai(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Thai}/u', $this->value);
    }

    /**
     * Check if string contains Devanagari characters (Hindi, Sanskrit, etc.).
     *
     * @example Babel::from('नमस्ते')->containsDevanagari() // true
     */
    public function containsDevanagari(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Devanagari}/u', $this->value);
    }

    /**
     * Check if string contains Bengali characters.
     *
     * @example Babel::from('বাংলা')->containsBengali() // true
     */
    public function containsBengali(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Bengali}/u', $this->value);
    }

    /**
     * Check if string contains Tamil characters.
     *
     * @example Babel::from('தமிழ்')->containsTamil() // true
     */
    public function containsTamil(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Tamil}/u', $this->value);
    }

    /**
     * Check if string contains Vietnamese characters (Latin with unique diacritics).
     *
     * @example Babel::from('Việt Nam')->containsVietnamese() // true
     */
    public function containsVietnamese(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // Vietnamese uses Latin script with unique diacritics and tone marks
        // Check for Vietnamese-specific characters: ă, â, đ, ê, ô, ơ, ư and their tones
        return (bool) preg_match('/[ăâđêôơưĂÂĐÊÔƠƯ]|[àảãáạ]|[èẻẽéẹ]|[ìỉĩíị]|[òỏõóọ]|[ùủũúụ]|[ằẳẵắặ]|[ầẩẫấậ]|[ềểễếệ]|[ồổỗốộ]|[ờởỡớợ]|[ừửữứự]|[ỳỷỹýỵ]/u', $this->value);
    }

    /**
     * Check if string contains Armenian characters.
     *
     * @example Babel::from('Հայաստusage')->containsArmenian() // true
     */
    public function containsArmenian(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Armenian}/u', $this->value);
    }

    /**
     * Check if string contains Georgian characters.
     *
     * @example Babel::from('საქართველო')->containsGeorgian() // true
     */
    public function containsGeorgian(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Georgian}/u', $this->value);
    }

    /**
     * Check if string contains Latin characters.
     *
     * @example Babel::from('Hello')->containsLatin() // true
     */
    public function containsLatin(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{Latin}/u', $this->value);
    }

    /**
     * Check if string contains emoji.
     *
     * @example Babel::from('Hello 👋')->containsEmoji() // true
     */
    public function containsEmoji(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        // Match emoji characters including:
        // - Emoticons (U+1F600-U+1F64F)
        // - Misc Symbols and Pictographs (U+1F300-U+1F5FF)
        // - Transport and Map (U+1F680-U+1F6FF)
        // - Flags (U+1F1E0-U+1F1FF)
        // - Supplemental Symbols (U+1F900-U+1F9FF)
        // - Additional symbols (U+2600-U+26FF, U+2700-U+27BF)
        return (bool) preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $this->value);
    }

    /**
     * Check if string contains a specific Unicode script.
     *
     * @example Babel::from('Привет')->containsScript('Cyrillic') // true
     * @example Babel::from('Hello')->containsScript('Devanagari') // false
     */
    public function containsScript(string $script): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        return (bool) preg_match('/\p{'.$script.'}/u', $this->value);
    }

    /**
     * Check if string contains only Latin characters and common punctuation.
     *
     * @example Babel::from('Hello, World!')->isLatin() // true
     * @example Babel::from('Hello 世界')->isLatin() // false
     */
    public function isLatin(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        // Allow Latin characters, numbers, punctuation, and whitespace
        return (bool) preg_match('/^[\p{Latin}\p{N}\p{P}\p{Z}\p{S}]*$/u', $this->value);
    }

    /**
     * Check if string contains only numeric characters.
     *
     * @example Babel::from('12345')->isNumeric() // true
     * @example Babel::from('12.34')->isNumeric() // false
     */
    public function isNumeric(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return (bool) preg_match('/^\d+$/', $this->value);
    }

    /**
     * Check if string contains only alphanumeric characters.
     *
     * @example Babel::from('Hello123')->isAlphanumeric() // true
     * @example Babel::from('Hello 123')->isAlphanumeric() // false
     */
    public function isAlphanumeric(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return (bool) preg_match('/^[\p{L}\p{N}]+$/u', $this->value);
    }

    /**
     * Check if string contains only characters from a specific script.
     *
     * @example Babel::from('Привет')->isScript('Cyrillic') // true
     */
    public function isScript(string $script): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        // Allow the specified script plus common characters (numbers, punctuation, whitespace)
        return (bool) preg_match('/^[\p{'.$script.'}\p{N}\p{P}\p{Z}\p{S}]*$/u', $this->value);
    }
}
