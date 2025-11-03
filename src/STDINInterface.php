<?php

namespace Foamycastle\Stream;

interface STDINInterface
{
    /**
     * Return an input line from STDIN, terminated by \n
     * @return string
     */
    function getLine():string;

    /**
     * Read a single character from STDIN
     * @return string
     */
    function getChar():string;
}