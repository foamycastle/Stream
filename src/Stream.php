<?php

namespace Foamycastle\Utilities;

interface Stream
{
    /**
     * Retrieve the underlaying resource
     * @param int $cast_as <br>Can be `STREAM_CAST_FOR_SELECT` when `stream_select()` is calling `stream_cast()` or `STREAM_CAST_AS_STREAM` when `stream_cast()` is called for other uses.
     * @return resource|false Should return the underlying stream resource used by the wrapper, or false.
     */
     public function stream_cast(int $cast_as);

    /**
     * Close a resource
     * @return void
     */
     public function stream_close():void;

    /**
     * Tests for end-of-file on a file pointer
     * @return bool Should return true if the read/write position is at the end of the stream and if no more data is available to be read, or false otherwise.
     */
     public function stream_eof(): bool;

    /**
     * Flushes the output. This method is called in response to fflush() and when the stream is being closed while any unflushed data has been written to it before.
     * If you have cached data in your stream but not yet stored it into the underlying storage, you should do so now.
     * @return bool
     */
     public function stream_flush(): bool;

    /**
     * Advisory file locking. This method is called in response to `flock()`, when `file_put_contents()` (when flags contains `LOCK_EX`), `stream_set_blocking()` and when closing the stream (`LOCK_UN`).
     * @param int $operation<br>
     * `LOCK_SH` to acquire a shared lock (reader).<br>
     * `LOCK_EX` to acquire an exclusive lock (writer).<br>
     * `LOCK_UN` to release a lock (shared or exclusive).<br>
     * `LOCK_NB` if you don't want flock() to block while locking. (not supported on Windows)<br>
     * @return bool Returns true on success or false on failure.
     */
     public function stream_lock(int $operation): bool;

    /**
     * Change stream metadata
     * @param string $path The file path or URL to set metadata. Note that in the case of a URL, it must be a :// delimited URL. Other URL forms are not supported.
     * @param int $option<br>
     * `STREAM_META_TOUCH` (The method was called in response to touch())<br>
     * `STREAM_META_OWNER_NAME` (The method was called in response to chown() with string parameter)<br>
     * `STREAM_META_OWNER` (The method was called in response to chown())<br>
     * `STREAM_META_GROUP_NAME` (The method was called in response to chgrp())<br>
     * `STREAM_META_GROUP` (The method was called in response to chgrp())<br>
     * `STREAM_META_ACCESS` (The method was called in response to chmod())<br>
     * @param int $value
     *
     * If option is
     *
     * `STREAM_META_TOUCH`: Array consisting of two arguments of the touch() function.<br>
     * `STREAM_META_OWNER_NAME` or STREAM_META_GROUP_NAME: The name of the owner user/group as string.<br>
     * `STREAM_META_OWNER` or STREAM_META_GROUP: The value owner user/group argument as int.<br>
     * `STREAM_META_ACCESS`: The argument of the chmod() as int.<br>
     * @return bool Returns true on success or false on failure. If option is not implemented, false should be returned.
     */
     public function stream_metadata(string $path, int $option, int $value): bool;

    /**
     * Opens file or URL. This method is called immediately after the wrapper is initialized (f.e. by `fopen()` and `file_get_contents()`).
     * @param string $path Specifies the URL that was passed to the original function. <b>Note:</b>
     * The URL can be broken apart with parse_url(). Note that only URLs delimited by :// are supported. : and :/ while technically valid URLs, are not.
     * @param string $mode The mode used to open the file, as detailed for `fopen()`.
     * @param int $options Holds additional flags set by the streams API. It can hold one or more of the following values OR'd together.<br>
     * `STREAM_USE_PATH`    If path is relative, search for the resource using the include_path.
     * `STREAM_REPORT_ERRORS`    If this flag is set, you are responsible for raising errors using `trigger_error()` during opening of the stream. If this flag is not set, you should not raise any errors.
     * @param string|null $opened_path If the path is opened successfully, and STREAM_USE_PATH is set in options, opened_path should be set to the full path of the file/resource that was actually opened.
     * @return bool Returns true on success or false on failure.
     */
     public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool;

