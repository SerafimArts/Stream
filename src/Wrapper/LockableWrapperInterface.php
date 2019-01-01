<?php
/**
 * This file is part of Reflect\Streaming package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Wrapper;

/**
 * This interface is part of streamWrapper virtual class.
 *
 * @see http://php.net/manual/en/class.streamwrapper.php
 */
interface LockableWrapperInterface
{
    /**
     * Advisory file locking. This method is called in response to flock(),
     * when file_put_contents() (when flags contains LOCK_EX),
     * stream_set_blocking() and when closing the stream (LOCK_UN).
     *
     * @see http://php.net/manual/en/streamwrapper.stream-lock.php
     * @param int $operation Operation is one of the following:
     *      - LOCK_SH: to acquire a shared lock (reader).
     *      - LOCK_EX: to acquire an exclusive lock (writer).
     *      - LOCK_UN: to release a lock (shared or exclusive).
     *      - LOCK_NB: if you don't want flock() to block while locking.
     *          (not supported on Windows)
     * @return bool Returns true on success or false on failure.
     */
    public function stream_lock(int $operation): bool;
}
