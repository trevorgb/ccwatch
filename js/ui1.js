var minLevel = 0;
// messageToLog is the message we want to log.
// level is the severity of the message.
// 5 is highest.
// 1 is lowest.

$(document).ready( function() {
   initUI();  
});

function initUI() {
   refreshMarket("#marketBlock");
   refreshSlaves("#slaveList");
   
   setInterval("refreshMarket('#marketBlock')",60000);
   $('#mainTabs').tabs();
}

function refreshMarket(targetBlock) {
   $.get('api/?method=getTickers', function(data) {
      if (data.status.level == 0) {
         $(targetBlock).html(data.records[0]);
         logError('Ticker Updated', 1);
      } else {
         logError('Unable to update Ticker.', 5);
      }
   }, 'json');
}

function refreshSlaves(targetBlock) {
   // get all the slaves
   $.get('api/?method=getSlaves', function(data) {
      if (data.status.level == 0) {
         $(data.records).each(function( id ) {
            $('#slaveList ul').append('<li><a href="" id="'+id+'">'+data.records[id].name+'</a></li>');
            logError('Adding Slave('+data.records[id].name+') to List',1);
         })
      } else {
         logError('Unable to get Slaves.', 5);
      }
   }, 'json');
}

/* UTIL */
function logError(messageToLog, level) {
   // add the date for the log entry.
   if (level >= minLevel) {
      var d = new Date();
      $('#logBlock').html(d.toLocaleTimeString()+' : '+messageToLog+'<br />'+$('#logBlock').html());
   }
}