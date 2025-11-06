<?php
require('../includes/sdk.php');

$orig = urldecode('2bhXF8%2FRx6fmVx%2B%2Fs8aKuy1Z0y%2B%2B%2BqSlKDTw%2FfMXhgA%3D');
$oidx = Crypto::dv($orig);
echo "{$orig} => {$oidx}\n\n";

echo Crypto::ev('5471');
