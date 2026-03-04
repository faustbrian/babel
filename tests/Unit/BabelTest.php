<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Babel', function (): void {
    describe('factory', function (): void {
        test('creates instance from string', function (): void {
            $babel = Babel::from('Hello');

            expect($babel)->toBeInstanceOf(Babel::class);
            expect($babel->value())->toBe('Hello');
        });

        test('creates instance from null', function (): void {
            $babel = Babel::from(null);

            expect($babel)->toBeInstanceOf(Babel::class);
            expect($babel->value())->toBeNull();
        });

        test('creates instance from empty string', function (): void {
            $babel = Babel::from('');

            expect($babel)->toBeInstanceOf(Babel::class);
            expect($babel->value())->toBe('');
        });
    });

    describe('utilities', function (): void {
        test('length returns character count', function (): void {
            expect(Babel::from('Hello')->length())->toBe(5);
            expect(Babel::from('Żółć')->length())->toBe(4);
            expect(Babel::from('')->length())->toBe(0);
            expect(Babel::from(null)->length())->toBe(0);
        });

        test('bytes returns byte count', function (): void {
            expect(Babel::from('Hello')->bytes())->toBe(5);
            expect(Babel::from('Żółć')->bytes())->toBe(8); // 4 chars × 2 bytes each
            expect(Babel::from('')->bytes())->toBe(0);
            expect(Babel::from(null)->bytes())->toBe(0);
        });

        test('isEmpty returns true for empty or null', function (): void {
            expect(Babel::from('')->isEmpty())->toBeTrue();
            expect(Babel::from(null)->isEmpty())->toBeTrue();
            expect(Babel::from('Hello')->isEmpty())->toBeFalse();
        });

        test('isNotEmpty returns true for non-empty', function (): void {
            expect(Babel::from('Hello')->isNotEmpty())->toBeTrue();
            expect(Babel::from('')->isNotEmpty())->toBeFalse();
            expect(Babel::from(null)->isNotEmpty())->toBeFalse();
        });

        test('__toString returns string value', function (): void {
            expect((string) Babel::from('Hello'))->toBe('Hello');
            expect((string) Babel::from(null))->toBe('');
            expect((string) Babel::from(''))->toBe('');
        });

        test('graphemes splits into grapheme clusters', function (): void {
            expect(Babel::from('Hello')->graphemes())->toBe(['H', 'e', 'l', 'l', 'o']);
            expect(Babel::from('café')->graphemes())->toBe(['c', 'a', 'f', 'é']);
            expect(Babel::from('')->graphemes())->toBe([]);
            expect(Babel::from(null)->graphemes())->toBe([]);
        });

        test('reverse preserves grapheme clusters', function (): void {
            expect(Babel::from('Hello')->reverse()->value())->toBe('olleH');
            expect(Babel::from('café')->reverse()->value())->toBe('éfac');
        });

        test('reverse returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->reverse())->toBe($babel);
        });
    });
});
