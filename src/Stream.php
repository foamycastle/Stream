<?php

namespace Foamycastle\Stream;
use Error;
use Exception;
use InvalidArgumentException;
use php_user_filter;
use RuntimeException;

abstract class Stream implements StreamInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var resource
     */
    protected $stream;

    /**
     * When TRUE, seeking is allowed beyond the length of the stream
     * @var bool
     */
    protected bool $seekBeyondLength = true;

    /**
     * The path used to open a stream
     * @var string|resource
     */
    protected $path;

    /**
     * @var int<StreamInterface::MODE_READ|StreamInterface::MODE_|StreamInterface::MODE_READ>
     */
    protected int $mode;

    /**
     * An array of filter attached to  streams
     * @var array<string,resource>
     */
    protected array $filters;

    /**
     * An array of filters attached to read streams
     * @var array<string,resource>
     */
    protected array $readFilters;


    function size(): int
    {
        return fstat($this->stream)['size'];
    }

    protected function read(int $length = 0): string
    {
        if($length<=0) return '';
        return fread($this->stream,$length);
    }

    function seek(int $offset): bool
    {
        if (!$this->seekBeyondLength) {
            if ($this->tell() > $this->size()) {
                return false;
            }
        }
        if(!$this->isSeekable()) return false;
        return fseek($this->stream, $offset) == 0;
    }

    function close(): bool
    {
        return fclose($this->stream);
    }

    function tell(): int
    {
        return ftell($this->stream);
    }

    function open($path, ?int $mode)
    {
        if(null === ($mode ?? $this->mode ?? null)){
            throw new Exception("Invalid mode provided.  Mode is not set.");
        }
        $mode=match($mode ?? $this->mode) {
            StreamInterface::MODE_READ=>'r',
            StreamInterface::MODE_WRITE=>'w',
            StreamInterface::MODE_READ_WRITE=>'rw'
        };
        if(is_resource($path)) return $path;
        if (($attempt = fopen($path, $mode)) === false) {
            throw new RuntimeException("Unable to open $path");
        }
        return $attempt;
    }

    function contents(): string
    {
        return (stream_get_contents($this->stream) ?: '');
    }

    protected function write(string $data): int
    {
        if ($this->isWritable()) {
            $written = @fwrite($this->stream, $data);
            if ($written === false) {
                throw new RuntimeException("Unable to write to $this->path");
            }
            return $written;
        }
        return -1;
    }

    function eof(): bool
    {
        return feof($this->stream);
    }


    function reset(): StreamInterface
    {
        rewind($this->stream);
        return $this;
    }

    function isSeekable(): bool
    {
        return ($this->size() > 0 || !is_resource($this->stream));
    }

    function isWritable(): bool
    {
        try {
            $result = @fwrite($this->path, "");
        } catch (Error|Exception $e) {
            unset($e);
            return false;
        }
        return $result === 0;
    }

    function isReadable(): bool
    {
        try {
            $result = @stream_get_contents($this->path);
        } catch (Error|Exception $e) {
            unset($e);
            return false;
        }
        return is_string($result);
    }

    protected function copyToStream(Stream $resourceTarget, ?int $length, int $offset = 0): StreamInterface
    {
        if (stream_copy_to_stream($this->stream, $resourceTarget->stream, $length, $offset) === false) {
            throw new RuntimeException("Unable to copy to new stream to '$this->path'");
        }
        return $this;
    }

    protected function copyFromStream(Stream $resourceTarget, ?int $length, int $offset = 0): StreamInterface
    {
        if (stream_copy_to_stream($resourceTarget->stream, $this->stream, $length, $offset) === false) {
            throw new RuntimeException("Unable to copy from stream from '$this->path'");
        }
        return $this;
    }

    protected function initStream(): void
    {
        @rewind($this->stream);
    }

    /**
     * @param string $name
     * @return int|null
     */
    protected function getFilterByName(string $name):?int
    {
        reset($this->filters);
        $index=0;
        while(is_array(current($this->filters) )){
            $currentFilter=current($this->filters);
            $filterName=$currentFilter['name'];
            if($name==$filterName){
                return $index;
            }
            next($this->filters);
            $index++;
        }
        return null;
    }

    /**
     * Indicate whether is it ok to add a filter to a stream
     * @param string $name
     * @return bool
     */
    protected function filterCheck(string $name, string $filterClass): bool
    {
        //check to see if class exists
        if(!class_exists($filterClass)) return false;

        //check to see if class is a child of php_user_filter
        if(($filterParents=class_parents($filterClass)) !== false){
            if(!in_array('php_user_filter',$filterParents)) return false;
        }
        return true;
    }

    protected function filterRegister(string $name, string $filterClass): bool
    {
        if(in_array($name,stream_get_filters())) return false;
        return stream_filter_register($name,$filterClass);
    }
    function removeFilter(string $name): StreamInterface
    {
        $find = $this->getFilterByName($name);
        if ($find === null) {
            throw new InvalidArgumentException("filter \"$name\" does not exist");
        }
        if (!stream_filter_remove($this->filters[$find]['filter'])) {
            throw new Exception("unable to remove \"$name\" filter");
        }
        unset($this->filters[$find]);
        return $this;
    }

    function filterExists(string $name): bool
    {
        return in_array($name, stream_get_filters(),true);
    }

}