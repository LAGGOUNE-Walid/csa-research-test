<?php

use App\Db\DB;
use App\Http\Handler;

require "vendor/autoload.php";

$httpHandler = Handler::withDatabaseConnection(DB::getInstance()->getConnection());
$httpHandler->handle($_SERVER['REQUEST_METHOD'], trim($_SERVER['REQUEST_URI'], "/"));