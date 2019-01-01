<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Exception;

/**
 * Class StreamException
 */
class StreamException extends \RuntimeException
{
    /**
     * Code denoting an internal error when calling common method.
     * @var int
     */
    public const CODE_GENERIC = 0x0000;

    /**
     * Code denoting an internal error when calling the stream_cast() method.
     * @var int
     */
    public const CODE_STREAM_CAST = 0x0001;

    /**
     * Code denoting an internal error when calling the stream_set_option() method.
     * @var int
     */
    public const CODE_STREAM_SET_OPTION = 0x0002;

    /**
     * Code denoting an internal error when calling the stream_metadata() method.
     * @var int
     */
    public const CODE_STREAM_METADATA = 0x0003;

    /**
     * Code denoting an internal error when calling the unlink() method.
     * @var int
     */
    public const CODE_UNLINK = 0x0004;

    /**
     * Code denoting an internal error when calling the rename() method.
     * @var int
     */
    public const CODE_RENAME = 0x0005;

    /**
     * Code denoting an internal error when calling the mkdir() method.
     * @var int
     */
    public const CODE_MKDIR = 0x0006;

    /**
     * Code denoting an internal error when calling the rmdir() method.
     * @var int
     */
    public const CODE_RMDIR = 0x0007;

    /**
     * Code denoting an internal error when calling the dir_opendir() method.
     * @var int
     */
    public const CODE_DIR_OPENDIR = 0x0008;

    /**
     * Code denoting an internal error when calling the dir_readdir() method.
     * @var int
     */
    public const CODE_DIR_READDIR = 0x0009;

    /**
     * Code denoting an internal error when calling the dir_rewinddir() method.
     * @var int
     */
    public const CODE_DIR_REWINDDIR = 0x000A;

    /**
     * Code denoting an internal error when calling the dir_closedir() method.
     * @var int
     */
    public const CODE_DIR_CLOSEDIR = 0x000B;

    /**
     * Code denoting an internal error when calling the stream_lock() method.
     * @var int
     */
    public const CODE_STREAM_LOCK = 0x000C;

    /**
     * Code denoting an internal error when calling the url_stat() method.
     * @var int
     */
    public const CODE_URL_STAT = 0x000D;

    /**
     * Code denoting an internal error when calling the stream_open() method.
     * @var int
     */
    public const CODE_STREAM_OPEN = 0x000E;

    /**
     * Code denoting an internal error when calling the stream_read() method.
     * @var int
     */
    public const CODE_STREAM_READ = 0x000F;

    /**
     * Code denoting an internal error when calling the stream_eof() method.
     * @var int
     */
    public const CODE_STREAM_EOF = 0x0010;

    /**
     * Code denoting an internal error when calling the stream_stat() method.
     * @var int
     */
    public const CODE_STREAM_STAT = 0x0011;

    /**
     * Code denoting an internal error when calling the stream_close() method.
     * @var int
     */
    public const CODE_STREAM_CLOSE = 0x0012;

    /**
     * Code denoting an internal error when calling the stream_tell() method.
     * @var int
     */
    public const CODE_STREAM_TELL = 0x0013;

    /**
     * Code denoting an internal error when calling the stream_seek() method.
     * @var int
     */
    public const CODE_STREAM_SEEK = 0x0014;

    /**
     * Code denoting an internal error when calling the stream_flush() method.
     * @var int
     */
    public const CODE_STREAM_FLUSH = 0x0015;

    /**
     * Code denoting an internal error when calling the stream_write() method.
     * @var int
     */
    public const CODE_STREAM_WRITE = 0x0016;

    /**
     * Code denoting an internal error when calling the stream_truncate() method.
     * @var int
     */
    public const CODE_STREAM_TRUNCATE = 0x0017;
}
