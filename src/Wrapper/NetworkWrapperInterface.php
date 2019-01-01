<?php
/**
 * This file is part of Reflect\Streaming package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Stream\Wrapper;

/**
 * This interface is part of streamWrapper virtual class.
 *
 * @see http://php.net/manual/en/class.streamwrapper.php
 */
interface NetworkWrapperInterface
{
    /**
     * Retrieve information about a file.
     * This method is called in response to all stat() related functions.
     *
     * @see http://php.net/manual/en/streamwrapper.url-stat.php
     * @param string $path The file path or URL to stat. Note that in the case of a URL,
     *      it must be a :// delimited URL. Other URL forms are not supported.
     * @param int $flags Holds additional flags set by the streams API.
     *      It can hold one or more of the following values OR'd together.
     *      - STREAM_URL_STAT_LINK: For resources with the ability to link
     *          to other resource (such as an HTTP Location: forward, or a filesystem symlink).
     *          This flag specified that only information about the link itself
     *          should be returned, not the resource pointed to by the link.
     *          This flag is set in response to calls to lstat(), is_link(), or filetype().
     *      - STREAM_URL_STAT_QUIET: If this flag is set, your wrapper should not raise any errors.
     *          If this flag is not set, you are responsible for reporting errors using the
     *          trigger_error() function during stating of the path.
     * @return array Should return as many elements as stat() does.
     *      Unknown or unavailable values should be set to a rational value (usually 0).
     */
    public function url_stat(string $path, int $flags): array;
}
