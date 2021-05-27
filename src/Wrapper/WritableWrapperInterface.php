<?php

/**
 * This file is part of Stream package.
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
interface WritableWrapperInterface extends ReadableWrapperInterface
{
    /**
     * Write to stream.
     * This method is called in response to fwrite().
     * Note: Remember to update the current position of the stream by
     *      number of bytes that were successfully written.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-write.php
     * @param string $data Should be stored into the underlying stream.
     *      Note: If there is not enough room in the underlying stream, store as
     *      much as possible.
     * @return int Should return the number of bytes that were successfully
     *      stored, or 0 if none could be stored.
     */
    public function stream_write(string $data): int;

    /**
     * Truncate stream.
     * Will respond to truncation, e.g., through ftruncate().
     *
     * @see http://php.net/manual/en/streamwrapper.stream-truncate.php
     * @param int $size The new size.
     * @return bool Returns true on success or false on failure.
     */
    public function stream_truncate(int $size): bool;

    /**
     * Flushes the output.
     * This method is called in response to fflush() and when the stream is
     * being closed while any unflushed data has been written to it before.
     * If you have cached data in your stream but not yet stored
     * it into the underlying storage, you should do so now.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-flush.php
     * @return bool Should return true if the cached data was successfully
     *      stored (or if there was no data to store), or false if the data could not
     *      be stored.
     */
    public function stream_flush(): bool;
}
