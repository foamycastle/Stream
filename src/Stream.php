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
use Foamycastle\Support\Whence;

abstract class Stream implements StreamInterface
{
    protected int $memLimit;
    protected string $path;
    protected Mode $mode;
    protected StreamState $state=StreamState::UNKNOWN;
    /**
     * The underlying php resource
     * @var $resource resource
     */
    protected $resource;

    protected int $bufferSize=1024;

    /**
     * @inheritDoc
     */
    public function getResource()
    {
        return $this->resource;
    }
    /**
     * Open a stream for operation
     * @param string|resource $path The path on which the stream will operate
     * @param Mode $mode The mode of operation
     * @param array{string,array{string,mixed}} $options
     * @return resource Returns the opened resource
     */
    abstract protected function open($path, Mode $mode, array $options=[]);

    /**
     * Close the underlying stream and restrict further operations
     * @return bool
     */
    abstract protected function close():bool;

    /**
     * Read a string of bytes from a stream
     * @param int $length
     * @param int $offset
     * @return string
     */
    abstract protected function read(int $length, int $offset=0):string;

    /**
     * Write bytes to a stream
     * @param string $data
     * @return bool
     */
    abstract protected function write(string $data):bool;

    /**
     * Move the pointer to a position in the stream
     * @param int $offset
     * @param Whence $whence
     * @return bool
     */
    abstract protected function seek(int $offset, Whence $whence):bool;

    /**
     * Reveal the byte position of the stream's pointer
     * @return int
     */
    abstract protected function tell():int;

    /**
     * Indicates that the end of the stream has been reached
     * @return bool
     */
    abstract protected function eof():bool;

    /**
     * return an array of statistics about the stream
     * @return array{size?:int}
     */
    abstract protected function stat():array;

    /**
     * Sets the stream's read or write buffer. On read-write streams, both buffers are set to `$bufferSize`
     * @param int $bufferSize
     * @return bool
     */
    abstract protected function setBufferSize(int $bufferSize):bool;


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

    function copy()
    {
        // TODO: Implement copy() method.
    }

    function copyFrom($target): Stream
    {
        return $this;
    }
    public function __destruct()
    {
        $this->close();
    }

    public static function Readable(Stream $stream):bool
    {
        return $stream->state==StreamState::READ || $stream->state==StreamState::READWRITE;
    }
    public static function Writable(Stream $stream):bool
    {
        return $stream->state==StreamState::WRITE;
    }

}