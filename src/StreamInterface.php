<?php

/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Stream;

interface StreamInterface
{
    /**
     * @param string $pathname
     * @return string
     */
    public function read(string $pathname): string;
}
