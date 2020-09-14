<?php

include_once '../../vendor/autoload.php';

use App\Support\CheckHeader;

$checker = new CheckHeader();
$checker->main();