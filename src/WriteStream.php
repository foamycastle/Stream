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
    protected function open($path, Mode $mode=Mode::WRITE, array $options = [])
    {
         return
             @fopen($path, Mode::toString($mode), false,$options['context'] ?? null)
          ?: @fopen("php://".self::DEFAULT_PATH, Mode::toString($mode),false,$options['context'] ?? null);
    }

    /**
     * @inheritDoc
     */
    function close(): bool
    {
        $this->state=StreamState::CLOSED;
        return @fclose($this->resource);
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

    /**
     * @inheritDoc
     */
    function write(string $data): bool
    {
        if(!Stream::Writable($this)) return false;
        $len=strlen($data);
        return @fwrite($this->resource,$data,$len)==$len;
    }

    /**
     * @param Whence $whence
     * @inheritDoc
     */
    function seek(int $offset, Whence $whence): bool
    {
        if(!isset($this->memLimit)) $this->memLimit = Support::GetMemLimit();
        if($offset>$this->memLimit) return false;
        return @fseek($this->resource,$offset,$whence->value)==0;
    }

    /**
     * @inheritDoc
     */
    function tell(): int
    {
        return @ftell($this->resource);
    }

    /**
     * @inheritDoc
     */
    function eof(): bool
    {
        return @feof($this->resource);
    }

    protected function stat(): array
    {
        return fstat($this->resource);
    }

    protected function setBufferSize(int $bufferSize): bool
    {
        //continually divide the buffer in half until its size is accepted
        while(stream_set_write_buffer($this->resource, $bufferSize)!==0 && $bufferSize > 0)
        {
            $bufferSize=intval($bufferSize/2);
        }
        if($bufferSize == 0) return false;
        return true;
    }

}