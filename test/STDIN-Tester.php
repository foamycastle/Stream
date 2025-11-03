<?php
include "../vendor/autoload.php";

use Foamycastle\Stream\Stdin;
use Foamycastle\Stream\TerminalControl;

$stdin = new STDIN();



TerminalControl::ForeColor(142);
TerminalControl::BackColor(199);
echo "Enter your thing: "; $get=$stdin->getLine(); echo "\n";
TerminalControl::ForeColor(0);
TerminalControl::BackColor(0);
echo "You entered:  $get"; echo "\n";
TerminalControl::ForeColor(85);
TerminalControl::BackColor(18);
echo "Enter another thing: "; $another=$stdin->getChar(); echo "\n";
TerminalControl::ForeColor(0);
TerminalControl::BackColor(0);
echo "You entered:  $another"; echo "\n";

