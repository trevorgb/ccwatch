<?php
// guess what, this is the API script.
require_once('../config.ini');
require_once('classes.php');

$response = new apiresponse();

$model  = (isset($_REQUEST['model'])) ? $_REQUEST['model'] : 'util';
$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : 'help';
$param  = (isset($_REQUEST['param']))  ? $_REQUEST['param']  : '';

if (class_exists($model)) {
   if (method_exists($model, $method)) {
      $x = new $model;
      $x->$method($response, $param);
   } else {
      $response->setError('API ERROR', 1000, 101); // method not found
   }
} else {
   $response->setError('API ERROR', 1000, 100); // model not found
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

class util {
   function help($request) {
     // build the 'API HELP' page.
     $request->addRecord('API Help to be written.');
   }
}


class markets {
   public function getMarkets($response) {
      $response->addRecords(
         db::query("select * from config WHERE parentid=".INDEXES_MARKET_PARENT));   
   }
   
   public function getMarket($response, $id) {
      $response->addRecord(
         db::query("SELECT * FROM market WHERE source=".$id." ORDER BY local_time DESC LIMIT 1",
            true));
   }
}

class miners {
    public function getMiners($response) {
         $response->addRecords(db::query("SELECT * FROM miners"));
    }
    
    public function getMiner($response, $id) {
      $response->addRecord(db::query("SELECT * FROM miners WHERE id=".$id, true));
    }
}

class pools {
   public function getPool($response, $id) {
   $response->addRecord(
         db::query("SELECT * FROM pool WHERE poolid=".$id." ORDER BY local_time DESC LIMIT 1",
            true));
   }
   
   public function getPools($response) {
      $response->addRecords(
      db::query("SELECT * FROM config WHERE parentid=".INDEXES_MINERS_PARENT));
   }
}

class slaves {
   public function getSlave($response, $id) {
      // get a specific slave.
      $response->addRecord(
            db::query("SELECT * FROM slaves WHERE =".$id, true));
   }
   
   public function getSlaves($response) {
      $response->addRecords(
            db::query("select distinct poolid,name from slaves", true));
   }   
}