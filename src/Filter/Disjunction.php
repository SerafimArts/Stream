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
 * Class Disjunction
 */
class Disjunction extends BaseFilter
{
    /**
     * @param string $path
     * @param string $class
     * @return bool
     */
    public function match(string $path, string $class): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter($path, $class)) {
                return true;
            }
        }

        return false;
    }
}
