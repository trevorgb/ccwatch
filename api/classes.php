<?php
require_once('gb.classes.php');

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
            'poolid' => $this->poolid,
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


class exchange {
   public static function getInfo($key, $secret, $type) {
    
      switch($type) {
      case 'cryptsy':
        $vals = self::cryptsyquery('getinfo', $key, $secret);
        if ($vals['success'] == 1) {
            $vals = $vals['return']['balances_available'];
        }
        break;
      case 'BTCE':
        $vals = self::btcequery('getInfo', $key, $secret);
        $vals = $vals['return']['funds'];
      }
      $balances = array();
      foreach($vals as $c=>$b) {
        if ($b > 0) {
            $balances[$c] = $b;
        }
      }
      return $balances;
   }
   
    private static function btcequery($method, $key, $secret) {
            // API settings
      $req['method'] = $method;
      $mt = explode(' ', microtime());
      $req['nonce'] = $mt[1];
      
      // generate the POST data string
      $post_data = http_build_query($req, '', '&');
   
      $sign = hash_hmac("sha512", $post_data, $secret);
   
      // generate the extra headers
      $headers = array(
         'Sign: '.$sign,
         'Key: '.$key,
      );
   
      // our curl handle (initialize if required)
      static $ch = null;
      if (is_null($ch)) {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; BTCE PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
      }
      $opts = array('CURLOPT_URL' => 'https://btc-e.com/tapi/',
                    'CURLOPT_POSTFIELDS' => $post_data,
                    'CURLOPT_HTTPHEADER' => $headers,
                    'CURLOPT_SSL_VERIFYPEER' => FALSE);
      curl_setopt_array($opts);
   
      // run the query
      $res = curl_exec($ch);
      if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
      $dec = json_decode($res, true);
      if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
      return $dec;
    }
   
   private static function cryptsyquery($method, $key, $secret, array $req = array()) {
        // API settings
        $req['method'] = $method;
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1];
       
        // generate the POST data string
        $post_data = http_build_query($req, '', '&');

        $sign = hash_hmac("sha512", $post_data, $secret);
 
        // generate the extra headers
        $headers = array(
                'Sign: '.$sign,
                'Key: '.$key,
        );
 
        // our curl handle (initialize if required)
        static $ch = null;
        if (is_null($ch)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; Cryptsy API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
        }
        $opts = array('CURLOPT_URL' => 'https://www.cryptsy.com/api',
                      'CURLOPT_HTTPHEADER' => $post_data,
                      'CURLOPT_HTTPHEADER' => $headers,
                      'CURLOPT_SSL_VERIFYPERR' => FALSE);
        curl_setopt_array($opts);
 
        // run the query
        $res = curl_exec($ch);

        if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
        $dec = json_decode($res, true);
        if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
        return $dec;
   }
}
