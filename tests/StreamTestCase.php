<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Tests;

use Serafim\Stream\Exception\NotFoundException;
use Serafim\Stream\Exception\NotReadableException;
use Serafim\Stream\Exception\StreamCreatingException;
use Serafim\Stream\Stream;
use Serafim\Stream\StreamInterface;

/**
 * Class StreamTestCase
 */
class StreamTestCase extends TestCase
{
    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Serafim\Stream\Exception\StreamCreatingException
     */
    public function testStreamCreatable(): void
    {
        $stream = Stream::create(__FUNCTION__);

        $path = __DIR__ . '/mocks/example.txt';
        $this->assertEquals('example', \trim(\file_get_contents($stream->pathname($path))));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Serafim\Stream\Exception\StreamCreatingException
     */
    public function testStreamRewritable(): void
    {
        $stream = Stream::create(__FUNCTION__)
            ->onRead(function (string $source): string {
                return \trim($source) . 42;
            });

        $path = __DIR__ . '/mocks/example.txt';
        $this->assertEquals('example42', \file_get_contents($stream->pathname($path)));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Serafim\Stream\Exception\StreamCreatingException
     */
    public function testStreamRewritableWhileOpening(): void
    {
        $stream = Stream::create(__FUNCTION__)
            ->tryRead(function (string $pathname): string {
                return \trim(\file_get_contents($pathname)) . 42;
            });

        $path = __DIR__ . '/mocks/example.txt';
        $this->assertEquals('example42', \file_get_contents($stream->pathname($path)));
    }

    /**
     * @return void
     * @throws StreamCreatingException
     */
    public function testUnregisteredStream(): void
    {
        $this->expectException(StreamCreatingException::class);

        Stream::get('undefined');
    }

    /**
     * @return void
     * @throws StreamCreatingException
     */
    public function testInvalidFile(): void
    {
        $this->expectException(NotFoundException::class);

        $stream = Stream::create(__FUNCTION__);
        \file_get_contents($stream->pathname('example.test'));
    }

    /**
     * @return void
     * @throws StreamCreatingException
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    public function testNotReadableFile(): void
    {
        if (\stripos(PHP_OS, 'WIN') === 0) {
            $this->markTestSkipped('Not supported under Windows OS');
        }

        $this->expectException(NotReadableException::class);

        $stream = Stream::create(__FUNCTION__);

        $path = __DIR__ .  '/mocks/unreadable.txt';

        \file_put_contents($path, '');
        @\chmod($path, 0000);
        @\chown($path, 'user');

        \file_get_contents($stream->pathname($path));
    }

    /**
     * @return void
     * @throws StreamCreatingException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStreamCanBeUnregistered(): void
    {
        $stream = Stream::create(__FUNCTION__);
        $this->assertTrue($stream->isRegistered());

        Stream::unregister($stream->getName());
        $this->assertFalse($stream->isRegistered());
    }

    /**
     * @return void
     * @throws StreamCreatingException
     */
    public function testBuiltinStreamsCannotBeUnregistered(): void
    {
        $this->expectException(StreamCreatingException::class);

        Stream::unregister('php');
        Stream::create('php');
    }

    /**
     * @return void
     * @throws StreamCreatingException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStreamsNotDuplicated(): void
    {
        $a = Stream::create(__FUNCTION__);
        $b = Stream::create(__FUNCTION__);

        $this->assertSame($a, $b);
    }

    /**
     * @return void
     * @throws StreamCreatingException
     */
    public function testStreamCanNotBeRegisteredTwice(): void
    {
        $this->expectException(StreamCreatingException::class);

        $stream = Stream::new();
        Stream::register($stream->getName(), $stream);
    }

    /**
     * @return void
     * @throws StreamCreatingException
     */
    public function testStreamRelativePathname(): void
    {
        $this->expectException(NotFoundException::class);

        $stream = Stream::create(__FUNCTION__);
        \file_get_contents($stream->pathname('example'), true);
    }

    /**
     * @return void
     * @throws StreamCreatingException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStreamSeekable(): void
    {
        $stream = Stream::create(__FUNCTION__);
        $path = $stream->pathname(__DIR__ . '/mocks/example.txt');

        $fp = \fopen($path, 'rb+');
        \fseek($fp, 1);
        $text = \fread($fp, 1024);
        \fclose($fp);

        $this->assertEquals('xample', \trim($text));
    }
}
