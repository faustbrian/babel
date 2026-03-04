<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Encoding Detection', function (): void {
    describe('detect', function (): void {
        test('detects ASCII encoding', function (): void {
            expect(Babel::from('Hello')->detect())->toBe('ASCII');
        });

        test('detects UTF-8 encoding', function (): void {
            expect(Babel::from('Héllo')->detect())->toBe('UTF-8');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->detect())->toBeNull();
            expect(Babel::from(null)->detect())->toBeNull();
        });
    });

    describe('isUtf8', function (): void {
        test('returns true for valid UTF-8', function (): void {
            expect(Babel::from('Hello')->isUtf8())->toBeTrue();
            expect(Babel::from('Żółć')->isUtf8())->toBeTrue();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isUtf8())->toBeTrue();
            expect(Babel::from(null)->isUtf8())->toBeTrue();
        });
    });

    describe('isAscii', function (): void {
        test('returns true for ASCII only', function (): void {
            expect(Babel::from('Hello')->isAscii())->toBeTrue();
            expect(Babel::from('Hello123!')->isAscii())->toBeTrue();
        });

        test('returns false for non-ASCII', function (): void {
            expect(Babel::from('Héllo')->isAscii())->toBeFalse();
            expect(Babel::from('Żółć')->isAscii())->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isAscii())->toBeTrue();
            expect(Babel::from(null)->isAscii())->toBeTrue();
        });
    });

    describe('isValidEncoding', function (): void {
        test('validates encoding', function (): void {
            expect(Babel::from('Hello')->isValidEncoding('UTF-8'))->toBeTrue();
            expect(Babel::from('Hello')->isValidEncoding('ASCII'))->toBeTrue();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isValidEncoding('UTF-8'))->toBeTrue();
            expect(Babel::from(null)->isValidEncoding('UTF-8'))->toBeTrue();
        });
    });
});
