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
 * @property resource $context
 */
interface ReadableWrapperInterface
{
    /**
     * Opens file or URL.
     * This method is called immediately after the
     * wrapper is initialized (f.e. by fopen() and file_get_contents()).
     *
     * @see http://php.net/manual/en/streamwrapper.stream-open.php
     * @param string $uri Specifies the URL that was passed to the original function.
     * @param string $mode The mode used to open the file, as detailed for fopen().
     * @param int $options Holds additional flags set by the streams API.
     *      It can hold one or more of the following values OR'd together.
     *      - STREAM_USE_PATH: If path is relative, search for
     *          the resource using the include_path.
     *      - STREAM_REPORT_ERRORS: If this flag is set, you are responsible for raising errors
     *          using trigger_error() during opening of the stream.
     *          If this flag is not set, you should not raise any errors.
     * @param string|null &$openedPath If the path is opened successfully,
     *      and STREAM_USE_PATH is set in options, opened_path should be set to the full
     *      path of the file/resource that was actually opened.
     * @return bool Returns true on success or false on failure.
     */
    public function stream_open(string $uri, string $mode, int $options, &$openedPath): bool;

    /**
     * Read from stream.
     * This method is called in response to fread() and fgets().
     * Note: Remember to update the read/write position of the stream
     *       (by the number of bytes that were successfully read).
     *
     * @see http://php.net/manual/en/streamwrapper.stream-read.php
     * @param int $length How many bytes of data from the current position should be returned.
     * @return string If there are less than count bytes available, return as many as are available.
     *      If no more data is available, return either FALSE or an empty string.
     */
    public function stream_read(int $length): string;

    /**
     * Tests for end-of-file on a file pointer.
     * This method is called in response to feof().
     *
     * @see http://php.net/manual/en/streamwrapper.stream-eof.php
     * @return bool Should return true if the read/write position is at the end of the stream
     *      and if no more data is available to be read, or false otherwise.
     */
    public function stream_eof(): bool;

    /**
     * Retrieve information about a file resource.
     *
     * @see http://php.net/manual/en/function.stat.php
     * @return array
     */
    public function stream_stat(): array;

    /**
     * Close a resource. This method is called in response to fclose(). All
     * resources that were locked, or allocated, by the wrapper should be
     * released.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-close.php
     * @return void
     */
    public function stream_close(): void;

    /**
     * Retrieve the current position of a stream.
     * This method is called in response to fseek() to determine the current
     * position.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-tell.php
     * @return int Should return the current position of the stream.
     */
    public function stream_tell(): int;

    /**
     * Seeks to specific location in a stream. This method is called in response
     * to fseek(). The read/write position of the stream should be updated
     * according to the offset and whence.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-seek.php
     * @param int $offset The stream offset to seek to.
     * @param int $whence Possible values:
     *      - SEEK_SET: Set position equal to offset bytes.
     *      - SEEK_CUR: Set position to current location plus offset.
     *      - SEEK_END: Set position to end-of-file plus offset.
     * @return bool Return true if the position was updated, false otherwise.
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool;
}
