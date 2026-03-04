<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Exceptions;

use RuntimeException;

/**
 * Base exception for all encoding-related errors.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class EncodingException extends RuntimeException implements BabelException
{
    // Abstract base - no factory methods
}
