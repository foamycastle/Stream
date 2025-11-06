<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   20:02
*/

namespace Foamycastle\Support;

class Support
{
    /**
     * @var int storage for the memory limit value so that it doesn't have to be calculated multiple times
     */
    private static int $memLimit;
    /**
     * Find the memory limit specified in php.ini and return a byte-limit integer value
     * @return int the discreet value of the memory limit in bytes
     */
    public static function GetMemLimit():int
    {
        if (isset(self::$memLimit)) return self::$memLimit;
        $ini=strtoupper(ini_get('memory_limit')) ?: '32M';
        $mults=[
            'K'=>1024,
            'M'=>1048576,
            'G'=>1073741824,
        ];

        foreach (['K','M','G'] as $mark) {
            if(str_ends_with($ini,$mark)) {
                return self::$memLimit=($mults[$mark]*intval($ini));
            }
        }
        return self::$memLimit=intval($ini);
    }
}