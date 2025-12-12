<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Character Analysis', function (): void {
    describe('containsNonPrintable', function (): void {
        test('detects non-printable characters', function (): void {
            expect(Babel::from("Hello\x00World")->containsNonPrintable())->toBeTrue();
            expect(Babel::from("Hello\x07World")->containsNonPrintable())->toBeTrue();
        });

        test('returns false for printable text', function (): void {
            expect(Babel::from('Hello World')->containsNonPrintable())->toBeFalse();
            expect(Babel::from("Hello\nWorld")->containsNonPrintable())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsNonPrintable())->toBeFalse();
            expect(Babel::from(null)->containsNonPrintable())->toBeFalse();
        });
    });

    describe('containsControlChars', function (): void {
        test('detects control characters', function (): void {
            expect(Babel::from("Hello\x07World")->containsControlChars())->toBeTrue();
            expect(Babel::from("Hello\x00World")->containsControlChars())->toBeTrue();
        });

        test('returns false for normal text', function (): void {
            expect(Babel::from('Hello World')->containsControlChars())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsControlChars())->toBeFalse();
            expect(Babel::from(null)->containsControlChars())->toBeFalse();
        });
    });

    describe('isWhitespace', function (): void {
        test('returns true for whitespace only', function (): void {
            expect(Babel::from("   \t\n")->isWhitespace())->toBeTrue();
            expect(Babel::from('   ')->isWhitespace())->toBeTrue();
        });

        test('returns false for non-whitespace', function (): void {
            expect(Babel::from('Hello')->isWhitespace())->toBeFalse();
            expect(Babel::from(' Hello ')->isWhitespace())->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isWhitespace())->toBeTrue();
            expect(Babel::from(null)->isWhitespace())->toBeTrue();
        });
    });

    describe('containsInvisible', function (): void {
        test('detects zero-width characters', function (): void {
            expect(Babel::from("Hello\u{200B}World")->containsInvisible())->toBeTrue();
            expect(Babel::from("Hello\u{FEFF}World")->containsInvisible())->toBeTrue();
        });

        test('returns false for visible text', function (): void {
            expect(Babel::from('Hello World')->containsInvisible())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsInvisible())->toBeFalse();
            expect(Babel::from(null)->containsInvisible())->toBeFalse();
        });
    });

    describe('containsHomoglyphs', function (): void {
        test('detects Cyrillic lookalikes', function (): void {
            // Using Cyrillic 'а' instead of Latin 'a'
            expect(Babel::from('Hеllo')->containsHomoglyphs())->toBeTrue();
        });

        test('returns false for pure Latin', function (): void {
            expect(Babel::from('Hello')->containsHomoglyphs())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsHomoglyphs())->toBeFalse();
            expect(Babel::from(null)->containsHomoglyphs())->toBeFalse();
        });
    });

    describe('containsMixedScripts', function (): void {
        test('detects mixed Latin and Cyrillic', function (): void {
            expect(Babel::from('Hello Привет')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Chinese', function (): void {
            expect(Babel::from('Hello 世界')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Arabic', function (): void {
            expect(Babel::from('Hello مرحبا')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Greek', function (): void {
            expect(Babel::from('Hello Ελληνικά')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Hebrew', function (): void {
            expect(Babel::from('Hello שלום')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Japanese', function (): void {
            expect(Babel::from('Hello こんにちは')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Korean', function (): void {
            expect(Babel::from('Hello 안녕하세요')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Devanagari', function (): void {
            expect(Babel::from('Hello नमस्ते')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Bengali', function (): void {
            expect(Babel::from('Hello বাংলা')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Tamil', function (): void {
            expect(Babel::from('Hello தமிழ்')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Thai', function (): void {
            expect(Babel::from('Hello สวัสดี')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Armenian', function (): void {
            expect(Babel::from('Hello Հայաստdelays')->containsMixedScripts())->toBeTrue();
        });

        test('detects mixed Latin and Georgian', function (): void {
            expect(Babel::from('Hello გამარჯობა')->containsMixedScripts())->toBeTrue();
        });

        test('returns false for single script', function (): void {
            expect(Babel::from('Hello World')->containsMixedScripts())->toBeFalse();
            expect(Babel::from('Привет мир')->containsMixedScripts())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsMixedScripts())->toBeFalse();
            expect(Babel::from(null)->containsMixedScripts())->toBeFalse();
        });
    });

    describe('hasBom', function (): void {
        test('detects UTF-8 BOM', function (): void {
            expect(Babel::from("\xEF\xBB\xBFHello")->hasBom())->toBeTrue();
        });

        test('detects UTF-16 BE BOM', function (): void {
            expect(Babel::from("\xFE\xFFHello")->hasBom())->toBeTrue();
        });

        test('detects UTF-16 LE BOM', function (): void {
            expect(Babel::from("\xFF\xFEHello")->hasBom())->toBeTrue();
        });

        test('returns false for no BOM', function (): void {
            expect(Babel::from('Hello')->hasBom())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->hasBom())->toBeFalse();
            expect(Babel::from(null)->hasBom())->toBeFalse();
        });
    });
});
