<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   11:51
*/

namespace Foamycastle;

use Foamycastle\Support\Mode;
use Foamycastle\Support\StreamState;

interface StreamInterface
{
    /**
     * exposes the underlying stream;
     * @return resource
     */
    function getResource();

    /**
     * Return the length of the streams buffer
     * @return mixed
     */
    function length(): int;

    /**
     * Return the entire contents of the stream
     * @return string
     */
    function getContents(): string;

    /**
     * Put the entire contents of `$string` in the stream
     * @param string $contents
     * @return bool
     */
    function putContents(string $contents): bool;

    /**
     * Copy the contents of the stream to another
     * @param resource $target
     * @return void
     */
    function copyTo($target): void;

    /**
     * Copy the contents of a stream into the current instance
     * @param $target
     * @return Stream
     */
    function copyFrom($target, string $newName): Stream;

    /**
     * Allow external entities to read the stream's state
     * @return StreamState
     */
    function getState(): StreamState;

}