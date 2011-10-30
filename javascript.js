$(document).ready(function() {
  $('.flash').css("display", "none");
  if ($('#notice').length > 0) {
    $('#info').css("display", "none");
    $('#notice').slideDown();
  }
  if ($('#error').length > 0) {
    $('#info').css("display", "none");
    $('#error').slideDown();
  }
});
