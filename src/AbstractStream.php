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
     * The current read/write position of the stream
     * @var int
     */
    protected int $streamPosition=0;

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
    public $stream;

    /**
     * Array resulting from the parse of the url
     * @var array
     */
    protected array $pathVars;





}