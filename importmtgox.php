<?php

require_once('config.ini');
require_once('api/classes.php');

define('DBTYPE_MYSQL', 'mysql');

date_default_timezone_set('America/New_York');

//$db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);

// here we open the CSV file and parse it into the database.

$file = 'mtgoximport.csv';

$fileHandle = fopen($file, 'r');

while(!feof($fileHandle)) {
   $line = fgets($fileHandle);
}

fclose($fileHandle);


// 2
// 2013-10-02 23:25
// out
// BTC sold: [tid:1380756313122540]
// 0.16999750åÊBTC at $124.00000
// 0.1699975
// 21.0769
// 0
// 21.07969
