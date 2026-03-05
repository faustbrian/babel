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

        test('preserves valid UTF-8 Cyrillic input', function (): void {
            expect(Babel::from('Привет')->toUtf8())->toBe('Привет');
        });

        test('keeps mojibake unchanged (use fixUtf8 for repair)', function (): void {
            expect(Babel::from("FÃ©dÃ©ration Camerounaise de Football\n")->toUtf8())
                ->toBe("FÃ©dÃ©ration Camerounaise de Football\n");
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->toUtf8())->toBeNull();
            expect(Babel::from(null)->toUtf8())->toBeNull();
        });
    });

    describe('fixUtf8', function (): void {
        test('fixes mojibake UTF-8 input', function (): void {
            expect(Babel::from("FÃ©dÃ©ration Camerounaise de Football\n")->fixUtf8())
                ->toBe("Fédération Camerounaise de Football\n");
        });

        test('returns null for empty input', function (): void {
            expect(Babel::from('')->fixUtf8())->toBeNull();
            expect(Babel::from(null)->fixUtf8())->toBeNull();
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

    describe('toLatin1TransliteratedUtf8', function (): void {
        $reference = static function (?string $value): ?string {
            if (in_array($value, [null, '', '0'], true)) {
                return null;
            }

            $transliterated = transliterator_transliterate('Any-Latin', $value);
            $source = $transliterated !== false ? $transliterated : $value;
            $iso = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $source);

            if ($iso !== false) {
                $utf8 = iconv('ISO-8859-1', 'UTF-8', $iso);

                return $utf8 !== false ? $utf8 : $source;
            }

            return $source;
        };

        test('matches api-1 conversion/null-empty cases', function (?string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with([
            'LG 65   NANO 81 – 4K TV (2024)',
            null,
            '',
        ]);

        test('matches api-1 swedish diacritics cases', function (string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with([
            'Björn Åkesson',
            'Göteborg',
            'Malmö',
            'ÅÄÖ åäö',
            'ÆØÅ æøå',
            'Smörrebröd, Göteborg, Ålesund',
        ]);

        test('matches api-1 json safety cases', function (string $input) use ($reference): void {
            $result = Babel::from($input)->toLatin1TransliteratedUtf8();
            $expected = $reference($input);

            expect($result)->toBe($expected);

            $encoded = json_encode(['name' => $result], \JSON_UNESCAPED_UNICODE);

            expect($encoded)->not->toBeFalse();

            $decoded = json_decode($encoded ?: '', true);

            expect($decoded['name'])->toBe($result);
        })->with([
            'Björn Åkesson',
            'Göteborg',
            'Malmö ä ö å',
            'LG 65 NANO 81 – 4K',
        ]);

        test('matches api-1 strips non-iso characters cases', function (string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with(['Hello 中文', 'Rocket 🚀', 'Plain text']);

        test('matches api-1 non-latin transliteration cases', function (string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with(['أنا شكرا', 'Москва', '東京', 'София, Белград, Київ, Љубљана']);

        test('matches api-1 slavic latin diacritics cases', function (string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with([
            'Łódź, Śląsk, Żółć, ĄĘĆŃÓŚŹŻ',
            'Český Krumlov, Žilina, Řeřicha, Ďábel, Ťap',
            'Đorđe, Čačak, Šibenik, Željko',
        ]);

        test('matches api-1 symbols outside iso cases', function (string $input) use ($reference): void {
            expect(Babel::from($input)->toLatin1TransliteratedUtf8())->toBe($reference($input));
        })->with(['100‰', 'EUR 99€', 'A🙂B']);

        test('matches api-1 iso repertoire and utf8 validity case', function (): void {
            $result = Babel::from('Björn 中文 東京 😀')->toLatin1TransliteratedUtf8();

            expect($result)->not->toBeNull()
                ->and(preg_match('//u', $result))->toBe(1)
                ->and($result)->toBe(iconv('ISO-8859-1', 'UTF-8', iconv('UTF-8', 'ISO-8859-1//IGNORE', $result)));
        });

        test('matches api-1 idempotence cases', function (string $input): void {
            $once = Babel::from($input)->toLatin1TransliteratedUtf8();
            $twice = Babel::from($once)->toLatin1TransliteratedUtf8();

            expect($twice)->toBe($once);
        })->with([
            'Björn Åkesson',
            'ÅÄÖ åäö',
            'Łódź / Göteborg / Москва',
            'Željko Åkesson – №123',
        ]);

        test('matches api-1 control character handling case', function (): void {
            $input = "Åsa\0Öberg\tKatu 1\n00100 Helsinki";
            $result = Babel::from($input)->toLatin1TransliteratedUtf8();

            expect($result)->toBe("Åsa\0Öberg\tKatu 1\n00100 Helsinki")
                ->and(preg_match('//u', $result))->toBe(1)
                ->and($result)->toBe(iconv('ISO-8859-1', 'UTF-8', iconv('UTF-8', 'ISO-8859-1//IGNORE', $result)))
                ->and(json_encode(['value' => $result], \JSON_UNESCAPED_UNICODE))
                ->toBe('{"value":"Åsa\\u0000Öberg\\tKatu 1\\n00100 Helsinki"}');
        });

        test('matches api-1 json round-trip cases', function (string $input) use ($reference): void {
            $normalized = Babel::from($input)->toLatin1TransliteratedUtf8();
            $expected = $reference($input);

            expect($normalized)->toBe($expected);

            $json = json_encode(['value' => $normalized], \JSON_UNESCAPED_UNICODE);

            expect($json)->not->toBeFalse();

            $decoded = json_decode($json ?: '', true);

            expect($decoded['value'])->toBe($expected);
        })->with([
            'Göteborg, Åvägen 5',
            'Łódź, Piotrkowska 10',
            'Москва, Тверская 7',
            "Åsa\0Öberg, Katu 1\n00100 Helsinki",
            'Željko, Ulica Četvrta 12',
        ]);
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
