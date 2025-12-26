<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Exceptions\EncodingConversionFailedException;
use Cline\Babel\Exceptions\EncodingDetectionFailedException;
use Cline\Babel\Exceptions\EncodingException;
use Cline\Babel\Exceptions\InvalidEncodingException;
use Cline\Babel\Exceptions\TransliterationException;
use Cline\Babel\Exceptions\TransliterationFailedException;

describe('Exceptions', function (): void {
    describe('EncodingConversionFailedException', function (): void {
        test('creates exception from encodings', function (): void {
            $exception = EncodingConversionFailedException::fromEncodings('UTF-8', 'ISO-8859-1');

            expect($exception)->toBeInstanceOf(EncodingConversionFailedException::class);
            expect($exception)->toBeInstanceOf(EncodingException::class);
            expect($exception->getMessage())->toBe('Failed to convert from UTF-8 to ISO-8859-1');
        });
    });

    describe('EncodingDetectionFailedException', function (): void {
        test('creates exception for string', function (): void {
            $exception = EncodingDetectionFailedException::forString();

            expect($exception)->toBeInstanceOf(EncodingDetectionFailedException::class);
            expect($exception)->toBeInstanceOf(EncodingException::class);
            expect($exception->getMessage())->toBe('Failed to detect string encoding');
        });
    });

    describe('InvalidEncodingException', function (): void {
        test('creates exception for invalid encoding name', function (): void {
            $exception = InvalidEncodingException::forName('INVALID-ENCODING');

            expect($exception)->toBeInstanceOf(InvalidEncodingException::class);
            expect($exception)->toBeInstanceOf(EncodingException::class);
            expect($exception->getMessage())->toBe('Invalid encoding: INVALID-ENCODING');
        });
    });

    describe('TransliterationFailedException', function (): void {
        test('creates exception with rules', function (): void {
            $exception = TransliterationFailedException::withRules('Any-Latin');

            expect($exception)->toBeInstanceOf(TransliterationFailedException::class);
            expect($exception)->toBeInstanceOf(TransliterationException::class);
            expect($exception->getMessage())->toBe('Transliteration failed with rules: Any-Latin');
        });
    });
});
