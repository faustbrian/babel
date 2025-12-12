<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Babel\Babel;

describe('Script Detection', function (): void {
    describe('containsAsian', function (): void {
        test('detects Chinese characters', function (): void {
            expect(Babel::from('Hello 世界')->containsAsian())->toBeTrue();
            expect(Babel::from('你好')->containsAsian())->toBeTrue();
        });

        test('detects Japanese characters', function (): void {
            expect(Babel::from('こんにちは')->containsAsian())->toBeTrue();
            expect(Babel::from('カタカナ')->containsAsian())->toBeTrue();
        });

        test('detects Korean characters', function (): void {
            expect(Babel::from('안녕하세요')->containsAsian())->toBeTrue();
        });

        test('returns false for non-Asian text', function (): void {
            expect(Babel::from('Hello World')->containsAsian())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsAsian())->toBeFalse();
            expect(Babel::from(null)->containsAsian())->toBeFalse();
        });
    });

    describe('containsChinese', function (): void {
        test('detects Han characters', function (): void {
            expect(Babel::from('你好')->containsChinese())->toBeTrue();
            expect(Babel::from('Hello 世界')->containsChinese())->toBeTrue();
        });

        test('returns false for non-Chinese', function (): void {
            expect(Babel::from('Hello')->containsChinese())->toBeFalse();
        });
    });

    describe('containsJapanese', function (): void {
        test('detects Hiragana', function (): void {
            expect(Babel::from('こんにちは')->containsJapanese())->toBeTrue();
        });

        test('detects Katakana', function (): void {
            expect(Babel::from('カタカナ')->containsJapanese())->toBeTrue();
        });

        test('returns false for non-Japanese', function (): void {
            expect(Babel::from('Hello')->containsJapanese())->toBeFalse();
        });
    });

    describe('containsKorean', function (): void {
        test('detects Hangul characters', function (): void {
            expect(Babel::from('안녕하세요')->containsKorean())->toBeTrue();
        });

        test('returns false for non-Korean', function (): void {
            expect(Babel::from('Hello')->containsKorean())->toBeFalse();
        });
    });

    describe('containsCyrillic', function (): void {
        test('detects Cyrillic characters', function (): void {
            expect(Babel::from('Привет')->containsCyrillic())->toBeTrue();
            expect(Babel::from('Hello Мир')->containsCyrillic())->toBeTrue();
        });

        test('returns false for non-Cyrillic', function (): void {
            expect(Babel::from('Hello')->containsCyrillic())->toBeFalse();
        });
    });

    describe('containsArabic', function (): void {
        test('detects Arabic characters', function (): void {
            expect(Babel::from('مرحبا')->containsArabic())->toBeTrue();
        });

        test('returns false for non-Arabic', function (): void {
            expect(Babel::from('Hello')->containsArabic())->toBeFalse();
        });
    });

    describe('containsHebrew', function (): void {
        test('detects Hebrew characters', function (): void {
            expect(Babel::from('שלום')->containsHebrew())->toBeTrue();
        });

        test('returns false for non-Hebrew', function (): void {
            expect(Babel::from('Hello')->containsHebrew())->toBeFalse();
        });
    });

    describe('containsGreek', function (): void {
        test('detects Greek characters', function (): void {
            expect(Babel::from('Γειά σου')->containsGreek())->toBeTrue();
        });

        test('returns false for non-Greek', function (): void {
            expect(Babel::from('Hello')->containsGreek())->toBeFalse();
        });
    });

    describe('containsThai', function (): void {
        test('detects Thai characters', function (): void {
            expect(Babel::from('สวัสดี')->containsThai())->toBeTrue();
        });

        test('returns false for non-Thai', function (): void {
            expect(Babel::from('Hello')->containsThai())->toBeFalse();
        });
    });

    describe('containsDevanagari', function (): void {
        test('detects Devanagari characters', function (): void {
            expect(Babel::from('नमस्ते')->containsDevanagari())->toBeTrue();
            expect(Babel::from('Hello नमस्ते')->containsDevanagari())->toBeTrue();
        });

        test('returns false for non-Devanagari', function (): void {
            expect(Babel::from('Hello')->containsDevanagari())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsDevanagari())->toBeFalse();
            expect(Babel::from(null)->containsDevanagari())->toBeFalse();
        });
    });

    describe('containsBengali', function (): void {
        test('detects Bengali characters', function (): void {
            expect(Babel::from('বাংলা')->containsBengali())->toBeTrue();
        });

        test('returns false for non-Bengali', function (): void {
            expect(Babel::from('Hello')->containsBengali())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsBengali())->toBeFalse();
        });
    });

    describe('containsTamil', function (): void {
        test('detects Tamil characters', function (): void {
            expect(Babel::from('தமிழ்')->containsTamil())->toBeTrue();
        });

        test('returns false for non-Tamil', function (): void {
            expect(Babel::from('Hello')->containsTamil())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsTamil())->toBeFalse();
        });
    });

    describe('containsVietnamese', function (): void {
        test('detects Vietnamese characters', function (): void {
            expect(Babel::from('Việt Nam')->containsVietnamese())->toBeTrue();
            expect(Babel::from('Xin chào')->containsVietnamese())->toBeTrue();
        });

        test('returns false for plain Latin', function (): void {
            expect(Babel::from('Hello')->containsVietnamese())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsVietnamese())->toBeFalse();
        });
    });

    describe('containsArmenian', function (): void {
        test('detects Armenian characters', function (): void {
            expect(Babel::from('Հայաdelays')->containsArmenian())->toBeTrue();
        });

        test('returns false for non-Armenian', function (): void {
            expect(Babel::from('Hello')->containsArmenian())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsArmenian())->toBeFalse();
        });
    });

    describe('containsGeorgian', function (): void {
        test('detects Georgian characters', function (): void {
            expect(Babel::from('საქართველო')->containsGeorgian())->toBeTrue();
        });

        test('returns false for non-Georgian', function (): void {
            expect(Babel::from('Hello')->containsGeorgian())->toBeFalse();
        });

        test('returns false for empty input', function (): void {
            expect(Babel::from('')->containsGeorgian())->toBeFalse();
        });
    });

    describe('containsLatin', function (): void {
        test('detects Latin characters', function (): void {
            expect(Babel::from('Hello')->containsLatin())->toBeTrue();
            expect(Babel::from('Café')->containsLatin())->toBeTrue();
        });

        test('returns false for non-Latin only', function (): void {
            expect(Babel::from('123')->containsLatin())->toBeFalse();
            expect(Babel::from('你好')->containsLatin())->toBeFalse();
        });
    });

    describe('containsEmoji', function (): void {
        test('detects emoji', function (): void {
            expect(Babel::from('Hello 👋')->containsEmoji())->toBeTrue();
            expect(Babel::from('🎉 Party')->containsEmoji())->toBeTrue();
        });

        test('returns false for no emoji', function (): void {
            expect(Babel::from('Hello World')->containsEmoji())->toBeFalse();
        });
    });

    describe('containsScript', function (): void {
        test('detects specified script', function (): void {
            expect(Babel::from('Привет')->containsScript('Cyrillic'))->toBeTrue();
            expect(Babel::from('你好')->containsScript('Han'))->toBeTrue();
        });

        test('returns false for missing script', function (): void {
            expect(Babel::from('Hello')->containsScript('Cyrillic'))->toBeFalse();
        });
    });

    describe('isLatin', function (): void {
        test('returns true for Latin-only text', function (): void {
            expect(Babel::from('Hello, World!')->isLatin())->toBeTrue();
            expect(Babel::from('Café 123')->isLatin())->toBeTrue();
        });

        test('returns false for non-Latin text', function (): void {
            expect(Babel::from('Hello 世界')->isLatin())->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isLatin())->toBeTrue();
            expect(Babel::from(null)->isLatin())->toBeTrue();
        });
    });

    describe('isNumeric', function (): void {
        test('returns true for numeric-only', function (): void {
            expect(Babel::from('12345')->isNumeric())->toBeTrue();
        });

        test('returns false for non-numeric', function (): void {
            expect(Babel::from('12.34')->isNumeric())->toBeFalse();
            expect(Babel::from('abc123')->isNumeric())->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isNumeric())->toBeTrue();
            expect(Babel::from(null)->isNumeric())->toBeTrue();
        });
    });

    describe('isAlphanumeric', function (): void {
        test('returns true for alphanumeric-only', function (): void {
            expect(Babel::from('Hello123')->isAlphanumeric())->toBeTrue();
            expect(Babel::from('abc')->isAlphanumeric())->toBeTrue();
        });

        test('returns false for non-alphanumeric', function (): void {
            expect(Babel::from('Hello 123')->isAlphanumeric())->toBeFalse();
            expect(Babel::from('Hello!')->isAlphanumeric())->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isAlphanumeric())->toBeTrue();
            expect(Babel::from(null)->isAlphanumeric())->toBeTrue();
        });
    });

    describe('isScript', function (): void {
        test('returns true for single script', function (): void {
            expect(Babel::from('Привет!')->isScript('Cyrillic'))->toBeTrue();
        });

        test('returns false for mixed scripts', function (): void {
            expect(Babel::from('Hello Привет')->isScript('Cyrillic'))->toBeFalse();
        });

        test('returns true for empty input', function (): void {
            expect(Babel::from('')->isScript('Cyrillic'))->toBeTrue();
            expect(Babel::from(null)->isScript('Cyrillic'))->toBeTrue();
        });
    });
});