    /**
     * Read from stream. This method is called in response to `fread()` and `fgets()`. <b>Note:</b> Remember to update the read/write position of the stream (by the number of bytes that were successfully read).
     * @param int $count How many bytes of data from the current position should be returned.
     * @return string|false If there are less than count bytes available, as many as are available should be returned. If no more data is available, an empty string should be returned. To signal that reading failed, false should be returned.
     */
     public function stream_read(int $count):string|false;

    /**
     * Seeks to specific location in a stream.This method is called in response to `fseek()`.
     * The read/write position of the stream should be updated according to the offset and whence.
     * @param int $offset The stream offset to seek to.
     * @param int $whence Possible values:<br>
     *
     * `SEEK_SET` - Set position equal to offset bytes. <br>
     * `SEEK_CUR` - Set position to current location plus offset.<br>
     * `SEEK_END` - Set position to end-of-file plus offset.<br>
     * <b>Note:</b> The current implementation never sets whence to SEEK_CUR; instead such seeks are internally converted to SEEK_SET seeks.
     * @return bool Return true if the position was updated, false otherwise.
     */
     public function stream_seek(int $offset, int $whence): bool;

    /**
     * Change stream options. This method is called to set options on the stream.
     * @param int $option One of:<br>
     *
     * `STREAM_OPTION_BLOCKING` (The method was called in response to `stream_set_blocking()`)<br>
     * `STREAM_OPTION_READ_TIMEOUT` (The method was called in response to `stream_set_timeout()`)<br>
     * `STREAM_OPTION_READ_BUFFER` (The method was called in response to `stream_set_read_buffer()`)<br>
     * `STREAM_OPTION_WRITE_BUFFER` (The method was called in response to `stream_set_write_buffer()`)<br>
     * @param int $arg1 If option is<br>
     *
     * `STREAM_OPTION_BLOCKING`: requested blocking mode (1 meaning block 0 not blocking).<br>
     * `STREAM_OPTION_READ_TIMEOUT`: the timeout in seconds.<br>
     * `STREAM_OPTION_READ_BUFFER`: buffer mode (`STREAM_BUFFER_NONE` or `STREAM_BUFFER_FULL`).<br>
     * `STREAM_OPTION_WRITE_BUFFER`: buffer mode (`STREAM_BUFFER_NONE` or `STREAM_BUFFER_FULL`).<br>
     * @param int $arg2 If option is<br>
     *
     * `STREAM_OPTION_BLOCKING`: This option is not set.<br>
     * `STREAM_OPTION_READ_TIMEOUT`: the timeout in microseconds.<br>
     * `STREAM_OPTION_READ_BUFFER`: the requested buffer size.<br>
     * `STREAM_OPTION_WRITE_BUFFER`: the requested buffer size.<br>
     * @return bool
     */
     public function stream_set_option(int $option, int $arg1, int $arg2): bool;

    /**
     * Retrieve information about a file resource. This method is called in response to `fstat()`.
     * @return array|false
     */
     public function stream_stat():array|false;

    /**
     * Retrieve the current position of a stream. This method is called in response to `fseek()` to determine the current position.
     * @return int Should return the current position of the stream.
     */
     public function stream_tell():int;

    /**
     * Truncate stream. Will respond to truncation, e.g., through ftruncate().
     * @param int $size The new size.
     * @return bool Returns true on success or false on failure.
     */
     public function stream_truncate(int $size): bool;

    /**
     * Write to stream.  This method is called in response to `fwrite()`. <b>Note:</b>
     * Remember to update the current position of the stream by number of bytes that were successfully written.
     * @param string $data Should be stored into the underlying stream. If there is not enough room in the underlying stream, store as much as possible.
     * @return int|bool Should return the number of bytes that were successfully stored, or 0 if none could be stored.
     */
     public function stream_write(string $data):int|bool;

}