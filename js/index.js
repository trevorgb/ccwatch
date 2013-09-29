$(document).ready(function() {
   ui.init();
});

var ui = {
   init: function() {
      $('#interface').tabs();

      // init the Pools
      $.ajax({url: 'api/?method=getPools',success: pools.init,dataType: 'json' });
   
      // init the Markets
      $.ajax({url: 'api/?method=getMarkets',success: markets.init,dataType: 'json' });
   
   // init the Miners
   },
   setContent: function(frame, data, target, id) {
      var newID = target+id;
      $('#'+target).append('<div id="'+newID+'">'+frame+'</div>');
      $('#'+newID+' legend').text(String(data.name));
      $(data).each( function(index) {
         // go through the data items and set values
         for (var property in data) {
            if (data.hasOwnProperty(property)) {
                // do stuff
                var toGet = eval('data.'+property);
                //alert('setting '+newID+' #'+property+' to '+toGet);
                $('#'+newID+' #'+property).val(String(toGet));
            }
        }
      });
   }
}

var markets = {
   init: function(src) {
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
                     record.name = markets[index].value;
                     ui.setContent(xmlData, record, 'markets', market.id);
                  }, 'html');
               };
            }, 'json');
         });
      } else {
         alert(src.status.message);    
      };
   }
};
   
   
var pools = {
   init: function (src) {
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
};

function load_page(pageToLoad, idToLoad) {
   switch(pageToLoad) {
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

function setValues() {
   
}