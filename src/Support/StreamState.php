<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   16:17
*/

namespace Foamycastle\Support;

enum StreamState
{
    case READ;
    case WRITE;
    case READWRITE;
    case CLOSED;
    case UNKNOWN;
}
