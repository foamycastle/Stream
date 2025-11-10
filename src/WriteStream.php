<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   12:06
*/

namespace Foamycastle;


use Foamycastle\Support\Mode;
use Foamycastle\Support\StreamState;
use Foamycastle\Support\Support;
use Foamycastle\Support\Whence;

class WriteStream extends Stream
{
    /**
     * Required in all descendants of Stream
     */
    public const DEFAULT_PATH='stdout';
    public function __construct($path, string $name)
    {
        $this->mode=Mode::WRITE;
        if(is_resource($path)){
            $this->resource=$path;
            $this->state=StreamState::WRITE;
        }else{
            $openAttempt=$this->open($path, $this->mode);
            if(!is_null($openAttempt)){
                $this->resource=$openAttempt;
                $this->state=StreamState::WRITE;
            }
        }
    }

    /**
     * @inheritDoc
     */
    function write(string $data): bool
    {
        if(!Stream::Writable($this)) return false;
        $len=strlen($data);
        return @fwrite($this->resource,$data,$len)==$len;
    }

}