<?php

namespace Foamycastle\Utilities;

abstract class AbstractStream implements Stream
{

    /**
     * The protocol handle
     * @var string
     */
    protected string $wrapperName;

    /**
     * The current read/write byte position of the stream
     * @var int
     */
    protected int $streamPosition=0;

    /**
     * The stream's length in bytes
     * @var int
     */
    protected int $streamLength=0;

    /**
     * Indicates the ability to read from the stream
     * @var bool
     */
    protected bool $readable=false;

    /**
     * Indicates the ability to write to the stream
     * @var bool
     */
    protected bool $writable=false;

    /**
     * Indicates the ability to seek within the stream
     * @var bool
     */
    protected bool $seekable=false;

    /**
     * The PHP auto-populated resource
     * @var resource $context
     */
    public $context;

    /**
     * The temp stream in use
     * @var resource $stream
     */
    protected $stream;

    /**
     * Array resulting from the parse of the url
     * @var array
     */
    protected array $pathVars;

    /**
     * Initialize stream wrapper
     * @return void
     */
    protected function initStream():void{
        $this->readable=true;
        $this->writable=true;
        $this->seekable=true;
        $this->streamPosition=0;
        $this->streamLength=0;
        $this->pathVars=[];
    }

    /**
     * Verify that a path is readable or writable based on the mode provided
     * @param string $path
     * @param string $mode
     * @return bool
     */
    protected function checkPathForMode(string $path,string $mode):bool
    {
        return match ($mode) {
            'r+','r+b', 'w+', 'a+', 'x+', 'c+'      => is_readable($path) & is_writable($path),
            'r', 'rb'                               => is_readable($path),
            'wb','w','a','x','c'                    => is_writable($path),
            default => false
        };
    }

    /**
     * Returns the number of bytes that are able to be read at the current pointer position
     * @param int $count a number of bytes that are desired for a read operation
     * @return int Returns the number of bytes remaining in the stream or the value of $count
     */
    protected function getReadableByteLength(int $count):int
    {
        return min($count,$this->streamLength-$this->streamPosition);
    }

    public function stream_cast(int $cast_as)
    {
        return $this->stream ?? false;
    }

    public function stream_close(): void
    {
        if(isset($this->context) && isset($this->stream)){
            fclose($this->stream);
        }
    }

    public function stream_eof(): bool
    {
        return $this->streamPosition>=$this->streamLength;
    }

    public function stream_flush(): bool
    {
        if(isset($this->context) && isset($this->stream)){
            return fflush($this->stream);
        }
        return false;
    }

    public function stream_lock(int $operation): bool
    {
        if(!isset($this->context)) return false;
        return match ($operation) {
            LOCK_SH => flock($this->stream, LOCK_SH),
            LOCK_EX => flock($this->stream, LOCK_EX),
            LOCK_NB => flock($this->stream, LOCK_NB),
            LOCK_UN => flock($this->stream, LOCK_UN),
            default => false,
        };
    }

    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        return $this->stream_metadata($path, $option, $value);
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        if(isset($this->stream)) return true;
        if($options & STREAM_USE_PATH){
            $includePath=get_include_path();
            if($includePath===false){
                $openPath=$path;
            }else {
                $openPath = $includePath.DIRECTORY_SEPARATOR.$path;
            }
        }else{
            $openPath=$path;
        }
        if(!$this->checkPathForMode($opened_path, $mode)){
            return false;
        }
        $openStream=fopen($openPath, $mode);
        if(is_resource($openStream)){
            $this->stream = $openStream;
            $this->initStream();
            return true;
        }
        return false;
    }

    public function stream_read(int $count): string|false
    {
        if(isset($this->context) && isset($this->stream)){
            return fread($this->stream, $this->getReadableByteLength($count));
        }
        return false;
    }

    public function stream_seek(int $offset, int $whence): bool
    {
        if(isset($this->context) && isset($this->stream)){
            return fseek($this->stream, $offset, $whence);
        }
        return false;
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        return $this->stream_set_option($option, $arg1, $arg2);
    }

    public function stream_stat(): array|false
    {
        if(isset($this->context) && isset($this->stream)){
            return fstat($this->stream);
        }
        return false;
    }

    public function stream_tell(): int
    {
        return $this->streamPosition ?? 0;
    }

    public function stream_truncate(int $size): bool
    {
        if(isset($this->context) && isset($this->stream)){
            return ftruncate($this->stream, $size);
        }
        return false;
    }

    public function stream_write(string $data): int|bool
    {
        if(isset($this->context) && isset($this->stream) && $this->writable){
            $op=fwrite($this->stream, $data);
            if($op===false){
                return false;
            }
            $this->streamLength=+$op;
            $this->streamPosition+=$op;
            return $op;
        }
        return false;
    }


}