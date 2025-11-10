<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/10/25
 *  Time:   10:42
*/


namespace Foamycastle;

use Foamycastle\Support\Mode;
use Foamycastle\Support\StreamState;
use Foamycastle\Support\Whence;

class ReadStream extends Stream
{
    public function __construct($path, string $name)
    {
        $this->mode = Mode::READ;
        if(is_resource($path)){
            $this->resource=$path;
            $this->state=StreamState::WRITE;
        }else{
            $openAttempt=$this->open($path, $this->mode);
            if(!is_null($openAttempt)){
                $this->resource=$openAttempt;
                $this->state=StreamState::READ;
            }
        }
    }

    /**
     * @inheritDoc
     */
    function read(int $length=-1, int $offset = 0): string
    {
        if(!Stream::Readable($this)) return '';
        if($length==-1) $length = $this->length();
        $this->seek($offset, Whence::SET);
        return @fread($this->resource, $length) ?: "";
    }

    function copyFrom($target): Stream
    {
        // TODO: Implement copyFrom() method.
    }
}