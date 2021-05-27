<?php

/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Stream\Filter;

use Psr\SimpleCache\CacheInterface;
use Serafim\Stream\Exception\StreamCreatingException;
use Serafim\Stream\Stream;
use Serafim\Stream\StreamInterface;

class Filter extends Conjunction
{
    /**
     * @var array<callable>
     */
    protected array $then = [];

    /**
     * @var StreamInterface
     */
    protected StreamInterface $stream;

    /**
     * @var bool
     */
    protected bool $vendor = false;

    /**
     * @param string $vendorDirectory
     * @throws StreamCreatingException
     */
    public function __construct(string $vendorDirectory)
    {
        $this->stream = Stream::new(32);

        $this->stream->onRead(function (string $sources) {
            foreach ($this->then as $handler) {
                $sources = $handler($sources);
            }

            return $sources;
        });

        $this->where(function (string $class, string $pathname) use ($vendorDirectory): bool {
            if ($this->vendor) {
                return true;
            }

            return \strpos($pathname, $vendorDirectory) !== 0;
        });
    }

    /**
     * @return BaseFilter|$this
     */
    public function exceptVendors(): self
    {
        $this->vendor = false;

        return $this;
    }

    /**
     * @internal Attention! Inclusion of all vendor files may cause undefined behavior.
     * @return BaseFilter|$this
     */
    public function withVendors(): self
    {
        $this->vendor = true;

        return $this;
    }

    /**
     * @param string $pathname
     * @return string
     */
    public function pathname(string $pathname): string
    {
        return $this->stream->pathname($pathname);
    }

    /**
     * @param callable $then
     * @return Filter
     */
    public function then(callable $then): self
    {
        $this->then[] = $then;

        return $this;
    }

    /**
     * @param CacheInterface $cache
     * @param callable $then
     * @return Filter
     */
    public function through(CacheInterface $cache, callable $then): self
    {
        return $this->then(function (string $sources) use ($cache, $then) {
            $key = \md5($sources);

            if (! $cache->has($key)) {
                $cache->set($key, $then($sources));
            }

            return $cache->get($key);
        });
    }
}
