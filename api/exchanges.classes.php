<?php
class exchange {
    public static function getData($params) {
      switch($type) {
      case 'cryptsy':
        $vals = exchange_cryptsy::apiquery('getinfo', $key, $secret);
        if ($vals['success'] == 1) {
            $vals = $vals['return']['balances_available'];
        }
        break;
      case 'BTCE':
        $vals = exchange_btce::apiquery('getInfo', $key, $secret);
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
    
   public static function getInfo($key, $secret, $type) {
      $opts = array('key' => $key, 'secret' => $secret, 'type' => $type);
      return exchange::getData($opts);
   }
}

class exchange_btce {
    private static function apiquery($method, $key, $secret) {
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
      $opts = array(CURLOPT_URL => 'https://btc-e.com/tapi/',
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_SSL_VERIFYPEER => FALSE);
      curl_setopt_array($ch, $opts);
   
      // run the query
      $res = curl_exec($ch);
      if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
      $dec = json_decode($res, true);
      if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
      return $dec;
    }
}


class exchange_cryptsy {
   private static function apiquery($method, $key, $secret, array $req = array()) {
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
        $opts = array(CURLOPT_URL => 'https://www.cryptsy.com/api',
                      CURLOPT_POSTFIELDS => $post_data,
                      CURLOPT_HTTPHEADER => $headers,
                      CURLOPT_SSL_VERIFYPEER => FALSE);
        curl_setopt_array($ch, $opts);
 
        // run the query
        $res = curl_exec($ch);

        if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
        $dec = json_decode($res, true);
        if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
        return $dec;
   }
}

