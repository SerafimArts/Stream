<?php

/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Stream\Tests;

use Serafim\Stream\Filter\Conjunction;
use Serafim\Stream\Filter\Disjunction;
use Serafim\Stream\Filter\Filter;

class FiltersTestCase extends TestCase
{
    public function testEmptyFilter(): void
    {
        $filter = $this->filter();

        $this->assertTrue($filter->match('A', 'a'));
    }

    private function filter(): Filter
    {
        return new Filter($this->vendor());
    }

    private function vendor(): string
    {
        return \realpath(__DIR__ . '/../vendor');
    }

    public function testFilterIsCallable(): void
    {
        $filter = $this->filter();

        $this->assertIsCallable($filter);
        $this->assertTrue($filter('A', 'a'));
    }

    public function testVendor(): void
    {
        $filter = $this->filter();

        $this->assertFalse($filter->match('A', $this->vendor() . '/some'));

        $filter->withVendors();
        $this->assertTrue($filter->match('A', $this->vendor() . '/some'));

        $filter->exceptVendors();
        $this->assertFalse($filter->match('A', $this->vendor() . '/some'));
    }

    public function testWhere(): void
    {
        $filter = $this->filter();

        $filter->where(function (string $class) {
            return $class === 'A';
        });

        $this->assertTrue($filter->match('A', 'a'));
        $this->assertFalse($filter->match('B', 'a'));
        $this->assertFalse($filter->match('C', 'a'));
    }

    public function testWhereNot(): void
    {
        $filter = $this->filter();

        $filter->not(function (string $class) {
            return $class === 'A';
        });

        $this->assertFalse($filter->match('A', 'a'));
        $this->assertTrue($filter->match('B', 'a'));
        $this->assertTrue($filter->match('C', 'a'));
    }

    public function testEvery(): void
    {
        $filter = $this->filter();

        $filter->every(function (Conjunction $fn): void {
            $fn->className('A');
            $fn->className('B');
        });

        $this->assertFalse($filter->match('A', 'a'));
        $this->assertFalse($filter->match('B', 'b'));
        $this->assertFalse($filter->match('C', 'c'));
    }

    public function testAny(): void
    {
        $filter = $this->filter();

        $filter->any(function (Disjunction $fn): void {
            $fn->className('A');
            $fn->className('B');
        });

        $this->assertTrue($filter->match('A', 'a'));
        $this->assertTrue($filter->match('B', 'b'));
        $this->assertFalse($filter->match('C', 'c'));
    }

    public function testFqn(): void
    {
        $filter = $this->filter()->fqn('A\\B\\C');

        $this->assertTrue($filter->match('A\\B\\C', 'a'));
        $this->assertTrue($filter->match('A\\B\\C\\\\\\\\\\', 'a'));
        $this->assertTrue($filter->match('\\A\\B\\C\\', 'a'));

        $this->assertFalse($filter->match('A', 'a'));
        $this->assertFalse($filter->match('B', 'b'));
        $this->assertFalse($filter->match('C', 'b'));
    }

    public function testNamespace(): void
    {
        $filter = $this->filter()->namespace('A');

        $this->assertTrue($filter->match('A\\B\\C', 'a'));
        $this->assertTrue($filter->match('A\\B\\', 'a'));
        $this->assertTrue($filter->match('A\\B', 'a'));
        $this->assertTrue($filter->match('A\\', 'a'));
        $this->assertTrue($filter->match('A', 'a'));

        $this->assertFalse($filter->match('B', 'b'));
        $this->assertFalse($filter->match('B\\A', 'b'));
        $this->assertFalse($filter->match('C', 'b'));
    }

    public function testFileName(): void
    {
        $filter = $this->filter();
        $filter->fileName('example');

        $this->assertTrue($filter->match('A', 'some/any/example'));
        $this->assertTrue($filter->match('A', 'some/any/example.php'));
        $this->assertTrue($filter->match('A', 'some\\any\\example.php'));
        $this->assertTrue($filter->match('A', 'some://example.ru'));
        $this->assertFalse($filter->match('A', 'example/some'));
    }

    public function testPathNameRegex(): void
    {
        $filter = $this->filter();
        $filter->pathNameMatches('some\d+');

        $this->assertTrue($filter->match('A', 'some42.php'));
        $this->assertTrue($filter->match('A', 'some/some42/file.php'));
        $this->assertFalse($filter->match('A', 'some/42/file.php'));
        $this->assertFalse($filter->match('A', 'some.42'));
    }

    public function testFileNameRegex(): void
    {
        $filter = $this->filter();
        $filter->fileNameMatches('some\d+');

        $this->assertTrue($filter->match('A', 'some42.php'));
        $this->assertFalse($filter->match('A', 'some/some42/file.php'));
        $this->assertFalse($filter->match('A', 'some/42/file.php'));
        $this->assertFalse($filter->match('A', 'some.42'));
    }

    public function testClassNameRegex(): void
    {
        $filter = $this->filter();
        $filter->classNameMatches('Class\d+');

        $this->assertTrue($filter->match('Class42', 'test.php'));
        $this->assertTrue($filter->match('Some\\Any\\Class42', 'test.php'));
        $this->assertFalse($filter->match('SomeClass42', 'test.php'));
        $this->assertFalse($filter->match('42Class', 'test.php'));
    }

    public function testFqnRegex(): void
    {
        $filter = $this->filter();
        $filter->fqnMatches('Class\d+');

        $this->assertTrue($filter->match('Class42', 'test.php'));
        $this->assertTrue($filter->match('Some\\Any\\Class42', 'test.php'));
        $this->assertFalse($filter->match('SomeClass\\42', 'test.php'));
        $this->assertFalse($filter->match('42Class', 'test.php'));
    }
}
