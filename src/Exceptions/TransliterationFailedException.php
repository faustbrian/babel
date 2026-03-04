<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

/**
 * Exception thrown when transliteration fails.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TransliterationFailedException extends TransliterationException
{
    public static function withRules(string $rules): self
    {
        return new self('Transliteration failed with rules: '.$rules);
    }
}
