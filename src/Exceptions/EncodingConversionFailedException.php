<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

use function sprintf;

/**
 * Exception thrown when encoding conversion fails.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class EncodingConversionFailedException extends EncodingException
{
    /**
     * Create an exception for a failed conversion between two encodings.
     *
     * @param string $from The source encoding.
     * @param string $to   The target encoding.
     */
    public static function fromEncodings(string $from, string $to): self
    {
        return new self(sprintf('Failed to convert from %s to %s', $from, $to));
    }
}
