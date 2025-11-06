<?php
/*
 *  Author: Aaron Sollman
 *  Email:  unclepong@gmail.com
 *  Date:   11/04/25
 *  Time:   20:07
*/


include "../vendor/autoload.php";

//use statements

$tests = [
    [
        '', ''
    ]
];

$passing = array_filter($tests, function ($test) {
    $funcName = array_shift($test);
    return call_user_func($funcName, ...$test);
});
$time = microtime(true);
echo "PASSING TESTS: \n\n";
print_r(array_column($passing, 0));
echo "\n\nFAILING TESTS: \n\n";
print_r(
    array_diff(
        array_column($tests, 0),
        array_column($passing, 0)
    )
);
echo "\n\nSTARTING TIME: " . $time . "\n";
echo "END TIME:      " . microtime(true) . "\n";

//test functions
