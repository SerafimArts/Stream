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
     * Default streaming class. Must implement StreamWrapperInterface.
     *
     * @var string
     */
    private const DEFAULT_STREAM_WRAPPER = ReadStreamWrapper::class;

    /**
     * Error message in the case of stream name conflicts.
     *
     * @var string
     */
    private const STREAM_DUPLICATION_EXCEPTION =
        'Could not create stream "%s", because stream ' .
        'with same name already has been registered.';

    /**
     * List of registered stream handlers.
     *
     * @var array<StreamInterface>
     */
    protected static $streams = [];

    /**
     * Current stream name.
     *
     * @var string
     */
    private $name;

    /**
     * List of handlers processing the source code of the stream.
     *
     * @var array|\Closure[]
     */
    private $readHandlers = [];

    /**
     * List of handlers processing file read attempts.
     *
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
     * Returns a positive result if the handler is valid and allows processing
     * of the result.
     *
     * In order to remove the handler's registration, you need to call
     * <code>
     * Stream::unregister($stream->getName());
     * </code>
     *
     * @return bool
     */
    public function isRegistered(): bool
    {
        return static::exists($this->name);
    }

    /**
     * Creates a new arbitrary stream handler with a randomly generated name.
     *
     * @param int $complexity Suffix length for the generated stream handler name.
     * @param string $wrapper A wrapper class where the read/write stream will be redirected.
     * @return Stream|static
     * @throws StreamCreatingException
     * @throws \Exception
     */
    public static function new(int $complexity = 8, string $wrapper = self::DEFAULT_STREAM_WRAPPER): self
    {
        \assert($complexity > 0, 'Name complexity should be greater than 0');

        $name = 'stream' . \bin2hex(\random_bytes($complexity));

        return static::create($name, $wrapper);
    }

    /**
     * Creating and registering a new stream with the specified name.
     *
     * If the existing handler is a system handler (e.g. "php", "memory",
     * "phar", "http", etc.), an exception will be thrown. In the event that
     * the non-system handler already exists, the existing one will be returned.
     *
     * @param string $protocol The name of the protocol. May contain
     *      alphanumeric sequences, but must begin with a letter.
     * @param string $wrapper A wrapper class where the read/write stream
     *      will be redirected.
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
     * Registration of stream protocol handler.
     *
     * @param string $protocol The name of the protocol. May contain
     *      alphanumeric sequences, but must begin with a letter.
     * @param string $wrapper A wrapper class where the read/write stream
     *      will be redirected.
     * @param StreamInterface $stream Instance of StreamInterface where
     *      read/write stream will be redirected.
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
     * Cancels custom handler registration. Ignores built-in handlers, or
     * handlers that have been registered with other registrars
     * (other than this class).
     *
     * @param string $protocol The name of the protocol. May contain
     *      alphanumeric sequences, but must begin with a letter.
     * @return bool Returns true in the event that the deletion of registration
     *      was made successfully and false otherwise.
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
     * Returns a handler by protocol name.
     *
     * @param string $protocol The name of the protocol. May contain
     *      alphanumeric sequences, but must begin with a letter.
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
     * Adds a read attempt handler. If no such handler has been registered for
     * this stream handler, it will be opened by calling the file_get_contents
     * function.
     *
     * <code>
     *  $stream->tryRead(function (string $pathname): ?string {
     *      return \is_file($pathname) ? \file_get_contents($pathname) : null;
     *  });
     * </code>
     *
     * @param \Closure $then
     * @return Stream
     */
    public function tryRead(\Closure $then): self
    {
        $this->openHandlers[] = $then;

        return $this;
    }

    /**
     * Adds a source code handler. Each closure passed to this method takes
     * the source text as a string and must return a line with the new text.
     *
     * <code>
     *  $stream->onRead(function (string $sources): string {
     *      return $sources;
     *  });
     * </code>
     *
     * @param \Closure $then
     * @return Stream|$this
     */
    public function onRead(\Closure $then): self
    {
        $this->readHandlers[] = $then;

        return $this;
    }

    /**
     * Returns the full path with the protocol for the passed name.
     *
     * @param string $pathname The path to the file string.
     * @return string
     */
    public function pathname(string $pathname): string
    {
        return \sprintf('%s://%s', $this->getName(), $pathname);
    }

    /**
     * Returns the name of the current stream.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Reads a file using the actual file path, using all the registered
     * read and processing handlers.
     *
     * <code>
     *  echo Stream::get('protocol')->read('path/to/file.txt');
     * </code>
     *
     * @param string $pathname The path to the file string.
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
                if (\is_string($result = $handler($pathname))) {
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
     * Returns whether the transferred protocol is registered.
     *
     * @param string $protocol
     * @return bool
     */
    public static function exists(string $protocol): bool
    {
        return \in_array($protocol, \stream_get_wrappers(), true);
    }
}
