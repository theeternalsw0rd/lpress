jQuery(document).ready(function( $ ) {(function() {
  $('form').on('click', 'input.faux-file', function(event) {
    var parent;

    event.stopPropagation();
    event.preventDefault();
    return parent = $(this).parent().find('input.file').click();
  });

  $('form').on('click', 'input.file', function(event) {
    return event.stopPropagation();
  });

  $('form').on('change', 'input.file', function(event) {
    return console.log($(this).val());
  });

}).call(this);
});