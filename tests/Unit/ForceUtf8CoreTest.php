<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Encodings\ForceUtf8;

describe('ForceUtf8 core', function (): void {
    test('returns non-string values unchanged', function (): void {
        expect(ForceUtf8::toUTF8(123))->toBe(123)
            ->and(ForceUtf8::fixUTF8(true))->toBeTrue()
            ->and(ForceUtf8::toWin1252(42))->toBe(42);
    });

    test('converts nested arrays recursively', function (): void {
        $value = [
            mb_convert_encoding('Hírek', 'ISO-8859-1', 'UTF-8'),
            ['inner' => mb_convert_encoding('árvíz', 'ISO-8859-1', 'UTF-8')],
        ];

        expect(ForceUtf8::toUTF8($value))->toBe([
            'Hírek',
            ['inner' => 'árvíz'],
        ]);
    });

    test('toWin1252 aliases return same result', function (): void {
        $input = 'Fédération';

        $expected = ForceUtf8::toWin1252($input);

        expect(ForceUtf8::toISO8859($input))->toBe($expected)
            ->and(ForceUtf8::toLatin1($input))->toBe($expected);
    });

    test('toWin1252 can use iconv options path', function (): void {
        $input = 'Plain ASCII';

        expect(ForceUtf8::toWin1252($input, ForceUtf8::ICONV_TRANSLIT))->toBe($input)
            ->and(ForceUtf8::toWin1252($input, ForceUtf8::ICONV_IGNORE))->toBe($input);
    });

    test('fixes known broken win1252 chars and removes bom', function (): void {
        expect(ForceUtf8::UTF8FixWin1252Chars("\xc2\x80"))->toBe('€')
            ->and(ForceUtf8::removeBOM("\xEF\xBB\xBFHello"))->toBe('Hello')
            ->and(ForceUtf8::removeBOM('Hello'))->toBe('Hello');
    });

    test('normalizes encoding aliases and falls back to utf8', function (): void {
        expect(ForceUtf8::normalizeEncoding('latin1'))->toBe('ISO-8859-1')
            ->and(ForceUtf8::normalizeEncoding('windows-1252'))->toBe('ISO-8859-1')
            ->and(ForceUtf8::normalizeEncoding('utf8'))->toBe('UTF-8')
            ->and(ForceUtf8::normalizeEncoding('unknown-encoding'))->toBe('UTF-8');
    });

    test('encode routes to latin1 or utf8 behavior', function (): void {
        $latinSource = 'Fédération';
        $utf8Source = mb_convert_encoding('Hírek', 'ISO-8859-1', 'UTF-8');

        expect(ForceUtf8::encode('latin', $latinSource))->toBe(ForceUtf8::toLatin1($latinSource))
            ->and(ForceUtf8::encode('utf', $utf8Source))->toBe(ForceUtf8::toUTF8($utf8Source));
    });
});
