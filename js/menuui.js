$(function() {
   initMenu();
   $('#mainMenu').menu();
})

function initMenu() {
   $.get('api/?method=getPools', function(data) {
      
      $('#mainMenu').menu();
   }, 'json');
}