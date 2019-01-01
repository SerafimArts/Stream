<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Wrapper;

use Serafim\Stream\Exception\StreamException;

/**
 * Class AbstractStreamWrapper
 */
abstract class AbstractStreamWrapper implements StreamWrapperInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_cast(int $castAs): void
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_CAST);
    }

    /**
     * @param string $function
     * @param int $code
     * @return StreamException
     */
    protected function notAcceptable(string $function, int $code = 0): StreamException
    {
        $error = \sprintf('%s->%s is not acceptable', static::class, $function);

        return new StreamException($error, $code);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_SET_OPTION);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_metadata(string $path, int $option, int $value): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_METADATA);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function unlink(string $path): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_UNLINK);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function rename(string $from, string $to): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_RENAME);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_MKDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function rmdir(string $path, int $options): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_RMDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function dir_opendir(string $path, int $options): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_DIR_OPENDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function dir_readdir(): void
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_DIR_READDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function dir_rewinddir(): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_DIR_REWINDDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function dir_closedir(): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_DIR_CLOSEDIR);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_lock(int $operation): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_LOCK);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function url_stat(string $path, int $flags): array
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_URL_STAT);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_open(string $uri, string $mode, int $options, &$openedPath): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_OPEN);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_read(int $length): string
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_READ);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_eof(): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_EOF);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_stat(): array
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_URL_STAT);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_close(): void
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_CLOSE);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_tell(): int
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_TELL);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_SEEK);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_flush(): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_FLUSH);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_write(string $data): int
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_WRITE);
    }

    /**
     * @inheritdoc
     * @throws StreamException
     */
    public function stream_truncate(int $size): bool
    {
        throw $this->notAcceptable(__FUNCTION__, StreamException::CODE_STREAM_TRUNCATE);
    }
}
