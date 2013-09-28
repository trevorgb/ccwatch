<?php
require_once('config.ini');
require_once('api/classes.php');

define('DBTYPE_MYSQL', 'mysql');

date_default_timezone_set('America/New_York');

$db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);

// Poll latest market data.
$db->query('SELECT * FROM config WHERE parentid='.INDEXES_MARKET_PARENT);
$markets = $db->getArray();
foreach($markets as $market) {
    // now we get our market data.
    // get the actual details.
    $db->query("SELECT * FROM config WHERE parentid=".$market['id']);
    $mktVals = $db->getArray();
    $options = array();
    $options['id'] = $market['id'];
    foreach($mktVals as $mktVal) {
      $options[$mktVal['key']] = $mktVal['value'];
   }
   // if the minutes is divisible by the delay evenly...
   if (!($options['market_frequency'] % date('i'. time()))) {
      // ..save the data.
     $mkt = new market($options['market_api'], $options['id']);
     $mkt->saveState();
   }
}

// Poll latest pool data
$db->query('SELECT * FROM config WHERE parentid='.INDEXES_MINERS_PARENT);
$pools = $db->getArray();
foreach ($pools as $pool) {
   $db->query('SELECT * FROM config WHERE parentid='.$pool['id']);
   $poolVals = $db->getArray();
   $options = array();
   $options['pool_id'] = $pool['id'];
   foreach($poolVals as $poolVal) {
      $options[$poolVal['key']] = $poolVal['value'];
   }
   if (!($options['pool_frequency']) % date('i', time())) {
         $pool = new pool($options['pool_key'], $options['pool_api'], $options['pool_id']);
      // pool->saveState does the slaves too.
      $pool->saveState();
   }
}
