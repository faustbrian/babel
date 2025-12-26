<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;
use Cline\Babel\Exceptions\TransliterationException;

describe('Normalization', function (): void {
    describe('normalize', function (): void {
        test('normalizes Unicode strings', function (): void {
            $babel = Babel::from('café');
            $normalized = $babel->normalize();

            expect($normalized)->toBeInstanceOf(Babel::class);
            expect($normalized->value())->not->toBeNull();
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->normalize())->toBe($babel);
        });
    });

    describe('removeBom', function (): void {
        test('removes UTF-8 BOM', function (): void {
            expect(Babel::from("\xEF\xBB\xBFHello")->removeBom()->value())->toBe('Hello');
        });

        test('preserves string without BOM', function (): void {
            expect(Babel::from('Hello')->removeBom()->value())->toBe('Hello');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeBom())->toBe($babel);
        });
    });

    describe('removeNonPrintable', function (): void {
        test('removes non-printable characters', function (): void {
            expect(Babel::from("Hello\x00World")->removeNonPrintable()->value())->toBe('HelloWorld');
            expect(Babel::from("Hello\x07World")->removeNonPrintable()->value())->toBe('HelloWorld');
        });

        test('preserves newlines and tabs', function (): void {
            expect(Babel::from("Hello\nWorld")->removeNonPrintable()->value())->toBe("Hello\nWorld");
            expect(Babel::from("Hello\tWorld")->removeNonPrintable()->value())->toBe("Hello\tWorld");
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeNonPrintable())->toBe($babel);
        });
    });

    describe('removeControlChars', function (): void {
        test('removes control characters', function (): void {
            expect(Babel::from("Hello\x07World")->removeControlChars()->value())->toBe('HelloWorld');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeControlChars())->toBe($babel);
        });
    });

    describe('removeInvisible', function (): void {
        test('removes zero-width characters', function (): void {
            expect(Babel::from("Hello\u{200B}World")->removeInvisible()->value())->toBe('HelloWorld');
            expect(Babel::from("Hello\u{FEFF}World")->removeInvisible()->value())->toBe('HelloWorld');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeInvisible())->toBe($babel);
        });
    });

    describe('removeEmoji', function (): void {
        test('removes emoji characters', function (): void {
            expect(Babel::from('Hello 👋 World')->removeEmoji()->value())->toBe('Hello  World');
            expect(Babel::from('🎉 Party 🎊')->removeEmoji()->value())->toBe(' Party ');
        });

        test('preserves text without emoji', function (): void {
            expect(Babel::from('Hello World')->removeEmoji()->value())->toBe('Hello World');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeEmoji())->toBe($babel);
        });
    });

    describe('removeScript', function (): void {
        test('removes specified script', function (): void {
            expect(Babel::from('Hello Привет')->removeScript('Cyrillic')->value())->toBe('Hello ');
        });

        test('preserves other scripts', function (): void {
            expect(Babel::from('Hello World')->removeScript('Cyrillic')->value())->toBe('Hello World');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeScript('Cyrillic'))->toBe($babel);
        });
    });

    describe('removeDiacritics', function (): void {
        test('removes accents from characters', function (): void {
            expect(Babel::from('café')->removeDiacritics()->value())->toBe('cafe');
            expect(Babel::from('Ñoño')->removeDiacritics()->value())->toBe('Nono');
            expect(Babel::from('Żółć')->removeDiacritics()->value())->toBe('Zołc'); // ł is not a diacritic
        });

        test('preserves plain ASCII', function (): void {
            expect(Babel::from('Hello')->removeDiacritics()->value())->toBe('Hello');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->removeDiacritics())->toBe($babel);
        });
    });

    describe('collapseWhitespace', function (): void {
        test('collapses multiple spaces', function (): void {
            expect(Babel::from('Hello    World')->collapseWhitespace()->value())->toBe('Hello World');
        });

        test('collapses mixed whitespace', function (): void {
            expect(Babel::from("Hello\t\n\tWorld")->collapseWhitespace()->value())->toBe('Hello World');
        });

        test('trims leading and trailing whitespace', function (): void {
            expect(Babel::from('  Hello World  ')->collapseWhitespace()->value())->toBe('Hello World');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->collapseWhitespace())->toBe($babel);
        });
    });

    describe('transliterate', function (): void {
        test('applies default transliteration', function (): void {
            expect(Babel::from('Żółć')->transliterate()->value())->toBe('Zolc');
        });

        test('applies custom rules', function (): void {
            expect(Babel::from('HELLO')->transliterate('Upper; Lower')->value())->toBe('hello');
        });

        test('throws exception for invalid rules', function (): void {
            expect(fn (): Babel => Babel::from('Hello')->transliterate('InvalidRule123'))
                ->toThrow(TransliterationException::class);
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->transliterate())->toBe($babel);
        });
    });
});
