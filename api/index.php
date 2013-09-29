<?php
// guess what, this is the API script.
require_once('../config.ini');
require_once('classes.php');

// methods allowed
// getTickers
// getPools
// getSlave

$response = new apiresponse();

$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'help';
$param  = (isset($_REQUEST['param']))  ? $_REQUEST['param']  : '';

switch ($method) {
   case 'getTickers':
      getTickers($response);
      break;
   case 'getPools':
      getPools($response);
      break;
   case 'getPool':
      getPool($response, $id);
      break;
   case 'getSlaves':
      getSlaves($response, $id);
      break;
   case 'getSlave':
      getSlave($response, $param);
      break;
   case 'help':
   default:
      // build the help here.
      $response->setError('show the user help', 100, 100);
}

die($response);

function getPool($response, $id) {
   // get a specific pool id.
   $response->setError('Not Coded', 100, 100);
}

function getSlave($response, $id) {
   // get a specific slave.
   $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
   $sql = "SELECT * FROM slaves WHERE id=".$id;
   $db->query($sql);
   $recs = $db->getArray();
   var_dump($recs);
   $response->addRecords($recs);
}

function getSlaves($response) {
      // get all slaves.
      $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      $sql = "select distinct poolid,name from slaves";
      $db->query($sql);
      $recs = $db->getArray();
      $response->addRecords($recs);   
}

function getPools($response) {
   // get all the names of the pools here.
   $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
   $sql = "SELECT * FROM config WHERE parentid=".INDEXES_MINERS_PARENT;
   die($sql);
   $response->addRecord(array('name' => 'Pools names and stats go here...'));
}

function getTickers($response) {
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
}