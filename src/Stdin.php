<?php

namespace Foamycastle\Stream;

use Foamycastle\Stream\Stream;

class Stdin extends ReadOnlyStream implements STDINInterface
{
    public function __construct(string $name="")
    {
        parent::__construct(STDIN, $name);
    }

    function getLine(): string
    {
        return fgets($this->stream);
    }

    function getChar(): string
    {
        return fgetc($this->stream);
    }
}