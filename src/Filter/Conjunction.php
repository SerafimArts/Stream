<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Filter;

/**
 * Class Conjunction
 */
class Conjunction extends BaseFilter
{
    /**
     * @param string $path
     * @param string $class
     * @return bool
     */
    public function match(string $class, string $path): bool
    {
        foreach ($this->filters as $filter) {
            if (! $filter($class, $path)) {
                return false;
            }
        }

        return true;
    }
}
