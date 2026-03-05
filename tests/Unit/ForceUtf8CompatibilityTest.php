<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Encodings\ForceUtf8;

function forceUtf8Fixture(string $name): string
{
    $contents = file_get_contents(__DIR__.'/../Fixtures/forceutf8/'.$name);

    if ($contents === false) {
        throw new RuntimeException('Failed to read forceutf8 fixture: '.$name);
    }

    return $contents;
}

describe('ForceUTF8 compatibility', function (): void {
    test('source fixtures are different before conversion', function (): void {
        expect(forceUtf8Fixture('test1.txt'))->not->toBe(forceUtf8Fixture('test1Latin.txt'));
    });

    test('simple encoding works', function (): void {
        expect(ForceUtf8::toUTF8(forceUtf8Fixture('test1Latin.txt')))->toBe(forceUtf8Fixture('test1.txt'));
    });

    test('array encoding works', function (): void {
        $source = [
            forceUtf8Fixture('test1Latin.txt'),
            forceUtf8Fixture('test1.txt'),
            forceUtf8Fixture('test1Latin.txt'),
        ];

        $expected = [
            forceUtf8Fixture('test1.txt'),
            forceUtf8Fixture('test1.txt'),
            forceUtf8Fixture('test1.txt'),
        ];

        expect($source)->not->toBe($expected)
            ->and(ForceUtf8::toUTF8($source))->toBe($expected);
    });

    test('fixUTF8 preserves valid UTF-8', function (): void {
        expect(ForceUtf8::fixUTF8(forceUtf8Fixture('test1.txt')))->toBe(forceUtf8Fixture('test1.txt'));
    });

    test('double-encoded text differs from valid UTF-8', function (): void {
        expect(mb_convert_encoding(forceUtf8Fixture('test1.txt'), 'UTF-8', 'ISO-8859-1'))
            ->not->toBe(forceUtf8Fixture('test1.txt'));
    });

    test('fixUTF8 reverts double-encoded strings', function (): void {
        $doubleEncoded = mb_convert_encoding(forceUtf8Fixture('test1.txt'), 'UTF-8', 'ISO-8859-1');

        expect(ForceUtf8::fixUTF8($doubleEncoded))->toBe(forceUtf8Fixture('test1.txt'));
    });

    test('fixUTF8 reverts double-encoded arrays', function (): void {
        $source = [
            mb_convert_encoding(forceUtf8Fixture('test1Latin.txt'), 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding(forceUtf8Fixture('test1.txt'), 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding(forceUtf8Fixture('test1Latin.txt'), 'UTF-8', 'ISO-8859-1'),
        ];

        $expected = [
            forceUtf8Fixture('test1.txt'),
            forceUtf8Fixture('test1.txt'),
            forceUtf8Fixture('test1.txt'),
        ];

        expect($source)->not->toBe($expected)
            ->and(ForceUtf8::fixUTF8($source))->toBe($expected);
    });

    test('known fixUTF8 examples', function (): void {
        expect(ForceUtf8::fixUTF8("FÃÂ©dération Camerounaise de Football\n"))
            ->toBe("Fédération Camerounaise de Football\n")
            ->and(ForceUtf8::fixUTF8("FÃ©dÃ©ration Camerounaise de Football\n"))
            ->toBe("Fédération Camerounaise de Football\n")
            ->and(ForceUtf8::fixUTF8("FÃÂ©dÃÂ©ration Camerounaise de Football\n"))
            ->toBe("Fédération Camerounaise de Football\n")
            ->and(ForceUtf8::fixUTF8("FÃÂÂÂÂ©dÃÂÂÂÂ©ration Camerounaise de Football\n"))
            ->toBe("Fédération Camerounaise de Football\n");
    });
});
