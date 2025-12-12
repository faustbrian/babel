<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

/**
 * Exception thrown when invalid transliteration rules are specified.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidTransliterationRulesException extends TransliterationException
{
    public static function forRules(string $rules): self
    {
        return new self('Invalid transliteration rules: '.$rules);
    }
}
