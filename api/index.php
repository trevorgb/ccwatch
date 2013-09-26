<?php
// guess what, this is the API script.
require_once('../config.ini');
require_once('classes.php');

// methods allowed
// getTickers
// getPools
// getSlave

$ret = '';
$response = new apiresponse();

switch ($_REQUEST['method']) {
   case 'getTickers':
      // get the last 2 entries for the market.
      $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      $sql = "SELECT * FROM market ORDER BY localtime LIMIT 2";
      $db->query($sql);
      $results = $db->getArray();
      $now = $results[0];
      $past = $results[1];
      switch (true) {
         case ($past['high'] > $now['low']):
            $col = "red";
            break;
         case ($past['high'] < $now['low']):
            $col = "green";
            break;
         default:
            break;
      }
      $ret = "LTC: <span class='".$col."'>".$now['high']."</span>";
      $response->addRecord($ret);
      break;
   case 'getPools':
      // get all the names of the pools here.
      $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      $sql = "SELECT * FROM config WHERE parentid=".INDEXES_MINERS_PARENTS;
      die($sql);
      $response->addRecord(array('name' => 'Pools names and stats go here...'));
      
      break;
   case 'getSlaves':
      // get all slaves.
      $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      $sql = "SELECT DISTINCT name FROM slaves";
      $db->query($sql);
      $recs = $db->getArray();
      $response->addRecords($recs);
      break;
   default:
      $response->setError('ERROR', 100, 100);
}

die($response);