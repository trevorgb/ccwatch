<?php
require_once('config.ini');
require_once('api/classes.php');

define('DBTYPE_MYSQL', 'mysql');
define('DEBUG', true);
date_default_timezone_set('America/New_York');


$db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
$triggerMin = date('i');
// Poll latest market data.
$sql = 'SELECT * FROM config WHERE parentid='.INDEXES_MARKET_PARENT;
$db->query($sql);
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
   if (($triggerMin % $options['frequency']) == 0){
      // ..save the data.
     $mkt = new market($options['api'], $options['id']);
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
   foreach($poolVals as $poolVal) {
      $options['id'] = $poolVal['parentid'];
      $options[$poolVal['key']] = $poolVal['value'];
   }
   if ((($triggerMin % $options['frequency']) == 0) || DEBUG) {
         $pool = new pool($options['key'], $options['api'], $options['id']);
      // pool->saveState does the slaves too.
      $pool->saveState();
   }
}

die();


// COINBOX STUFF HERE;
//$db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);


$url = 'http://coinbox.me/?a=1HzV762FPsrsy3khorD8PWSydj2YyJ6nnq';

$urlContent = file_get_contents($url);

// Specify configuration
$config = array(
           'indent'         => true,
           'output-xhtml'   => true,
           'wrap'           => 200);

// Tidy
$tidy = new tidy;
$tidy->parseString($urlContent, $config, 'utf8');
$tidy->cleanRepair();

$doc = new DOMDocument();
//libxml_use_internal_errors(true);
$doc->loadHTML($tidy);
$h4s = $doc->getElementsByTagName('h4');
foreach($h4s as $h4) {
   if (strpos($h4->textContent, 'Pending amount') > 0) {
      echo "PENDING".$h4->textContent."\n";
   }
   
   if (strpos($h4-textContent, 'paid') > 0) {
      echo "PAID".$h4->textContent."\n";
   }
}



