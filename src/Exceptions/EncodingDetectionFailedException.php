<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

/**
 * Exception thrown when encoding detection fails.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class EncodingDetectionFailedException extends EncodingException
{
    public static function forString(): self
    {
        return new self('Failed to detect string encoding');
    }
}
