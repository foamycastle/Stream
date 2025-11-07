<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   19:12
*/

include "../vendor/autoload.php";

use Foamycastle\Stream;
use Foamycastle\Support\Whence;
use Foamycastle\WriteStream;
use Foamycastle\Support\Support;

$tests=[
    [
        'test__construct','php://memory'
    ],
    [
        'testWrite','php://memory','this is some test data'
    ],
    [
        'testRead','php://memory'
    ],
    [
        'testLength','php://memory','this is some test data'
    ],
    [
        'testSeekAndTell','php://memory','this is some test data'
    ],
    [
        'testEOF','php://memory','this is some test data'
    ],
    [
        'testGetMemLimit'
    ],
    [
        'testSeekOverMemLimit'
    ]
];

$passing=array_filter($tests,function($test){
    $funcName=array_shift($test);
    return call_user_func($funcName,...$test);
});
$starttime=gettimeofday();
echo "PASSING TESTS: \n\n";
print_r(array_column($passing,0));
echo "\n\nFAILING TESTS: \n\n";
print_r(
    array_diff(
        array_column($tests,0),
        array_column($passing,0)
    )
);
echo "\n\nELAPSED TIME: ".tod_diff($starttime,gettimeofday())." microseconds\n";

function tod_diff(array $timeStart,array $timeEnd):int
{
    return
        (($timeEnd["sec"]-$timeStart["sec"])*1000000) +
        (($timeEnd["usec"]-$timeStart["usec"]));
}
function test__construct(string $path):bool
{
    $write = new WriteStream($path,'name');
    return is_resource($write->getResource());
}

function testWrite(string $path, string $data):bool
{
    $write = new WriteStream($path,'name');
    $len = $write->write($data);
    return $len==strlen($data);
}
function testRead(string $path):bool
{
    $read = new WriteStream($path,'name');
    $attempt= $read->read();
    return (!Stream::Readable($read) && $attempt=='');
}
function testLength(string $path, string $data):bool
{
    $write = new WriteStream($path,'name');
    $len = $write->write($data);
    return $len==$write->length();
}
function testSeekAndTell(string $path, string $data):bool
{
    $write = new WriteStream($path,'name');
    $len = $write->write($data);
    return $write->seek(0, Whence::SET) && $write->tell()==0;
}

function testEOF(string $path, string $data): bool
{
    $write = new WriteStream($path,'name');
    $len = $write->write($data);
    $write->seek(123456,Whence::SET);
    return !$write->eof();
}
function testGetMemLimit():bool
{
    return (128*(1024*1024)) == Support::GetMemLimit();
}
function testSeekOverMemLimit():bool
{
    $write = new WriteStream('php://memory','name');
    $write->write(random_bytes(1024));
    $expectTrue=$write->seek(1,Whence::SET);
    $expectFail=$write->seek(129*(1024*1024),Whence::SET);
    return $expectTrue && !$expectFail;
}
