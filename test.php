<?php

use Utils\BinaryOpertions;

include "src/Utils/Entr.php";
include "src/Utils/Math.php";
include "src/Utils/BinaryOpertions.php";
include "src/Nodes/Node.php";
include "src/FanoCoder.php";

$fanoCoder = new FanoCoder("/var/www/tik/fano/test.txt");
$bytes = $fanoCoder->encode(1);
echo $fanoCoder->decodeFile("/var/www/tik/fano/test.txt.huf");

