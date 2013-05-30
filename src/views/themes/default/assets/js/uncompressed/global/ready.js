jQuery(document).ready(function( $ ) {
(function() {
  if (msie === true) {
    $('html').addClass('ie');
  }

  $(document).on('mousedown', 'input.button', function(event) {
    return $(this).addClass('active-button');
  });

  $(document).on('mouseup', function(event) {
    return $('.active-button').removeClass('active-button');
  });

  $('.opacity form').each(function() {
    return $(this).find('input.file').attr('tabindex', '-1');
  });

  $('.opacity form').on('focus', 'input.file', function(event) {
    var $this;

    $this = $(this);
    if ($.isEmptyObject(event.originalEvent) === false) {
      return $this.parent().find('input.faux-file').focus();
    } else {
      return setTimeout(function() {
        return $this.parent().find('input.faux-file').focus();
      }, 300);
    }
  });

  $('.opacity form').on('click', 'input.faux-file', function(event) {
    var $this;

    $this = $(this);
    event.preventDefault();
    $(this).focus();
    return $(this).parent().find('input.file').trigger('focus', jQuery.Event('focus')).click();
  });

  $('.opacity form').on('keydown', 'input.faux-file', function(event) {
    var key;

    key = event.which;
    switch (key) {
      case 13:
      case 32:
        event.preventDefault();
        return $(this).click();
    }
  });

  $('.opacity form').on('change', 'input.file', function(event) {
    return console.log($(this).val());
  });

  $('.opacity form').on('click', 'label.checkbox', function(event) {
    var $this;

    event.preventDefault();
    $this = $(this);
    return $('#' + $this.attr('for')).click();
  });

  $('.opacity form').on('click', 'input.real-checkbox', function(event) {
    var $this;

    event.stopPropagation();
    $this = $(this);
    if ($this.is(':checked') === true) {
      return $this.parent().addClass('checked');
    } else {
      return $this.parent().removeClass('checked');
    }
  });

}).call(this);

});