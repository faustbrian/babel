<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Encodings;

use function array_keys;
use function array_values;
use function chr;
use function iconv;
use function is_array;
use function is_string;
use function mb_convert_encoding;
use function ord;
use function pack;
use function preg_replace;
use function str_replace;
use function strlen;
use function strtoupper;
use function substr;

/**
 * Core UTF-8 repair and conversion primitives.
 *
 * Ported from neitanod/forceutf8 and adapted to Babel's PHP 8+ baseline.
 * @author Brian Faust <brian@cline.sh>
 */
final class ForceUtf8
{
    /**
     * Use iconv transliteration in decode operations.
     */
    public const ICONV_TRANSLIT = 'TRANSLIT';

    /**
     * Use iconv ignore behavior in decode operations.
     */
    public const ICONV_IGNORE = 'IGNORE';

    /**
     * Use pure conversion logic without iconv options.
     */
    public const WITHOUT_ICONV = '';

    /** @var array<int, string> */
    protected static array $win1252ToUtf8 = [
        128 => "\xe2\x82\xac",
        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",
        142 => "\xc5\xbd",
        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",
        158 => "\xc5\xbe",
        159 => "\xc5\xb8",
    ];

    /** @var array<string, string> */
    protected static array $brokenUtf8ToUtf8 = [
        "\xc2\x80" => "\xe2\x82\xac",
        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",
        "\xc2\x8e" => "\xc5\xbd",
        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",
        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8",
    ];

    /** @var array<string, string> */
    protected static array $utf8ToWin1252 = [
        "\xe2\x82\xac" => "\x80",
        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92" => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86" => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0" => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92" => "\x8c",
        "\xc5\xbd" => "\x8e",
        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c" => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1" => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93" => "\x9c",
        "\xc5\xbe" => "\x9e",
        "\xc5\xb8" => "\x9f",
    ];

