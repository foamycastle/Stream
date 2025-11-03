<?php
include '../vendor/autoload.php';

use Foamycastle\Stream\Stdout;
use Foamycastle\Stream\TerminalControl;

$out=new Stdout();

TerminalControl::OutputStream($out);
TerminalControl::BackColor(195);
TerminalControl::ForeColor(17);
$out->line("This is some sample text");
