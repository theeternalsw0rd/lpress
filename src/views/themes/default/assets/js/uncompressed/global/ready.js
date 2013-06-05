jQuery(document).ready(function( $ ) {
/*
 * open global/forms.coffee
*/


(function() {
  var $body, $html, $page, css, head, style;

  $html = $('html');

  $body = $('body');

  $page = $(document.getElementById('page'));

  if ($html.hasClass('lt-ie8')) {
    css = '*{noFocusLine: expression(this.hideFocus=true);}';
    head = document.getElementsByTagName('head')[0];
    style = document.createElement('style');
    style.type = 'text/css';
    style.styleSheet.cssText = css;
    head.appendChild(style);
    if ($body.height() > $page.height()) {
      $page.css('height', $body.height());
    }
    $('form').each(function() {
      var $this;

      $this = $(this);
      /* replace input buttons cause ie puts an unremovable black border around them use an anchor instead
      */

      $this.find('input[type=submit]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button submit'>" + ($this.val()) + "</a>").remove();
      });
      $this.find('input[type=reset]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button reset'>" + ($this.val()) + "</a>").remove();
      });
      return $this.find('input[type=button]').each(function() {
        $this = $(this);
        return $this.after("<a href='#' tabindex='" + ($this.attr('tabindex')) + "' class='button'>" + ($this.val()) + "</a>").remove();
      });
    });
    $('label.checkbox').on('mousedown', function(event) {
      return $(this).find('input.checkbox').focus();
    });
    $('a.submit').on('click', function(event) {
      event.preventDefault();
      return $(this).closest('form').submit();
    });
    $('input, textarea').on('focus', function(event) {
      var $this;

      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        $this.next().addClass('focused');
      }
      return $this.addClass('focused');
    });
    $('input, textarea').on('blur', function(event) {
      var $this;

      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        $this.next().removeClass('focused');
      }
      return $this.removeClass('focused');
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

  $(document).on('focus', '.button', function(event) {
    return $(this).addClass('focused-button');
  });

  $(document).on('blur', '.button', function(event) {
    return $(this).removeClass('focused-button');
  });

  if ($html.hasClass('opacity') || $html.hasClass('ie')) {
    $(document).on('mousedown', 'label.checkbox', function(event) {
      return $(this).find('span.faux-checkbox').addClass('active-checkbox');
    });
    $('form').each(function() {
      var $this;

      $this = $(this);
      if ($html.hasClass('lt-ie8') === false) {
        $this.find('input.checkbox').each(function() {
          return $(this).after("<span class='faux-checkbox' id='for-" + this.id + "'>                  <span unselectable='on' class='checkmark'>&#x2713;</span>              </span>");
        });
      }
      return $this.find('input.file').each(function() {
        $this = $(this);
        if ($html.hasClass('ie')) {
          $this.after("<a unselectable='on' tabindex='" + ($this.attr('tabindex')) + "' id='for-" + this.id + "' class='button'>" + ($this.attr('data-label')) + "</a>");
          $this.prop('tabindex', '-1');
        } else {
          $this.after("<span unselectable='on' id='for-" + this.id + "' class='button'>" + ($this.attr('data-label')) + "</span>");
        }
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
      var filename;

      filename = this.files ? this.files[0].name || this.files.item(0).fileName : this.value.replace(/^.*(\\|\/|\:)/, '');
      return document.getElementById('label-' + this.id).innerHTML = filename;
    });
    /* workaround browsers that have two-tab focus on file input
    */

    if (navigator.appName === 'Opera') {
      $(document).on('keydown', '*[tabindex]', function(event) {
        var $tabindexed, $target, current_index, max_index;

        if (event.which === 9) {
          $target = $(event.target);
          $tabindexed = $('*[tabindex]').not('[tabindex="-1"]').sort(function(a, b) {
            var index_a, index_b;

            index_a = parseInt($(a).attr('tabindex'));
            index_b = parseInt($(b).attr('tabindex'));
            return index_a - index_b;
          });
          current_index = $tabindexed.index($target);
          max_index = $tabindexed.length - 1;
          if (event.shiftKey) {
            if (current_index !== 0) {
              event.preventDefault();
              event.stopPropagation();
              /* opera 10 doesn't prevent default, so blur target and set smallest timeout to focus
              */

              $target.blur();
              return setTimeout(function() {
                return $tabindexed.eq(current_index - 1).focus();
              }, 1);
            }
          } else {
            if (current_index !== max_index) {
              event.preventDefault();
              event.stopPropagation();
              /* opera 10 doesn't prevent default, so blur target and set smallest timeout to focus
              */

              $target.blur();
              return setTimeout(function() {
                return $tabindexed.eq(current_index + 1).focus();
              }, 1);
            }
          }
        }
      });
      /* unify browser accessibility experience for opera
      */

      $('input.file').on('keydown', function(event) {
        var $this, _ref;

        if ((_ref = event.which) === 13 || _ref === 32) {
          $this = $(this);
          event.preventDefault();
          event.stopPropagation();
          return $this.click();
        }
      });
    }
    if ($html.hasClass('ie')) {
      /* unify browser accessibility experience for ie
      */

      /* this.id.substring(4) removes the 'for-' from the id
      */

      $('div.upload a.button').on('keydown', function(event) {
        var $this, _ref;

        if ((_ref = event.which) === 13 || _ref === 32) {
          $this = $(this);
          event.preventDefault();
          event.stopPropagation();
          return $this.click();
        }
      });
      /* this.id.substring(4) removes the 'for-' from the id
      */

      $('div.file a.button').on('click', function(event) {
        var $file;

        event.preventDefault();
        $file = $(document.getElementById(this.id.substring(4)));
        $(':focus').blur();
        $file.click();
        return $(this).focus();
      });
    }
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