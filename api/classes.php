<?php
require_once('gb.classes.php');
require_once('exchanges.classes.php');

class pool {
    private $api_key = '';
    private $api_url = '';
    private $poolid  = 0;
    
    public function __construct($apiKey, $apiURL, $poolid) {
        $this->api_key = $apiKey;
        $this->api_url = $apiURL;
        $this->poolid  = $poolid;
        $this->getData();
    }
    
    public function getData() {
      $raw = gbwebclient::get($this->api_url, array('api_key' => $this->api_key));
      $this->state = json_decode($raw);
    }
    
    public function saveState() {
      // save the current 'pool' state and the 'slave' states.
      $poolVals = array('poolid' => $this->poolid,
         'balance' => $this->state->confirmed_rewards,
         'round_estimate' => $this->state->round_estimate,
         'total_hashrate' => $this->state->total_hashrate,
         'total_income' => $this->state->payout_history,
         'round_shares' => $this->state->round_shares,
         'income_estimate' => $this->state->round_estimate * $this->state->round_shares,
         'local_time' => time()
      );
      $db = new gbdb('', BADBOY_DBHOST, BADBOY_DBUSER, BADBOY_DBPASS, BADBOY_DBNAME, DBTYPE_MYSQL);
      $db->insert('pool', $poolVals);
      foreach ($this->state->workers as $name => $body) {
         $slaveVals = array('name' => $name,
            'poolid' => $poolVals->poolid,
            'alive' => $body->alive,
            'hashrate' => $body->hashrate,
            'lastreport' => $body->last_share_timestamp,
            'local_time' => time());
         $db->insert('slaves', $slaveVals, false, true);
      }
    }
}

class market {
    private $market_url = '';
    private $marketid = 0;
    private $state = '';
    public $status = array();
    
    public function __construct($tickerURL = '', $marketID) {
      $this->marketid = $marketID;
      $this->market_url = $tickerURL;
      $ticker = json_decode($this->getData());
      $this->state = $ticker->ticker;
    }
    
    public function getData() {
        $data = gbwebclient::get($this->market_url, array())."\n";
        return $data;
    }
    
    public function saveState() {
      $this->status = array('high' => $this->state->high,
         'low' => $this->state->low,
         'average' => $this->state->avg,
          'volume' => $this->state->vol,
          'volume_current' => $this->state->vol_cur,
          'last' => $this->state->last,
          'buy' => $this->state->buy,
          'sell' => $this->state->sell,
          'updated' => $this->state->updated,
          'server_time' => $this->state->server_time,
          'local_time' => time(),
          'source' => $this->marketid
      );
      // FIXME: hardcoded database creds.
      $db = new gbdb('', BADBOY_DBHOST,
         BADBOY_DBUSER,
         BADBOY_DBPASS,
         BADBOY_DBNAME,
         DBTYPE_MYSQL);
      $db->insert('market', $this->status, false, true);
    }
}

