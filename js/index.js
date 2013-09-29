$(document).ready(function() {
   
   // load pools.
   init_ui();
   $('#interface').tabs();
   $('#pools').accordion();
   /*
   $('.gogo').click( function( sender) {
      load_page(sender.target.id, $('#num').val());
   });
   */
});

function init_ui() {
   // init the Pools
   $.ajax({url: 'api/?method=getPools',success: init_pools,dataType: 'json' });
   
   // init the Markets
   $.ajax({url: 'api/?method=getMarkets',success: init_markets,dataType: 'json' });
   
   // init the Miners
}

function init_markets(src) {
   if (src.status.code == 200) {
      $('#markets').html('');
      jQuery.ajaxSetup({async:false});
      var markets = src.records;
      $(markets).each( function( index ) {
         var market = markets[index];
         $.get('api/?method=getMarket&param='+market.id, function(marketData) {
            if (marketData.status.code == 200) {
               var record = marketData.records[0];
               $.get('int/summary.market.xml', function(xmlData) {
                  // load the form...
                  $('#markets').append('<div id=market'+market.id+'>'+xmlData+'</div>');
                  // ...and set the values.
//                  $('#market'+market.id+' #currency').val(String(market.market_currency));
                  $('#market'+market.id+' legend').text(market.value);
                  $('#market'+market.id+' #low').val(String(record.low));
                  $('#market'+market.id+' #high').val(String(record.high));
                  $('#market'+market.id+' #volume').val(String(record.volume_current));
                  
               }, 'html');
            };
         }, 'json');
      });
   } else {
      alert(src.status.message);
   }
}

function init_pools(src) {
   if (src.status.code == 200) {
      $('#pools').html('');
      // go to sync mode
      jQuery.ajaxSetup({async:false});
      var pools = src.records
      $(pools).each( function( index ) {
         var pool = pools[index];
         // build and append the body for each pool
         // get the pool details
         $.get('api/?method=getPool&param='+pool.id, function(poolData) {
            if (poolData.status.code == 200) {
               record = poolData.records[0];
               // here we put 'summary.pool.xml'
               $.get('int/summary.pool.xml', function(xmlData) {
                  $('#pools').append('<div id="pool'+pool.id+'">'+xmlData+'</div>');
                  // and set the values.
                  $('#pool'+pool.id+' legend').text(String(pool.value));
                  $('#pool'+pool.id+' #unpaidShares').val(String(record.round_shares));
                  $('#pool'+pool.id+' #income').val(String(record.total_income));
                  $('#pool'+pool.id+' #hashrate').val(String(record.total_hashrate));
                  $('#pool'+pool.id+' #estimate').val(String(record.round_estimate));
                  $('#pool'+pool.id+' #projected').val(String(record.income_estimate));
                  $('#pool'+pool.id+' #balance').val(String(record.balance));
               }, 'html');
            }
         }, 'json');
      });
      // go back to async mode
      jQuery.ajaxSetup({async:true});
   } else {
      alert('API error');
   }
}

function load_page(pageToLoad, idToLoad) {
   switch(pageToLoad) {
      case 'summary.market.xml':
         var toAddToPage = '';
         $.get('int/summary.market.xml', function(data) {
            toAddToPage = data;
            $.get('api/?method=getMarket&param='+idToLoad, function(data) {
               var record = data.records[0];
               $('#action').html(toAddToPage);
               $('#action #name').text(record.name);
               $('#action #market_currency').text(record.market_currency);
               $('#action #market_high').text(record.high);
               $('#action #market_low').text(record.low);
               alert('summary.market done');
            });
         }, 'html');
         break;
      case 'summary.slave.xml':
         var toAddToPage = '';
         $.get('int/summary.slave.xml', function(data) {
            toAddToPage = data;
            $.get('api/?method=getSlave?param='+idToLoad, function(data) {
               var record = data.records[0];
               $('#action').html(toAddToPage);
               $('#action #name').text(record.name);
               $('#action #hashrate').text(record.hashrate);
            });
         }, 'html');
         break;
   }
}