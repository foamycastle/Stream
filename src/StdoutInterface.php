<?php

namespace Foamycastle\Stream;

interface StdoutInterface
{
    /**
     * Write `data` to Stdout
     * @param string $data
     * @return self
     */
    function put(string $data = ''): self;

    /**
     * Write `data` to Stdout and append a `\n` character
     * @param string $data
     * @return self
     */
    function line(string $data = ''): self;



}