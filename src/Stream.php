<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream;

use Serafim\Stream\Exception\NotFoundException;
use Serafim\Stream\Exception\NotReadableException;
use Serafim\Stream\Exception\StreamCreatingException;
use Serafim\Stream\Wrapper\ReadStreamWrapper;

/**
 * Class Stream
 */
class Stream implements StreamInterface
{
    /**
     * @var string
     */
    private const DEFAULT_STREAM_WRAPPER = ReadStreamWrapper::class;

    /**
     * @var string
     */
    private const STREAM_DUPLICATION_EXCEPTION =
        'Could not create stream "%s", because stream ' .
        'with same name already has been registered.';

    /**
     * @var array<StreamInterface>
     */
    protected static $streams = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array|\Closure[]
     */
    private $readHandlers = [];

    /**
     * @var array|\Closure[]
     */
    private $openHandlers = [];

    /**
     * Stream constructor.
     * @param string $name
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isRegistered(): bool
    {
        return static::exists($this->name);
    }

    /**
     * @param int $complexity
     * @param string $wrapper
     * @return Stream|static
     * @throws StreamCreatingException
     * @throws \Exception
     */
    public static function new(int $complexity = 8, string $wrapper = self::DEFAULT_STREAM_WRAPPER): self
    {
        \assert($complexity > 0, 'Name complexity should be greater than 0');

        $name = 'stream' . \bin2hex(\random_bytes(\random_int(1, $complexity)));

        return static::create($name, $wrapper);
    }

    /**
     * @param string $protocol
     * @param string $wrapper
     * @return Stream|static
     * @throws StreamCreatingException
     */
    public static function create(string $protocol, string $wrapper = self::DEFAULT_STREAM_WRAPPER): self
    {
        if (isset(static::$streams[$protocol])) {
            return static::$streams[$protocol];
        }

        static::register($protocol, $stream = new static($protocol), $wrapper);

        return $stream;
    }

    /**
     * @param string $protocol
     * @param string $wrapper
     * @param StreamInterface $stream
     * @return StreamInterface
     * @throws StreamCreatingException
     */
    public static function register(
        string $protocol,
        StreamInterface $stream,
        string $wrapper = self::DEFAULT_STREAM_WRAPPER
    ): StreamInterface {
        static::$streams[$protocol] = $stream;

        if (static::exists($protocol)) {
            throw new StreamCreatingException(\sprintf(self::STREAM_DUPLICATION_EXCEPTION, $protocol));
        }

        \stream_wrapper_register($protocol, $wrapper);

        return $stream;
    }

    /**
     * @param string $protocol
     * @return bool
     */
    public static function unregister(string $protocol): bool
    {
        if (isset(static::$streams[$protocol])) {
            unset(static::$streams[$protocol]);
            \stream_wrapper_unregister($protocol);

            return true;
        }

        return false;
    }

    /**
     * @param string $protocol
     * @return StreamInterface
     * @throws StreamCreatingException
     */
    public static function get(string $protocol): StreamInterface
    {
        if (! isset(static::$streams[$protocol])) {
            $error = \sprintf('Protocol "%s://" should be registered', $protocol);
            throw new StreamCreatingException($error);
        }

        return static::$streams[$protocol];
    }

    /**
     * @param \Closure $then
     * @return Stream
     */
    public function tryRead(\Closure $then): self
    {
        $this->openHandlers[] = $then;

        return $this;
    }

    /**
     * @param \Closure $then
     * @return Stream|$this
     */
    public function onRead(\Closure $then): self
    {
        $this->readHandlers[] = $then;

        return $this;
    }

    /**
     * @param string $pathname
     * @return string
     */
    public function pathname(string $pathname): string
    {
        return \sprintf('%s://%s', $this->getName(), $pathname);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $pathname
     * @return string
     * @throws NotFoundException
     * @throws NotReadableException
     */
    public function read(string $pathname): string
    {
        return $this->handleRead($this->handleOpen($pathname));
    }

    /**
     * @param string $sources
     * @return string
     */
    private function handleRead(string $sources): string
    {
        foreach ($this->readHandlers as $handler) {
            $sources = $handler($sources);
        }

        return $sources;
    }

    /**
     * @param string $pathname
     * @return string
     * @throws NotFoundException
     * @throws NotReadableException
     */
    private function handleOpen(string $pathname): string
    {
        if (\count($this->openHandlers)) {
            foreach ($this->openHandlers as $handler) {
                if (($result = $handler($pathname)) !== false) {
                    return $result;
                }
            }
        }

        $this->assertIsFile($pathname);
        $this->assertIsReadable($pathname);

        return \file_get_contents($pathname);
    }

    /**
     * @param string $pathname
     * @return void
     * @throws NotFoundException
     */
    private function assertIsFile(string $pathname): void
    {
        if (! \is_file($pathname)) {
            $error = \sprintf('File %s not found', $pathname);
            throw new NotFoundException($error);
        }
    }

    /**
     * @param string $pathname
     * @return void
     * @throws NotReadableException
     */
    private function assertIsReadable(string $pathname): void
    {
        if (! \is_readable($pathname)) {
            $error = \sprintf('File %s not readable', $pathname);
            throw new NotReadableException($error);
        }
    }

    /**
     * @param string $protocol
     * @return bool
     */
    public static function exists(string $protocol): bool
    {
        return \in_array($protocol, \stream_get_wrappers(), true);
    }
}
