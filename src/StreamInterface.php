<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   11:51
*/

namespace Foamycastle;

use Foamycastle\Support\Mode;

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
    function length():int;

    /**
     * Return the entire contents of the stream
     * @return string
     */
    function getContents():string;

    /**
     * Copy the contents of the stream to another
     * @return resource
     */
    function copy();

    /**
     * Copy the contents of a stream into the current instance
     * @param $target
     * @return Stream
     */
    function copyFrom($target):Stream;
}