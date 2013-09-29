$(document).ready(function() {
   ui.init();
});

var ui = {
   minErrorLevel: 100,
   init: function() {
      // init the tabs
      $('#interface').tabs();

      pools.init(api.call('pools', 'getPools'));
      markets.init(api.call('markets', 'getMarkets'));   

      // init the Exchanges
//      exchanges.init(api.call('exchanges', 'getExchanges'));
      
      // init the Miners
      miners.init(api.call('miners', 'getMiners'));
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
   },
   error: function(title, status, level) {
      if (level >= ui.minErrorLevel) {
         alert(status.message);
      } else {
         console.log(status.message);
      }
   }
};

var miners = {
   init: function(src) {
      if (src.status.code == 200) {
         $('#miners').html('');
         var miners = src.records;
         $(miners).each(function( index, miner) {
            var minerData = api.call('miners', 'getMiner', miner.id);
            if (minerData.status.code == 200) {
               var record = minerData.records[0];
               ui.setContent(api.load('summary.miners'),
                             minerData.records[0],
                             'miners',
                             miner.id);
            } else {
               ui.error('API Error', minerData.status, 1000);
            }
         });
      } else {
         ui.error('API ERROR', src.status, 1000);
      }
   }
};

var markets = {
   init: function(src) {
      if (src.status.code == 200) {
         $('#markets').html('');
         var markets = src.records;
         $(markets).each( function( index, market ) {
            var marketData = api.call('markets', 'getMarket', market.id);
            if (marketData.status.code == 200) {
               ui.setContent(api.load('summary.market'),
                             marketData.records[0],
                             'markets',
                             market.id);
            } else {
               ui.error('API ERROR', src.status, 1000);
            }
         });
      } else {
         ui.error('API ERROR', src.status, 1000);
      };
   }
};



var exchanges = {
   init: function(src) {
      /*
      if (src.status.code == 200) {
         $('#exchanges').html('');
         jQuery.ajaxSetup({async:false});
         var exchanges = src.records;
         $exchanges.each( function(index) {
            var exchange = exchanges[index];
            $.get()
         });
      } else {
         alert(src.status.message);
      }
      */
   }
};

var api = {
   call: function(type, method, param) {
      
      jQuery.ajaxSetup({async:false});
      var result = '';
      var url = 'api/?model='+type+'&method='+method;
      if (param !== undefined) {
         url += '&param='+param;
      }
      $.get(url, function(data) {
         result = data;
      }, 'json');
      jQuery.ajaxSetup({async:true});
      return result;
   },
   load: function( chunk ) {
      jQuery.ajaxSetup({async:false});
      var result = '';
      var url = 'int/'+chunk+'.xml';
      $.get(url, function(data) {
         result = data;
      }, 'html');
      return result;
   }
}

var pools = {
   init: function (src) {
      if (src.status.code == 200) {
         $('#pools').html('');
         var pools = src.records;
         $(pools).each( function( index, pool ) {
            poolData = api.call('pools', 'getPool', pool.id);
            if (poolData.status.code == 200) {
               var poolRecord = poolData.records[0];
               xmlData = api.load('summary.pool');
               poolRecord.name = pools[index].value;
               ui.setContent(xmlData,poolRecord,'pools', pool.id);
            } else {
               ui.error('API ERROR', poolData.status, 1000);
            }
         });
      } else {
         ui.error('API ERROR', src.status, 1000);
      }
   }
};