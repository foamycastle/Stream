<?php

namespace Foamycastle\Stream;

class Stdout extends WriteOnlyStream implements StdoutInterface
{
    public function __construct(string $name = "")
    {
        parent::__construct(STDOUT, $name);
    }

    function put(string $data=''): StdoutInterface
    {
        $this->write($data);
        return $this;
    }

    function line(string $data=''): StdoutInterface
    {
        $this->write($data."\n");
        return $this;
    }




}