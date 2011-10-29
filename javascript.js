$(document).ready(function() {
  $('.flash').css("display", "none");
  if ($('#notice').html().length > 0) {
    $('#info').css("display", "none");
    $('#notice').slideDown();
  }
  if ($('#error').html().length > 0) {
    $('#info').css("display", "none");
    $('#error').slideDown();
  }
});
