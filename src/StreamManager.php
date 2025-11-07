<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/06/25
 *  Time:   21:36
*/


namespace Foamycastle;

use Foamycastle\Util\Validator\Validator;
use InvalidArgumentException;


class StreamManager
{
    /**
     * @var array{string,Stream[]}
     */
    private static array $streams = [];
    private static array $badWords = [
        "STDOUT","STDIN","STDERROR",
    ];
    public static function Init():void
    {
        Validator::From(
            'streamName',
            function($name){
                if(
                    method_exists(StreamManager::class, $name) ||
                    in_array($name,self::$badWords)
                ) return false;
                return true;
            }
        );
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
        if(!Validator::streamName($name)){
            throw new InvalidArgumentException("Stream name '{$name}' is not a valid stream name");
        }
        $stream = new WriteStream(STDOUT,$name);
        self::RegisterStream($name,$stream);
    }

    public static function DestroyStream(string $name):void
    {
        self::UnregisterStream($name);
    }

    protected static function RegisterStream(string $name, Stream $stream):void
    {
        if(!self::hasStream($name)) return;
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
        if(!empty($arguments) && is_string($arguments[0])){
            return self::$streams[$name]->write($arguments[0]);
        }
        return self::$streams[$name];
    }

    public static function hasStream(string $name):bool
    {
        return isset(self::$streams[$name]);
    }
}