(function($) {

  $(document).ready(function () {
  //iterate through each row in the table
  $('tr').each(function () {
      //the value of sum needs to be reset for each row, so it has to be set inside the row loop
      var sum = 0
      //find the combat elements in the current row and sum it
      $(this).find('.product').each(function () {
          var combat = $(this).text();
          if (!isNaN(combat) && combat.length !== 0) {
              sum += parseFloat(combat);
          }
      });
      //set the value of currents rows sum to the total-combat element in the current row
      $('.total-product', this).html(sum);
  });
});
})
(jQuery);
