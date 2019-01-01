<?php
/**
 * This file is part of Stream package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Wrapper;

/**
 * Allows you to implement your own protocol handlers
 * and streams for use with all the other filesystem
 * functions (such as fopen(), fread() etc.).
 *
 * @see http://php.net/manual/en/class.streamwrapper.php
 */
interface StreamWrapperInterface extends
    ConfigurableWrapperInterface,
    FileSystemWrapperInterface,
    LockableWrapperInterface,
    NetworkWrapperInterface,
    ReadableWrapperInterface,
    WritableWrapperInterface
{
}
