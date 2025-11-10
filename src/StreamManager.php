<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/06/25
 *  Time:   21:36
*/


namespace Foamycastle;

use Foamycastle\Util\Validator\Validator;
use Foamycastle\Utilities\Str;
use InvalidArgumentException;


class StreamManager
{
    /**
     * @var array{string,Stream[]}
     */
    private static array $streams = [];
    private static array $badWords = [
        "STDOUT","STDIN","STDERROR",'REPORT'
    ];

    public static function Alias(string $alias):void
    {
        spl_autoload_register(function ($class) use ($alias) {
            if (basename($class) === $alias) {
                spl_autoload(self::class);
            }
        });
    }

    /**
     * @param string $name
     * @param string|resource $path
     * @return void
     */
    public static function CreateStream(
        string $name,
        $path
    ):void
    {
        $stream = new WriteStream(STDOUT,$name);
        self::RegisterStream($name,$stream);
    }

    public static function DestroyStream(string $name):void
    {
        self::UnregisterStream($name);
    }

    protected static function RegisterStream(string $name, Stream $stream):void
    {
        self::$streams[$name] = $stream;
    }

    protected static function UnregisterStream(string $name):void
    {
        if(!self::hasStream($name)) return;
        unset(self::$streams[$name]);
    }
    public static function __callStatic(string $name, array $arguments):?Stream
    {
        if(!self::hasStream($name)) return null;
        if(!empty($arguments)){
            self::$streams[$name]->write(Str::StringFrom($arguments[0]));
            return self::$streams[$name];
        }
        return self::$streams[$name];
    }

    public static function hasStream(string $name):bool
    {
        return isset(self::$streams[$name]);
    }
}