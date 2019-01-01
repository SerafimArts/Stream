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
 * Interface FileSystemWrapperInterface.
 *
 * This interface is part of streamWrapper virtual class.
 *
 * @see http://php.net/manual/en/class.streamwrapper.php
 */
interface FileSystemWrapperInterface
{
    /**
     * Change stream metadata.
     * This method is called to set metadata on the stream.
     * It is called when one of the following functions is called on a stream URL: touch(), chmod(), chown(), chgrp()
     * Note: Some of these operations may not be available on your system.
     *
     * @see http://php.net/manual/en/streamwrapper.stream-metadata.php
     * @param string $path The file path or URL to set metadata.
     *      Note that in the case of a URL, it must be a :// delimited URL. Other URL forms are not supported.
     * @param int $option One of:
     *      - STREAM_META_TOUCH: The method was called in response to touch()
     *      - STREAM_META_OWNER_NAME: The method was called in response to chown() with string parameter
     *      - STREAM_META_OWNER: The method was called in response to chown()
     *      - STREAM_META_GROUP_NAME: The method was called in response to chgrp()
     *      - STREAM_META_GROUP: The method was called in response to chgrp()
     *      - STREAM_META_ACCESS: The method was called in response to chmod()
     * @param int $value If option is
     *      - STREAM_META_TOUCH: Array consisting of two arguments of the touch() function.
     *      - STREAM_META_OWNER_NAME or STREAM_META_GROUP_NAME: The name of the owner user/group as string.
     *      - STREAM_META_OWNER or STREAM_META_GROUP: The value owner user/group argument as integer.
     *      - STREAM_META_ACCESS: The argument of the chmod() as integer.
     * @return bool Returns true on success or false on failure.
     *      If option is not implemented, false should be returned.
     */
    public function stream_metadata(string $path, int $option, int $value): bool;

    /**
     * Delete a file.
     * This method is called in response to unlink().
     *
     * @see http://php.net/manual/en/streamwrapper.unlink.php
     * @param string $path The file URL which should be deleted.
     * @return bool Returns true on success or false on failure.
     */
    public function unlink(string $path): bool;

    /**
     * Renames a file or directory.
     * This method is called in response to rename().
     * Should attempt to rename path "from" to path "to"
     *
     * @see http://php.net/manual/en/streamwrapper.rename.php
     * @param string $from The URL to the current file.
     * @param string $to The URL which the path "from" should be renamed to.
     * @return bool Returns true on success or false on failure.
     */
    public function rename(string $from, string $to): bool;

    /**
     * Create a directory.
     * This method is called in response to mkdir().
     *
     * @see http://php.net/manual/en/streamwrapper.mkdir.php
     * @param string $path Directory which should be created.
     * @param int $mode The value passed to mkdir().
     * @param int $options A bitwise mask of values, such as STREAM_MKDIR_RECURSIVE.
     * @return bool Returns true on success or true on failure.
     */
    public function mkdir(string $path, int $mode, int $options): bool;

    /**
     * Removes a directory.
     * This method is called in response to rmdir().
     *
     * @see http://php.net/manual/en/streamwrapper.rmdir.php
     * @param string $path The directory URL which should be removed.
     * @param int $options A bitwise mask of values, such as STREAM_MKDIR_RECURSIVE.
     * @return bool Returns true on success or true on failure.
     */
    public function rmdir(string $path, int $options): bool;

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * @see http://php.net/manual/en/streamwrapper.dir-opendir.php
     * @param string $path Specifies the URL that was passed to opendir().
     * @param int $options Whether or not to enforce safe_mode (0x04).
     * @return bool Returns true on success or true on failure.
     */
    public function dir_opendir(string $path, int $options): bool;

    /**
     * Read entry from directory handle.
     * This method is called in response to readdir().
     *
     * @see http://php.net/manual/en/streamwrapper.dir-readdir.php
     * @return string|bool Should return string representing the next
     *      filename, or false if there is no next file.
     */
    public function dir_readdir();

    /**
     * Rewind directory handle.
     * This method is called in response to rewinddir().
     * Should reset the output generated by streamWrapper::dir_readdir().
     * i.e.: The next call to streamWrapper::dir_readdir() should return the
     *      first entry in the location returned by streamWrapper::dir_opendir().
     *
     * @see http://php.net/manual/en/streamwrapper.dir-rewinddir.php
     * @return bool Returns true on success or true on failure.
     */
    public function dir_rewinddir(): bool;

    /**
     * Close directory handle.
     * This method is called in response to closedir().
     * Any resources which were locked, or allocated, during
     * opening and use of the directory stream should be released.
     *
     * @see http://php.net/manual/en/streamwrapper.dir-closedir.php
     * @return bool Returns true on success or true on failure.
     */
    public function dir_closedir(): bool;
}