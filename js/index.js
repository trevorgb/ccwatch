$(document).ready(function() {
   $('.gogo').click( function( sender) {
      load_page(sender.target.id, $('#num').val());
   });
});

function load_page(pageToLoad, idToLoad) {
   switch(pageToLoad) {
      case 'summary.pool.xml':
         var toAddToPage = '';
         $.get('int/summary.pool.xml', function(data) {
           toAddToPage = data;
            // now load the JSON for the ID and fill in the boxes.
            $.get('api/?method=getPool&param='+idToLoad, function( data ) {
               var record = data.records[0];
               $('#action').html(toAddToPage);
               $('#action #name').text(record.name);
               $('#action #hashrate').text(record.hashrate);
               $('#action #paidshares').text(record.paid);
               alert('summary.pool done');
            }, 'json');
         }, 'html');
         break;
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