<?php
// guess what, this is the API script.
require_once('../config.ini');
require_once('classes.php');

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
      getPool($response, $param);
      break;
   case 'getSlaves':
      getSlaves($response, $param);
      break;
   case 'getMarkets':
      getMarkets($response);
      break;
   case 'getMarket':
      getMarket($response, $param);
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

class db {
   public static function query($sql, $single = false) {
      $db = new gbdb($sql, BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      if ($single) {
         $r = $db->getSingleArray();
      } else {
         $r = $db->getArray();
      }
      return $r;
   }
}

function getMarkets($response) {
   $response->addRecords(
         db::query("select * from config WHERE parentid=".INDEXES_MARKET_PARENT));
}

function getMarket($response, $id) {
   $response->addRecord(
         db::query("SELECT * FROM market WHERE source=".$id." ORDER BY local_time DESC LIMIT 1",
            true));   
}


function getPool($response, $id) {
   $response->addRecord(
         db::query("SELECT * FROM pool WHERE poolid=".$id." ORDER BY local_time DESC LIMIT 1",
            true));
}

function getSlave($response, $id) {
   // get a specific slave.
   $response->addRecord(
         db::query("SELECT * FROM slaves WHERE =".$id, true));
}

function getSlaves($response) {
   $response->addRecords(
         db::query("select distinct poolid,name from slaves", true));
}

function getPools($response) {
   $response->addRecords(
         db::query("SELECT * FROM config WHERE parentid=".INDEXES_MINERS_PARENT));
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