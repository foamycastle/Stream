<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/10/25
 *  Time:   12:25
*/


namespace Foamycastle;


use Foamycastle\Support\Mode;
use Foamycastle\Support\StreamState;

class ReadStream extends Stream
{
    /**
     * Required in all descendants of Stream
     */
    public const DEFAULT_PATH='stdin';
    public function __construct($path, string $name)
    {
        $this->mode=Mode::READ;
        if(is_resource($path)){
            $this->resource=$path;
            $this->state=StreamState::READ;
            $this->path=$path;
        }else{
            $openAttempt=$this->open($path, $this->mode);
            if(!is_null($openAttempt)){
                $this->resource=$openAttempt;
                $this->state=StreamState::READ;
                $this->path=$path;
            }
        }
        StreamManager::CreateStream($name,$this->resource);
    }

}