    /**
     * Convert text recursively to UTF-8.
     *
     * Arrays are converted recursively, non-string values are returned
     * unchanged.
     */
    public static function toUTF8(mixed $text): mixed
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::toUTF8($value);
            }

            return $text;
        }

        if (!is_string($text)) {
            return $text;
        }

        $max = self::byteLength($text);
        $buffer = '';

        for ($i = 0; $i < $max; ++$i) {
            $c1 = $text[$i];

            if ($c1 >= "\xc0") {
                $c2 = $i + 1 >= $max ? "\x00" : $text[$i + 1];
                $c3 = $i + 2 >= $max ? "\x00" : $text[$i + 2];
                $c4 = $i + 3 >= $max ? "\x00" : $text[$i + 3];

                if ($c1 >= "\xc0" && $c1 <= "\xdf") {
                    if ($c2 >= "\x80" && $c2 <= "\xbf") {
                        $buffer .= $c1.$c2;
                        ++$i;
                    } else {
                        $cc1 = self::leadByte($c1) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buffer .= $cc1.$cc2;
                    }
                } elseif ($c1 >= "\xe0" && $c1 <= "\xef") {
                    if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf") {
                        $buffer .= $c1.$c2.$c3;
                        $i += 2;
                    } else {
                        $cc1 = self::leadByte($c1) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buffer .= $cc1.$cc2;
                    }
                } elseif ($c1 >= "\xf0" && $c1 <= "\xf7") {
                    if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf") {
                        $buffer .= $c1.$c2.$c3.$c4;
                        $i += 3;
                    } else {
                        $cc1 = self::leadByte($c1) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buffer .= $cc1.$cc2;
                    }
                } else {
                    $cc1 = self::leadByte($c1) | "\xc0";
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buffer .= $cc1.$cc2;
                }
            } elseif (($c1 & "\xc0") === "\x80") {
                if (isset(self::$win1252ToUtf8[ord($c1)])) {
                    $buffer .= self::$win1252ToUtf8[ord($c1)];
                } else {
                    $cc1 = self::leadByte($c1) | "\xc0";
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buffer .= $cc1.$cc2;
                }
            } else {
                $buffer .= $c1;
            }
        }

        return $buffer;
    }

    /**
     * Convert text recursively from UTF-8 to Windows-1252/Latin-1 bytes.
     */
    public static function toWin1252(mixed $text, string $option = self::WITHOUT_ICONV): mixed
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::toWin1252($value, $option);
            }

            return $text;
        }

        if (is_string($text)) {
            return self::utf8Decode($text, $option);
        }

        return $text;
    }

    /**
     * Alias for {@see self::toWin1252()}.
     */
    public static function toISO8859(mixed $text, string $option = self::WITHOUT_ICONV): mixed
    {
        return self::toWin1252($text, $option);
    }

    /**
     * Alias for {@see self::toWin1252()}.
     */
    public static function toLatin1(mixed $text, string $option = self::WITHOUT_ICONV): mixed
    {
        return self::toWin1252($text, $option);
    }

    /**
     * Repair double-encoded or broken UTF-8 recursively.
     */
    public static function fixUTF8(mixed $text, string $option = self::WITHOUT_ICONV): mixed
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = self::fixUTF8($value, $option);
            }

            return $text;
        }

        if (!is_string($text)) {
            return $text;
        }

        $last = '';

        while ($last !== $text) {
            $last = $text;
            $text = self::toUtf8String(self::utf8Decode($text, $option));
        }

        return self::toUtf8String(self::utf8Decode($text, $option));
    }

    /**
     * Fix UTF-8 text that was decoded as ISO-8859-1 from Windows-1252 bytes.
     */
    public static function UTF8FixWin1252Chars(string $text): string
    {
        return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
    }

    /**
     * Remove an UTF-8 BOM prefix if present.
     */
    public static function removeBOM(string $text = ''): string
    {
        if (substr($text, 0, 3) === pack('CCC', 0xEF, 0xBB, 0xBF)) {
            return substr($text, 3);
        }

        return $text;
    }

    /**
     * Normalize encoding aliases used by legacy callers.
     */
    public static function normalizeEncoding(string $encodingLabel): string
    {
        $encoding = strtoupper($encodingLabel);
        $encoding = (string) preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);

        $equivalences = [
            'ISO88591' => 'ISO-8859-1',
            'ISO8859' => 'ISO-8859-1',
            'ISO' => 'ISO-8859-1',
            'LATIN1' => 'ISO-8859-1',
            'LATIN' => 'ISO-8859-1',
            'UTF8' => 'UTF-8',
            'UTF' => 'UTF-8',
            'WIN1252' => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1',
        ];

        return $equivalences[$encoding] ?? 'UTF-8';
    }

    /**
     * Encode to either UTF-8 or Latin-1 based on normalized encoding label.
     */
    public static function encode(string $encodingLabel, mixed $text): mixed
    {
        $encodingLabel = self::normalizeEncoding($encodingLabel);

        if ($encodingLabel === 'ISO-8859-1') {
            return self::toLatin1($text);
        }

        return self::toUTF8($text);
    }

    /**
     * Get byte length without grapheme/multibyte semantics.
     */
    protected static function byteLength(string $text): int
    {
        return strlen($text);
    }

    /**
     * Decode UTF-8 into Windows-1252-compatible bytes.
     */
    protected static function utf8Decode(string $text, string $option = self::WITHOUT_ICONV): string
    {
        if ($option === self::WITHOUT_ICONV) {
            return mb_convert_encoding(
                str_replace(
                    array_keys(self::$utf8ToWin1252),
                    array_values(self::$utf8ToWin1252),
                    self::toUtf8String($text),
                ),
                'ISO-8859-1',
                'UTF-8',
            );
        }

        return (string) iconv(
            'UTF-8',
            'Windows-1252'.($option === self::ICONV_TRANSLIT ? '//TRANSLIT' : ($option === self::ICONV_IGNORE ? '//IGNORE' : '')),
            $text,
        );
    }

    /**
     * Ensure a string return from {@see self::toUTF8()} for internal calls.
     */
    protected static function toUtf8String(string $text): string
    {
        $result = self::toUTF8($text);

        return is_string($result) ? $result : '';
    }

    /**
     * Compute the lead byte for a single-byte char during conversion.
     */
    protected static function leadByte(string $char): string
    {
        return chr(ord($char) >> 6);
    }
}
