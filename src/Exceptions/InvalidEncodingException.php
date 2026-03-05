<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

/**
 * Exception thrown when an invalid encoding is specified.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidEncodingException extends EncodingException
{
    /**
     * Create an exception for an invalid encoding name.
     *
     * @param string $encoding The invalid encoding value.
     */
    public static function forName(string $encoding): self
    {
        return new self('Invalid encoding: '.$encoding);
    }
}
