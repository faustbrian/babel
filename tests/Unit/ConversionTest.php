<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Conversion', function (): void {
    describe('toAscii', function (): void {
        test('converts accented characters', function (): void {
            expect(Babel::from('Café')->toAscii())->toBe('Cafe');
            expect(Babel::from('naïve')->toAscii())->toBe('naive');
        });

        test('converts Polish characters', function (): void {
            expect(Babel::from('Żółć')->toAscii())->toBe('Zolc');
            expect(Babel::from('ąęłńóśźż')->toAscii())->toBe('aelnoszz'); // ICU transliteration
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toAscii())->toBeNull();
            expect(Babel::from(null)->toAscii())->toBeNull();
        });

        test('preserves ASCII characters', function (): void {
            expect(Babel::from('Hello World')->toAscii())->toBe('Hello World');
        });
    });

    describe('toUtf8', function (): void {
        test('returns UTF-8 string', function (): void {
            expect(Babel::from('Hello')->toUtf8())->toBe('Hello');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toUtf8())->toBeNull();
            expect(Babel::from(null)->toUtf8())->toBeNull();
        });
    });

    describe('toLatin1', function (): void {
        test('converts to Latin-1 with transliteration', function (): void {
            expect(Babel::from('Żółć')->toLatin1())->toBe('Zolc');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toLatin1())->toBeNull();
            expect(Babel::from(null)->toLatin1())->toBeNull();
        });
    });

    describe('toHtmlEntities', function (): void {
        test('converts special characters', function (): void {
            expect(Babel::from('<script>')->toHtmlEntities())->toBe('&lt;script&gt;');
            expect(Babel::from('"quoted"')->toHtmlEntities())->toBe('&quot;quoted&quot;');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toHtmlEntities())->toBeNull();
            expect(Babel::from(null)->toHtmlEntities())->toBeNull();
        });
    });

    describe('fromHtmlEntities', function (): void {
        test('decodes HTML entities', function (): void {
            expect(Babel::from('&lt;script&gt;')->fromHtmlEntities()->value())->toBe('<script>');
            expect(Babel::from('&quot;quoted&quot;')->fromHtmlEntities()->value())->toBe('"quoted"');
        });

        test('returns same instance for empty input', function (): void {
            $babel = Babel::from('');
            expect($babel->fromHtmlEntities())->toBe($babel);
        });
    });

    describe('toSlug', function (): void {
        test('creates URL-safe slug', function (): void {
            expect(Babel::from('Hello World!')->toSlug())->toBe('hello-world');
            expect(Babel::from('Żółć zażółć')->toSlug())->toBe('zolc-zazolc');
        });

        test('uses custom separator', function (): void {
            expect(Babel::from('Hello World')->toSlug('_'))->toBe('hello_world');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toSlug())->toBeNull();
            expect(Babel::from(null)->toSlug())->toBeNull();
        });
    });

    describe('toFilename', function (): void {
        test('creates safe filename', function (): void {
            expect(Babel::from('My File.txt')->toFilename())->toBe('my_file.txt');
        });

        test('uses custom separator', function (): void {
            expect(Babel::from('My File.txt')->toFilename('-'))->toBe('my-file.txt');
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toFilename())->toBeNull();
            expect(Babel::from(null)->toFilename())->toBeNull();
        });
    });

    describe('toXmlSafe', function (): void {
        test('removes invalid XML characters', function (): void {
            expect(Babel::from("Hello\x00World")->toXmlSafe())->toBe('HelloWorld');
        });

        test('preserves valid characters', function (): void {
            expect(Babel::from("Hello\nWorld")->toXmlSafe())->toBe("Hello\nWorld");
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toXmlSafe())->toBeNull();
            expect(Babel::from(null)->toXmlSafe())->toBeNull();
        });
    });

    describe('toEncoding', function (): void {
        test('converts to specified encoding', function (): void {
            $result = Babel::from('Hello')->toEncoding('UTF-16');
            expect($result)->not->toBeNull();
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toEncoding('UTF-16'))->toBeNull();
            expect(Babel::from(null)->toEncoding('UTF-16'))->toBeNull();
        });
    });
});
