jQuery(document).ready(function( $ ) {
/*
 * open global/forms.coffee
*/


(function() {
  var $focusables, $html, css, focusables_max_index, head, style;

  $focusables = $(':focusable');

  focusables_max_index = $focusables.length - 1;

  $html = $('html');

  if ($html.hasClass('lt-ie8')) {
    css = '*{noFocusLine: expression(this.hideFocus=true);}';
    head = document.getElementsByTagName('head')[0];
    style = document.createElement('style');
    style.type = 'text/css';
    style.styleSheet.cssText = css;
    head.appendChild(style);
    $('form').each(function() {
      var $this;

      $this = $(this);
      /* replace input buttons cause ie puts a unremovable black border around them use an anchor instead
      */

      $this.find('input[type=submit]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button submit'>" + ($this.val()) + "</a>").remove();
      });
      $this.find('input[type=reset]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button reset'>" + ($this.val()) + "</a>").remove();
      });
      $this.find('input[type=button]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button'>" + ($this.val()) + "</a>").remove();
      });
      return $focusables = $(':focusable');
    });
    $('label.checkbox').on('mousedown', function(event) {
      return $(this).find('input.checkbox').focus();
    });
    $('a.submit').on('click', function(event) {
      event.preventDefault();
      return $(this).closest('form').submit();
    });
    $('input, textarea, a.button').on('focus', function(event) {
      var $this;

      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        return $this.next().addClass('focused');
      } else {
        return $this.addClass('focused');
      }
    });
    $('input, textarea, a.button').on('blur', function(event) {
      var $this;

      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        return $this.next().removeClass('focused');
      } else {
        return $this.removeClass('focused');
      }
    });
    $('input[type=file]').on('keydown', function(event) {
      var $this, _ref;

      $this = $(this);
      if ((_ref = event.which) === 13 || _ref === 32) {
        event.preventDefault();
        return $this.click();
      }
    });
    $('input[type=text], input[type=password]').on('keydown', function(event) {
      var $this;

      $this = $(this);
      if (event.which === 13) {
        return $this.closest('form').submit();
      }
    });
  }

  $(document).on('mouseup', function(event) {
    $('.active-button').removeClass('active-button');
    return $('.active-checkbox').removeClass('active-checkbox');
  });

  $(document).on('mousedown', '.button', function(event) {
    return $(this).addClass('active-button');
  });

  if ($html.hasClass('opacity') || $html.hasClass('ie')) {
    $(document).on('mousedown', 'label.checkbox', function(event) {
      return $(this).find('span.faux-checkbox').addClass('active-checkbox');
    });
    $('form').each(function() {
      var $this;

      $this = $(this);
      if ($html.hasClass('lt-ie7') === false) {
        $this.find('input.checkbox').each(function() {
          return $(this).after("<span class='faux-checkbox' id='for-" + this.id + "'>                  <span unselectable='on' class='checkmark'>&#x2713;</span>              </span>");
        });
      }
      return $this.find('input.file').each(function() {
        $this = $(this);
        $this.after("<span unselectable='on' id='for-" + this.id + "' class='button'>" + ($this.attr('data-label')) + "</span>");
        return $this.parent().after("<p class='file'>File to be uploaded: <span id='label-" + this.id + "'>none</span></p>");
      });
    });
    /* firefox doesn't support focus psuedo-class on input type file
    */

    $('input.file').on('focus', function(event) {
      return $(this).next().addClass('focused-button');
    });
    $('input.file').on('blur', function(event) {
      return $(this).next().removeClass('focused-button');
    });
    $('input.file').on('change', function(event) {
      var $this;

      return $this = $(this);
    });
    /* workaround browsers that have two-tab focus on file input
    */

    $('form').on('keydown', function(event) {
      var current_index, focusable_max_index, next_index;

      $focusables = $(':focusable');
      focusable_max_index = $focusables.length - 1;
      if (event.which === 9) {
        event.preventDefault();
        event.stopPropagation();
        current_index = $focusables.index(event.target);
        if (event.shiftKey) {
          next_index = current_index === 0 ? focusables_max_index : current_index - 1;
        } else {
          next_index = current_index === focusables_max_index ? 0 : current_index + 1;
        }
        return $focusables.eq(next_index).focus();
      }
    });
    /* this.id.substring(4) removes the 'for-' from the id
    */

    $('.ie div.file span.button').on('click', function(event) {
      var $file;

      event.preventDefault();
      $file = $(document.getElementById(this.id.substring(4)));
      return $file.click();
    });
    $('input.file').on('keydown', function(event) {
      var _ref;

      if ((_ref = event.which) === 13 || _ref === 32) {
        event.preventDefault();
        return $(this).click();
      }
    });
    $('label.checkbox').on('click', function(event) {
      var $this;

      event.preventDefault();
      $this = $(this);
      return $('#' + $this.attr('for')).click();
    });
    $('input.checkbox').on('click', function(event) {
      var $this;

      event.stopPropagation();
      $this = $(this);
      if ($this.is(':checked') === true) {
        return $this.parent().addClass('checked');
      } else {
        return $this.parent().removeClass('checked');
      }
    });
  }

  /*
   * close global/forms.coffee
  */


}).call(this);

});