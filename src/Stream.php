<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   11:46
*/

namespace Foamycastle;

use Foamycastle\Support\Mode;
use Foamycastle\Support\StreamState;
use Foamycastle\Support\Support;
use Foamycastle\Support\Whence;

abstract class Stream implements StreamInterface
{
    protected int $memLimit;
    protected string $path;
    protected Mode $mode;
    protected StreamState $state = StreamState::UNKNOWN;
    /**
     * The underlying php resource
     * @var $resource resource
     */
    protected $resource;

    protected int $bufferSize = 1024;

    /**
     * @inheritDoc
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Move the pointer to a position in the stream
     * @param int $offset
     * @param Whence $whence
     * @return bool
     */
    function seek(int $offset, Whence $whence): bool
    {
        if (!isset($this->memLimit)) $this->memLimit = Support::GetMemLimit();
        if ($offset > $this->memLimit) return false;
        return @fseek($this->resource, $offset, $whence->value) == 0;
    }

    /**
     * Close the underlying stream and restrict further operations
     * @return bool
     */
    function close(): bool
    {
        $this->state = StreamState::CLOSED;
        return @fclose($this->resource);
    }

    /**
     * Reveal the byte position of the stream's pointer
     * @return int
     */
    function tell(): int
    {
        return @ftell($this->resource);
    }

    /**
     * Indicates that the end of the stream has been reached
     * @return bool
     */
    function eof(): bool
    {
        return @feof($this->resource);
    }

    /**
     * Read a string of bytes from a stream
     * @param int $length
     * @param int $offset
     * @return string
     */
    protected function read(int $length, int $offset = 0): string
    {
        return "";
    }

    /**
     * Write bytes to a stream
     * @param string $data
     * @return bool
     */
    protected function write(string $data): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    function length(): int
    {
        return $this->stat()['size'];
    }

    /**
     * @inheritDoc
     */
    function getContents(): string
    {
        return stream_get_contents($this->resource) ?: "";
    }

    function putContents(string $contents): bool
    {
        if (
            ($this->mode == Mode::WRITE || $this->mode == Mode::READWRITE) &&
            (!$this->state == StreamState::CLOSED && !$this->state == StreamState::UNKNOWN)
        ) {
            $this->seek(0,SEEK_SET);
            if(!$this->write($contents)) return false;
        }else{
            return false;
        }
        return true;
    }

    function copyTo($target): void
    {
        if (get_resource_type($target) == get_resource_type($this->resource)) {
            stream_copy_to_stream($this->resource, $target);
        }
    }

    public static function Readable(Stream $stream): bool
    {
        return $stream->state == StreamState::READ || $stream->state == StreamState::READWRITE;
    }

    public static function Writable(Stream $stream): bool
    {
        return $stream->state == StreamState::WRITE;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if (StreamManager::hasStream($name)) {
            $maybeReturn = StreamManager::$name(...$arguments);
            if (is_object($maybeReturn)) return $maybeReturn;
        }
        return null;
    }

    /**
     * Open a stream for operation
     * @param string|resource $path The path on which the stream will operate
     * @param Mode $mode The mode of operation
     * @param array{string,array{string,mixed}} $options
     * @return resource Returns the opened resource
     */
    protected function open($path, Mode $mode = Mode::WRITE, array $options = [])
    {
        return
            @fopen($path, Mode::toString($mode), false, $options['context'] ?? null)
                ?: @fopen("php://" . WriteStream::DEFAULT_PATH, Mode::toString($mode), false, $options['context'] ?? null);
    }

    /**
     * return an array of statistics about the stream
     * @return array{size?:int}
     */
    protected function stat(): array
    {
        return fstat($this->resource);
    }

    /**
     * Sets the stream's read or write buffer. On read-write streams, both buffers are set to `$bufferSize`
     * @param int $bufferSize
     * @return bool
     */
    protected function setBufferSize(int $bufferSize): bool
    {
        $this->bufferSize = $bufferSize;
        /**
         * @var $function callable
         */
        $function = ($this->mode == Mode::READ) ? "stream_set_read_buffer" : "stream_set_write_buffer";

        //continually divide the buffer in half until its size is accepted
        while ($function($this->resource, $bufferSize) !== 0 && $bufferSize > 1) {
            $bufferSize = intval($bufferSize / 2);
        }
        if ($bufferSize == 0) return false;
        return true;
    }
}