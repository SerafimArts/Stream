<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream;

use Composer\Autoload\ClassLoader as Composer;
use Serafim\Stream\Exception\StreamCreatingException;
use Serafim\Stream\Filter\Filter;

/**
 * Class ClassLoader
 * @property Filter $when
 */
class ClassLoader
{
    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var Filter[]
     */
    private $filters = [];

    /**
     * @var string
     */
    private $vendorDir;

    /**
     * ClassLoader constructor.
     * @param Composer $composer
     * @param bool $register
     */
    public function __construct(Composer $composer, bool $register = true)
    {
        $this->composer = $composer;
        $this->vendorDir = $this->getVendorDir($composer);

        if ($register) {
            $this->register();
        }
    }

    /**
     * @param Composer $composer
     * @return string
     */
    private function getVendorDir(Composer $composer): string
    {
        $dirname = \dirname((new \ReflectionObject($composer))->getFileName(), 2);

        return \str_replace('\\', '/', $dirname);
    }

    /**
     * @return $this|ClassLoader
     */
    public function register(): self
    {
        \spl_autoload_register([$this, 'loadClass'], true, true);

        return $this;
    }

    /**
     * @return $this|ClassLoader
     */
    public function unregister(): self
    {
        \spl_autoload_unregister([$this, 'loadClass']);

        return $this;
    }

    /**
     * @return Filter
     * @throws StreamCreatingException
     */
    public function when(): Filter
    {
        return $this->filters[] = new Filter($this->vendorDir);
    }

    /**
     * @param string $class
     * @return bool
     * @throws \Throwable
     */
    public function loadClass(string $class): bool
    {
        if ($this->isSameNamespace($class)) {
            return false;
        }

        if (! \is_string($file = $this->composer->findFile($class))) {
            return false;
        }

        $file = \str_replace('\\', '/', \realpath($file));

        foreach ($this->filters as $filter) {
            if ($filter->match($class, $file)) {
                /** @noinspection PhpIncludeInspection */
                require $filter->pathname($file);

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function isSameNamespace(string $class): bool
    {
        return \strpos($class, __NAMESPACE__) === 0;
    }

    /**
     * @param string $name
     * @return Filter|null
     * @throws StreamCreatingException
     */
    public function __get(string $name)
    {
        if ($name === 'when') {
            return $this->when();
        }

        return null;
    }
}
