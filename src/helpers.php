<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

use Serafim\Stream\Stream;
use Serafim\Stream\StreamInterface;

if (! \function_exists('\\restream')) {
    /**
     * @param string $protocol
     * @param Closure $then
     * @return StreamInterface
     * @throws \Serafim\Stream\Exception\StreamCreatingException
     */
    function restream(string $protocol, \Closure $then): StreamInterface
    {
        return Stream::create($protocol)->tryRead($then);
    }
}
