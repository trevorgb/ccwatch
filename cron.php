<?php
require_once('config.ini');
require_once('api/classes.php');

define('UPDATE_MARKET', true);
define('UPDATE_POOL', true);

define ('INDEXES_MARKET_PARENT', 6);
define ('INDEXES_MINERS_PARENT', 4);

date_default_timezone_set('America/New_York');

$db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);

if (UPDATE_MARKET) {
   // Poll latest market data.
   $db->query('SELECT * FROM config WHERE parentid='.INDEXES_MARKET_PARENT);
   $markets = $db->getArray();
   foreach($markets as $market) {
       // now we get our market data.
       $mkt = new market($market['value']);
       $mkt->saveState();
   }
}

if (UPDATE_MINERS) {
    // Poll latest pool data
   $db->query('SELECT * FROM config WHERE parentid='.INDEXES_MINERS_PARENT);
   $pools = $db->getArray();
   foreach ($pools as $pool) {
      $db->query('SELECT * FROM config WHERE parentid='.$pool['id']);
      $poolVals = $db->getArray();
      $options = array();
      foreach($poolVals as $poolVal) {
         $options[$poolVal['key']] = $poolVal['value'];
      }
      $miner = new miner($options['miner_key'], $options['miner_api'], $pool['id']);
      // miner->saveState does the slaves too.
      $miner->saveState();
   }
}