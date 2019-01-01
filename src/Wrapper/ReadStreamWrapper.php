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
use Serafim\Stream\Stream;
use Serafim\Stream\StreamInterface;

/**
 * Class ReadStreamWrapper
 */
class ReadStreamWrapper extends AbstractStreamWrapper
{
    /**
     * @var int
     */
    protected const STAT_MTIME_NUMERIC_OFFSET = 9;

    /**
     * @var string
     */
    protected const STAT_MTIME_ASSOC_OFFSET = 'mtime';

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $pathname;

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * @inheritdoc
     */
    public function stream_open(string $uri, string $mode, int $options, &$openedPath): bool
    {
        [$this->protocol, $this->pathname] = $this->parseUri($uri);

        $this->stream = Stream::get($this->protocol);

        if (\STREAM_USE_PATH & $options && ! \is_file($this->pathname)) {
            $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);

            $this->pathname = \dirname($trace[1]['file']) . '/' . $this->pathname;
        }

        $this->resource = \fopen('php://memory', 'rb+');

        \fwrite($this->resource, $this->stream->read($this->pathname));
        \rewind($this->resource);

        return true;
    }

    /**
     * @param string $uri
     * @return array<string>
     * @throws StreamException
     */
    private function parseUri(string $uri): array
    {
        \preg_match('/^(\w+):\/\/(.*?)$/', $uri, $matches);

        if (\count($matches) !== 3) {
            $error = \sprintf('Bad protocol format "%s"', $uri);
            throw new StreamException($error, StreamException::CODE_STREAM_OPEN);
        }

        return [$matches[1], $matches[2]];
    }

    /**
     * @inheritdoc
     */
    public function stream_read(int $length): string
    {
        return ! \feof($this->resource) ? \fread($this->resource, $length) : '';
    }

    /**
     * @inheritdoc
     */
    public function stream_eof(): bool
    {
        return \feof($this->resource);
    }

    /**
     * @return array
     */
    public function stream_stat(): array
    {
        $stat = \fstat($this->resource);

        if ($stat) {
            ++$stat[static::STAT_MTIME_ASSOC_OFFSET];
            ++$stat[static::STAT_MTIME_NUMERIC_OFFSET];
        }

        return $stat;
    }

    /**
     * @inheritdoc
     */
    public function stream_close(): void
    {
        \fclose($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function stream_tell(): int
    {
        return (int)\ftell($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return \fseek($this->resource, $offset, $whence) !== false;
    }
}
