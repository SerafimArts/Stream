<?php

/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Stream\Filter;

abstract class BaseFilter implements FilterInterface
{
    /**
     * @var array<callable>
     */
    protected array $filters = [];

    /**
     * @param string $class
     * @param string $file
     * @return bool
     */
    public function __invoke(string $class, string $file): bool
    {
        return $this->match($class, $file);
    }

    /**
     * @param callable $filter
     * @return $this
     */
    public function not(callable $filter): self
    {
        return $this->where(function (string $class, string $file) use ($filter): bool {
            return ! $filter($class, $file);
        });
    }

    /**
     * @param callable $filter
     * @return $this
     */
    public function where(callable $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param \Closure $then
     * @return BaseFilter|$this
     */
    public function every(\Closure $then): self
    {
        return $this->where(function (string $class, string $file) use ($then): bool {
            $then($conjunction = new Conjunction());

            return $conjunction->match($class, $file);
        });
    }

    /**
     * @param \Closure $then
     * @return $this
     */
    public function any(\Closure $then): self
    {
        return $this->where(function (string $class, string $file) use ($then): bool {
            $then($disjunction = new Disjunction());

            return $disjunction->match($class, $file);
        });
    }

    /**
     * @param string $fqn
     * @return $this
     */
    public function fqn(string $fqn): self
    {
        return $this->where(function (string $class) use ($fqn): bool {
            return \trim($class, '\\') === \trim($fqn, '\\');
        });
    }

    /**
     * @param string $name
     * @return $this
     */
    public function className(string $name): self
    {
        $name = \trim($name, '\\');

        return $this->where(function (string $fqn) use ($name): bool {
            return \substr($fqn, -\strlen($name)) === $name;
        });
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function namespace(string $prefix): self
    {
        $prefix = \trim($prefix, '\\');

        return $this->where(function (string $class) use ($prefix): bool {
            return \strpos(\trim($class, '\\'), $prefix) === 0;
        });
    }

    /**
     * @param string $name
     * @return $this
     */
    public function fileName(string $name): self
    {
        return $this->where(function (string $_, string $file) use ($name): bool {
            $file = \str_replace('\\', '/', $file);

            return \pathinfo($file, \PATHINFO_FILENAME) === $name;
        });
    }

    /**
     * @param string $regex
     * @return $this
     */
    public function pathNameMatches(string $regex): self
    {
        $regex = $this->regex($regex, false);

        return $this->where(function (string $_, string $file) use ($regex): bool {
            return \preg_match($regex, $file) !== 0;
        });
    }

    /**
     * @param string $regex
     * @return $this
     */
    public function fileNameMatches(string $regex): self
    {
        $regex = $this->regex($regex);

        return $this->where(function (string $_, string $file) use ($regex): bool {
            return \preg_match($regex, \pathinfo($file, \PATHINFO_FILENAME)) !== 0;
        });
    }

    /**
     * @param string $regex
     * @return $this
     */
    public function classNameMatches(string $regex): self
    {
        $regex = $this->regex($regex, true);

        return $this->where(function (string $fqn) use ($regex): bool {
            $class = \basename(\str_replace('\\', '/', $fqn));

            return \preg_match($regex, $class) !== 0;
        });
    }

    /**
     * @param string $regex
     * @return $this
     */
    public function fqnMatches(string $regex): self
    {
        $regex = $this->regex($regex);

        return $this->where(function (string $fqn) use ($regex): bool {
            return \preg_match($regex, $fqn) !== 0;
        });
    }

    /**
     * @param string $regex
     * @param bool $strict
     * @return string
     */
    private function regex(string $regex, bool $strict = false): string
    {
        $regex = $strict ? '^' . $regex . '$' : $regex;

        return \sprintf('/%s/isuS', \addcslashes($regex, '/'));
    }
}
