<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   11:54
*/

namespace Foamycastle\Support;

enum Mode
{
    case READ;
    case WRITE;
    case READWRITE;

    public static function toString(Mode $mode):string
    {
        return match($mode) {
            Mode::WRITE => "w",
            Mode::READ => "r",
            Mode::READWRITE => "r+",
        };
    }
}
