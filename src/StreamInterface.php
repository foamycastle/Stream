<?php

namespace Foamycastle\Stream;

interface StreamInterface
{
    public const MODE_WRITE=0;
    public const MODE_READ=1;
    public const MODE_READ_WRITE=2;
    public const FILTER_APPEND=64;
    public const FILTER_PREPEND=128;


    /**
     * @param Stream $stream
     * @param int $offset
     * @param int $length
     * @return int
     */
    //function copyFrom(Stream $stream, int $offset, int $length):int;

    /**
     * Copies the underlying stream to a target stream
     * @param Stream|null $target If provided, the data will be copied to the `Stream` object.  If left null, a new `Stream` object will be created.
     * @param int $mode The mode indicates whether the newly created stream shall be restricted to write-only, read-only, or unrestricted read-write operations.
     * @return StreamInterface return the newly created stream
     */
    //function copyTo(Stream $target=null, int $mode=StreamInterface::MODE_READ):StreamInterface;

    /**
     * Move the stream pointer to a specified position in the stream
     * @param int $offset
     * @return bool
     */
    function seek(int $offset):bool;

    /**
     * Return the current position of the stream's pointer
     * @return int
     */
    function tell():int;

    /**
     * Indicate whether the stream's pointer is at the end of the stream or beyond
     * @return bool
     */
    function eof():bool;
    function isSeekable():bool;
    function isWritable():bool;
    function isReadable():bool;

    /**
     * Return the current size of the underlying
     * @return int
     */
    function size():int;

    /**
     * Open a stream for operation
     * @param string|resource $path
     * @param int|null $mode
     * @return resource
     */
    function open($path, ?int $mode);

    /**
     * Close the underlying stream
     * @return bool
     */
    function close():bool;

    /**
     * The stream's contents are reset and the pointer is set to zero.
     * @return StreamInterface
     */
    function reset(): self;

    /**
     * Returns the entire contents of the stream
     * @return string
     */
    function contents():string;

    /**
     * Attaches a write filter to the stream
     * @param string $name
     * @param class-string $filterClass
     * @return StreamInterface
     */
    function attachFilter(
        string $name,
        string $filterClass
    ):self;

    /**
     * Remove a filter from the current stream
     * @param string $name
     * @return StreamInterface
     */
    function removeFilter(string $name):self;


}