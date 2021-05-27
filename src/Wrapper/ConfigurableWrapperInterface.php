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
interface ConfigurableWrapperInterface
{
    /**
     * Retrieve the underlaying resource.
     * This method is called in response to stream_select().
     *
     * @see http://php.net/manual/en/streamwrapper.stream-cast.php
     * @param int $castAs Can be {@see \STREAM_CAST_FOR_SELECT} when stream_select()
     *      is calling stream_cast() or {@see \STREAM_CAST_AS_STREAM} when stream_cast()
     *      is called for other uses.
     * @return resource Should return the underlying stream resource used by the
     *      wrapper, or false.
     */
    public function stream_cast(int $castAs);

    /**
     * Change stream options.
     * This method is called to set options on the stream.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-set-option.php
     *
     * @param int $option One of:
     *      - {@see STREAM_OPTION_BLOCKING} - The method was called in response to {@see stream_set_blocking()}
     *      - {@see STREAM_OPTION_READ_TIMEOUT} - The method was called in response to {@see stream_set_timeout()}
     *      - {@see STREAM_OPTION_WRITE_BUFFER} - The method was called in response to {@see stream_set_write_buffer()}.
     * @param int $arg1 If option is
     *      - {@see STREAM_OPTION_BLOCKING} - Requested blocking mode (1 meaning block, 0 not blocking).
     *      - {@see STREAM_OPTION_READ_TIMEOUT} - The timeout in seconds.
     *      - {@see STREAM_OPTION_WRITE_BUFFER} - Buffer mode ({@see STREAM_BUFFER_NONE} or {@see STREAM_BUFFER_FULL}).
     * @param int $arg2 If option is
     *      - {@see \STREAM_OPTION_BLOCKING} - This option is not set.
     *      - {@see \STREAM_OPTION_READ_TIMEOUT} - The timeout in microseconds.
     *      - {@see \STREAM_OPTION_WRITE_BUFFER} - The requested buffer size.
     * @return bool Returns true on success or false on failure.
     *      If option is not implemented, false should be returned.
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool;
}
