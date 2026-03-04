<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Directionality', function (): void {
    describe('isRtl', function (): void {
        test('returns true for RTL text', function (): void {
            expect(Babel::from('مرحبا')->isRtl())->toBeTrue();
            expect(Babel::from('שלום')->isRtl())->toBeTrue();
        });

        test('returns false for LTR text', function (): void {
            expect(Babel::from('Hello')->isRtl())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->isRtl())->toBeFalse();
            expect(Babel::from(null)->isRtl())->toBeFalse();
        });
    });

    describe('containsRtl', function (): void {
        test('returns true for RTL characters', function (): void {
            expect(Babel::from('Hello مرحبا')->containsRtl())->toBeTrue();
        });

        test('returns false for LTR-only text', function (): void {
            expect(Babel::from('Hello World')->containsRtl())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsRtl())->toBeFalse();
            expect(Babel::from(null)->containsRtl())->toBeFalse();
        });
    });

    describe('direction', function (): void {
        test('returns ltr for left-to-right text', function (): void {
            expect(Babel::from('Hello')->direction())->toBe('ltr');
            expect(Babel::from('Привет')->direction())->toBe('ltr');
        });

        test('returns rtl for right-to-left text', function (): void {
            expect(Babel::from('مرحبا')->direction())->toBe('rtl');
            expect(Babel::from('שלום')->direction())->toBe('rtl');
        });

        test('returns mixed for mixed directions', function (): void {
            expect(Babel::from('Hello مرحبا World')->direction())->toBe('mixed');
        });

        test('returns neutral for numbers only', function (): void {
            expect(Babel::from('123')->direction())->toBe('neutral');
        });

        test('returns neutral for empty input', function (): void {
            expect(Babel::from('')->direction())->toBe('neutral');
            expect(Babel::from(null)->direction())->toBe('neutral');
        });
    });
});
