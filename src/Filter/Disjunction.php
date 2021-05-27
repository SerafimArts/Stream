<?php

/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Stream\Filter;

class Disjunction extends BaseFilter
{
    /**
     * @param string $class
     * @param string $pathname
     * @return bool
     */
    public function match(string $class, string $pathname): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter($class, $pathname)) {
                return true;
            }
        }

        return false;
    }
}
