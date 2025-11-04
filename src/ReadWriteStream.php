<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/03/25
 *  Time:   9:17
*/

namespace Foamycastle\Stream;

use Exception;
use Foamycastle\Stream\Stream;
use InvalidArgumentException;

class ReadWriteStream extends Stream
{
    /**
     * Stores a list of currently active filters on the stream
     * @var array{name:string,filter:resource}
     */
    protected array $writeFilters = [];
    /**
     * @param string|resource $path
     * @param string $name
     * @throws Exception
     */
    public function __construct($path, string $name="")
    {
        if(!is_string($path) && !is_resource($path)){
            throw new InvalidArgumentException("Path must be string or resource");
        }
        $this->name = $name;
        $this->path=$path;
        $this->mode=StreamInterface::MODE_READ_WRITE;
        $this->stream = $this->open($path,$this->mode);

        stream_set_write_buffer($this->stream, 4096);
        stream_set_read_buffer($this->stream, 4096);
        stream_set_chunk_size($this->stream, 4096);

        parent::registerStream($name,$this);
    }

    function attachFilter(string $name, string $filterClass, array $options=[]): StreamInterface
    {
        if(!$this->filterCheck($name,$filterClass)){
            throw new Exception('Cannot add filter '.$name. '. Did not pass filter check.');
        }

        $this->filterRegister($name,$filterClass);
        $newHandle=stream_filter_append($this->stream,$name,STREAM_FILTER_ALL,$options);
        $this->filters[]=['name'=>$name,'filter'=>$newHandle];
        return $this;

    }


}