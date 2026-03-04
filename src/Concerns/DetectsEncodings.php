<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Babel\Concerns;

use Cline\Babel\Babel;

use function mb_check_encoding;
use function mb_detect_encoding;

/**
 * Provides encoding detection methods for the Babel class.
 *
 * @mixin Babel
 * @author Brian Faust <brian@cline.sh>
 */
trait DetectsEncodings
{
    /**
     * Detect the string's encoding.
     *
     * @example Babel::from('Hello')->detect() // "ASCII"
     * @example Babel::from('Héllo')->detect() // "UTF-8"
     */
    public function detect(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        $encoding = mb_detect_encoding($this->value, null, true);

        return $encoding !== false ? $encoding : null;
    }

    /**
     * Check if string is valid UTF-8.
     *
     * @example Babel::from('Hello')->isUtf8() // true
     */
    public function isUtf8(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return mb_check_encoding($this->value, 'UTF-8');
    }

    /**
     * Check if string contains only ASCII characters.
     *
     * @example Babel::from('Hello')->isAscii() // true
     * @example Babel::from('Héllo')->isAscii() // false
     */
    public function isAscii(): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return mb_check_encoding($this->value, 'ASCII');
    }

    /**
     * Check if string is valid for a specific encoding.
     *
     * @example Babel::from('Hello')->isValidEncoding('UTF-8') // true
     */
    public function isValidEncoding(string $encoding): bool
    {
        if ($this->isEmpty()) {
            return true;
        }

        return mb_check_encoding($this->value, $encoding);
    }
}
