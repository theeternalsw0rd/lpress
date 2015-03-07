
/*
 * open global/helpers.coffee
 */

(function() {
  var $, $body, $document, $gallery, $html, $page, bottomIsVisible, css, filter, getDialog, getFocusables, getUploader, head, icons, initializeTabindex, list_option_html, multiple_select, nextFocusable, parseURI, pinToBottom, rebuildTabindex, selected_option_html, setLastItem, single_select, style, topIsVisible, ulSlideToggle;

  $ = jQuery;

  $document = $(document);

  $html = $('html');

  $body = $(document.body).removeClass('nojs');

  $page = $(document.getElementById('page'));


  /*
  Thanks css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive
   */

  $.expr[":"].containsNS = $.expr.createPseudo(function(arg) {
    return function(elem) {
      var text;
      text = $(elem).text().substring(0, arg.length).toUpperCase();
      return text === arg.toUpperCase();
    };
  });

  filter = function($list, needle) {
    var $items;
    $items = $list.children().removeClass('filtered');
    if (needle !== '') {
      $items.has(':not(:containsNS("' + needle + '"))').not('.filter').addClass('filtered');
    }
    return rebuildTabindex(getFocusables($document), $(':focus'));
  };

  $(document).on('cbox_open', function() {
    var $focusables, recall_id;
    $focusables = getFocusables($document);
    recall_id = $focusables.index($(this));
    return $body.data('focus-recall-id', recall_id);
  });

  $(document).on('cbox_complete', function() {
    var $focusElement, $focusables;
    $focusables = getFocusables($(document.getElementById('colorbox')));
    $focusElement = $focusables.first();
    return rebuildTabindex($focusables, $focusElement);
  });

  $(document).on('cbox_close', function() {
    var $focusElement, $focusables, colorbox_focus_id;
    colorbox_focus_id = parseInt($body.data('colorbox-focus-id'), 10);
    $focusables = getFocusables($document);
    if ($focusables.length >= colorbox_focus_id) {
      $focusElement = $focusables.eq(colorbox_focus_id);
    } else {
      $focusElement = $focusables.first();
    }
    return rebuildTabindex($focusables, $focusElement);
  });

  nextFocusable = function($current, wraparound) {
    var $focusElement, fallback_index, next_index;
    next_index = parseInt($current.attr('tabindex'), 10) + 1;
    fallback_index = next_index - 2;
    $focusElement = $("*[tabindex='" + next_index + "']");
    if ($focusElement.length === 0) {
      if ((typeof wraparound !== 'undefined' && wraparound) || fallback_index < 1) {
        $focusElement = $("*[tabindex='1']");
      } else {
        $focusElement = $("*[tabindex='" + fallback_index + "']");
      }
    }
    return $focusElement;
  };

  setLastItem = function($root, selector, fallback) {
    var $last;
    fallback = fallback || '';
    $root.find('.last').removeClass('last');
    $last = $root.find(selector).last();
    if ($last.length === 0 && fallback !== '') {
      $last = $root.find(fallback);
    }
    return $last.addClass('last');
  };


  /*
   start icon codes from font awesome
   */

  icons = {
    'fa-check': "&#xf00c;",
    'fa-times': "&#xf00d;",
    'fa-sort': "&#xf0dc;",
    'fa-trash-o': "&#xf014;",
    'fa-times-circle': "&#xf057;",
    'fa-caret-square-o-down': "&#xf150;",
    'fa-caret-square-o-up': "&#xf151;",
    'fa-search': "&#xf002;"
  };


  /*
   end icon codes
   */

  getDialog = function(action, options) {
    var $focusables, colorbox_focus_id, html, url;
    url = options.url.split(':')[1];
    switch (action) {
      case 'delete':
        html = "<div class='dialog'><h2>" + lang_global_forms.delete_item + "</h2><p>" + lang_global_forms.confirm_delete + "</p><div class='dialog-buttons'><a class='button' href='" + url + "'><span class='button-icon fa-check'>" + icons['fa-check'] + "</span><span class='button-label'>" + lang_global_forms.ok + "</span></a><a href='#' class='button cancel'><span class='button-icon fa-times'>" + icons['fa-times'] + "</span><span class='button-label'>" + lang_global_forms.cancel + "</span></a></div>\n</div>";
    }
    $focusables = getFocusables($document);
    colorbox_focus_id = -1;
    $focusables.each(function(index, element) {
      var $element;
      $element = $(element);
      if ($element.attr('href') === url) {
        return colorbox_focus_id = index;
      }
    });
    $body.data('colorbox-focus-id', colorbox_focus_id);
    return $.colorbox({
      'html': html,
      'scrolling': false,
      'closeButton': false
    });
  };

  parseURI = function(uri) {
    var queries, query_string;
    query_string = uri.split('?')[1];
    queries = {};
    query_string.replace(/([^&=]+)=?([^&]*)(?:&+|$)/g, function(match, key, value) {
      return (queries[key] = queries[key] || []).push(value);
    });
    return queries;
  };

  getFocusables = function($element) {
    return $element.find('a[href], input, button, textarea, *[contenteditable="true"]').filter(':not(.disabled)').filter(':visible');
  };

  rebuildTabindex = function($focusables, $focusElement) {
    $('*[tabindex]').attr('tabindex', '-1');
    $focusables.each(function(index, element) {
      var $this;
      $this = $(element);
      return $this.attr('tabindex', index + 1);
    });
    return $focusElement.focus();
  };

  ulSlideToggle = function(event, clickElement) {
    var $item, $this;
    event.preventDefault();
    $this = $(clickElement);
    $item = $this.parent();
    if ($item.hasClass('inactive')) {
      return $this.next().slideDown('fast', function() {
        var $focusElement, $focusables, $root;
        $item.removeClass('inactive').addClass('active');
        $focusables = getFocusables($document);
        $focusElement = getFocusables($(this)).first();
        rebuildTabindex($focusables, $focusElement);
        $root = $(this).closest('ul.select');
        setLastItem($root, 'a.option:visible', 'li.filter');
        if ($root.length > 0) {
          return pinToBottom($root, $root.find('a.label'), $root.find('a.close'), 'right');
        }
      });
    } else {
      return $this.next().slideUp('fast', function() {
        var $focusElement, $focusables;
        $item.removeClass('active').addClass('inactive');
        $focusables = getFocusables($document);
        $focusElement = $this;
        return rebuildTabindex($focusables, $focusElement);
      });
    }
  };

  pinToBottom = function($root, $width_element, $target, position) {
    var $focusElement, $focusables, left, offset, x;
    offset = $width_element.offset();
    left = offset.left;
    if (position === 'right') {
      x = left + $width_element.outerWidth();
    } else {
      x = left - $target.outerWidth();
    }
    $target.css({
      'left': x + 'px'
    });
    if (topIsVisible($root)) {
      $root.addClass('top_is_visible');
    } else {
      $root.removeClass('top_is_visible');
    }
    if (bottomIsVisible($root)) {
      $target.css({
        'position': 'absolute',
        'left': $width_element.outerWidth() + 'px'
      });
    } else {
      $target.css({
        'position': 'fixed',
        'left': left + $width_element.outerWidth() + 'px'
      });
    }
    $focusables = getFocusables($document);
    $focusElement = $(':focus');
    if ($focusElement.length === 0) {
      $focusElement = getFocusables($root).first();
    }
    return rebuildTabindex($focusables, $focusElement);
  };

  topIsVisible = function($target) {
    var $window, height, scroll_top, top, window_height;
    top = $target.offset().top;
    height = $target.outerHeight();
    $window = $(window);
    window_height = $window.height();
    scroll_top = $window.scrollTop();
    return window_height + scroll_top > top && scroll_top < top;
  };

  bottomIsVisible = function($target) {
    var $window, height, scroll_top, top, window_height;
    top = $target.offset().top;
    height = $target.outerHeight();
    $window = $(window);
    window_height = $window.height();
    scroll_top = $window.scrollTop();
    return window_height + scroll_top > height + top;
  };


  /*
   * close global/helpers.coffee
   */


  /*
   * open global/forms.coffee
   */


  /*
   uppercase booleans easier to read, custom code in watcher to change it, not a coffee feature.
   */

  Dropzone.autoDiscover = false;

  $document.on('click', 'ul.select > li > a', function(event) {
    return ulSlideToggle(event, this);
  });

  $document.on('scroll', function(event) {
    return $('a.close').each(function() {
      var $focusElement, $focusables, $root, $this, $width_element;
      $this = $(this);
      $root = $(this).closest('ul.select');
      if ($root.length > 0) {
        $width_element = $root.find('a.label');
        if (topIsVisible($root)) {
          $root.addClass('top_is_visible');
        } else {
          $root.removeClass('top_is_visible');
        }
        if (bottomIsVisible($root)) {
          $this.css({
            'position': 'absolute',
            'left': $width_element.outerWidth() + 'px'
          });
        } else {
          $this.css({
            'position': 'fixed',
            'left': $root.offset().left + $width_element.outerWidth() + 'px'
          });
        }
        $focusables = getFocusables($document);
        $focusElement = $(':focus');
        if ($focusElement.length === 0) {
          $focusElement = getFocusables($root).first();
        }
        return rebuildTabindex($focusables, $focusElement);
      }
    });
  });

  $document.on('click', 'a.cancel', function(event) {
    var $focusElement, $focusables, colorbox_focus_id;
    event.preventDefault();
    $.colorbox.remove();
    $focusables = getFocusables($document);
    colorbox_focus_id = parseInt($body.data('colorbox-focus-id'), 10);
    if ($focusables.length >= colorbox_focus_id) {
      $focusElement = $focusables.eq(colorbox_focus_id);
    } else {
      $focusElement = $focusables.first();
    }
    return rebuildTabindex($focusables, $focusElement);
  });

  $document.on('click', 'a.delete', function(event) {
    var options;
    event.preventDefault();
    options = {
      'url': this.href
    };
    return getDialog('delete', options);
  });

  getUploader = function(id, path, target_id, attachment_type) {
    return $("<div id='" + id + "' class='colorbox'><div id='" + id + "-tabs' class='tabs'><ul class='etabs clear-fix'><li class='tab'><a href='#" + id + "-new'>" + lang_global_forms["new"] + "</a></li><li class='tab'><a href='#" + id + "-existing'>" + lang_global_forms.existing + "</a></li></ul><ul id='" + id + "-new' class='tab-contents dropzone files'></ul><div id='" + id + "-existing' class='tab-contents' data-url='" + path + "' data-attachment_type='" + attachment_type + "' data-target_id='" + target_id + "'></div></div>\n</div>");
  };

  selected_option_html = function(id, $option, selected) {
    var attrs, unselect;
    if (selected == null) {
      selected = -1;
    }
    unselect = "<span class='icon'>" + icons['fa-times'] + "</span>";
    attrs = " data-value='" + ($option.val()) + "'";
    attrs += selected === -1 ? " class='unselect button unselected'" : " class='unselect button'";
    return "<a href='#'" + attrs + ">" + ($option.text()) + unselect + "</a>";
  };

  list_option_html = function(id, $option, selected) {
    var attrs;
    if (selected == null) {
      selected = -1;
    }
    attrs = selected > -1 ? " class='selected'" : "";
    return "<li" + attrs + "><a class='option' href='#" + id + "' data-value='" + ($option.val()) + "'>" + ($option.html()) + "</a></li>";
  };

  multiple_select = function($select, $options, label) {
    var $focusElement, $focusables, $list, $selected, $selected_list, html, selected_ids;
    $selected = $select.find('option[selected="selected"]');
    $selected_list = $("<div class='selected'></div>");
    selected_ids = new Array();
    $selected.each(function() {
      return selected_ids.push(this.value);
    });
    if ($options.length !== $selected.length) {
      html = "<ul class='multiselect select'><li class='inactive'>";
      html += "<a href='#' class='button label'>" + label + "<span class='icon'>" + icons['fa-sort'] + "</span></a>";
      html += "<ul class='options'>";
      html += "<li class='filter'><div class='clearfix'><span class='icon'>" + icons['fa-search'] + "</span><span class='editable' contentEditable='true'></span></div></li>";
      $options.each(function() {
        var $option, id, selected;
        $option = $(this);
        id = $select.attr('id');
        selected = $.inArray(this.value, selected_ids);
        html += list_option_html(id, $option, selected);
        return $selected_list.append(selected_option_html(id, $option, selected));
      });
      html += "</ul><a href='#' class='close icon'>" + icons['fa-times-circle'] + "</a></li></ul>";
    } else {
      $select.addClass('disabled');
      html = "<ul class='multiselect select'><li><a class='label disabled'>" + label + " " + current + "<span class='icon disabled'>" + icons['fa-sort'] + "</span></a></li></ul>";
    }
    $select.before($selected_list);
    $list = $(html);
    $select.after($list);
    $focusables = getFocusables($document);
    $focusElement = $(':focus');
    if ($focusElement.length === 0) {
      $focusElement = $focusables.first();
    }
    return rebuildTabindex($focusables, $focusElement);
  };

  single_select = function($select, $options, label) {
    var $focusElement, $focusables, $list, current, html;
    current = $select.find('option[selected="selected"]').html();
    if ($options.length > 1) {
      html = "<ul class='singleselect select'><li class='inactive'>";
      html += "<a href='#' class='label'>" + label + "<span class='current'> " + current + "</span><span class='icon'>" + icons['fa-sort'] + "</span></a>";
      html += "<ul class='options'>";
      $options.each(function() {
        var $option;
        $option = $(this);
        return html += option_html($select.attr('id'), $option);
      });
      html += "</ul><a href='#'>Close Select</a></li></ul>";
    } else {
      $select.addClass('disabled');
      html = "<ul class='singleselect select'><li><a class='label disabled'>" + label + " " + current + "<span class='icon disabled'>" + icons['fa-sort'] + "</span></a></li></ul>";
    }
    $list = $(html);
    $select.after($list);
    $focusables = getFocusables($document);
    $focusElement = $(':focus');
    if ($focusElement.length === 0) {
      $focusElement = $focusables.first();
    }
    return rebuildTabindex($focusables, $focusElement);
  };

  $document.on('click', 'ul.select a.close', function(event) {
    event.preventDefault();
    return $(this).closest('ul.select').find('a.label').click();
  });

  $document.on('keyup', 'li.filter span.editable', function(event) {
    var $list, $this;
    $this = $(this);
    filter($this.closest('ul, ol'), $this.text());
    $list = $this.closest('ul');
    return setLastItem($list, 'a.option:visible', 'li.filter');
  });

  $document.on('keypress', 'li.filter span.editable', function(event) {
    return event.which !== 13;
  });

  $document.on('keyup', 'ul.select', function(event) {
    var $next, $previous, $this, tabindex;
    switch (event.which) {
      case 38:
        $this = $(this);
        tabindex = parseInt($this.find(':focus').attr('tabindex'), 10) - 1;
        $previous = $this.find("*[tabindex='" + tabindex + "']").not('a.close');
        if ($previous.length === 0) {
          $previous = $this.find('li').last().find("*[tabindex]");
        }
        return $previous.focus();
      case 40:
        $this = $(this);
        tabindex = parseInt($this.find(':focus').attr('tabindex'), 10) + 1;
        $next = $this.find("*[tabindex='" + tabindex + "']").not('a.close');
        if ($next.length === 0) {
          $next = $this.find('li').first().find("*[tabindex]").first();
        }
        return $next.focus();
    }
  });

  $document.on('click', 'div.selected a.unselect', function(event) {
    var $focusElement, $list, $select, $this, value;
    event.preventDefault();
    $this = $(this).focus();
    $this.addClass('unselected');
    value = $this.data('value');
    $select = $this.closest('div.selected').next();
    $list = $select.next();
    $list.find("a[data-value='" + value + "']").parent().removeClass('selected');
    setLastItem($list, 'a.option:visible', 'li.filter');
    $select.find("option[value='" + value + "']").prop('selected', false).removeAttr('selected');
    $focusElement = nextFocusable($this);
    return rebuildTabindex(getFocusables($document), $focusElement);
  });

  $document.on('click', 'ul.multiselect a.option', function(event) {
    var $focusElement, $focusables, $item, $list, $select, $selected, $this, next_tabindex, value;
    event.preventDefault();
    $this = $(this);
    $item = $this.closest('li');
    next_tabindex = parseInt($this.attr('tabindex'), 10) + 1;
    $item.addClass('selected');
    $list = $item.closest('ul.select');
    $focusElement = $("*[tabindex='" + next_tabindex + "'");
    if ($focusElement.length === 0 || !$focusElement.hasClass('option')) {
      $focusElement = $("*[tabindex='" + (next_tabindex - 2) + "']");
      if ($focusElement.length === 0) {
        $focusElement = $list.find("*[contentEditable='true']");
      }
    }
    $focusables = getFocusables($document);
    rebuildTabindex($focusables, $focusElement);
    value = $this.data('value');
    setLastItem($list, 'a.option:visible', 'li.filter');
    $select = $list.prev();
    $select.find("option[value='" + value + "']").prop('selected', true).attr('selected', 'selected');
    $selected = $select.prev();
    return $selected.find("a[data-value='" + value + "']").removeClass('unselected');
  });

  $document.on('click', 'ul.singleselect a.option', function(event) {
    var $label, $target, $this;
    event.preventDefault();
    $this = $(this);
    $target = $($this.attr('href'));
    $target.val($this.data('value'));
    $target.find('option[selected="selected"]').removeAttr('selected');
    $label = $this.closest('ul').prev();
    $label.find('span.current').html(' ' + $this.html());
    return $label.click();
  });

  $('select').each(function() {
    var $label, $options, $select, label;
    $select = $(this);
    $label = $select.prev().hide();
    label = $label.html();
    $options = $select.find('option');
    if ($options.length < 1) {
      return;
    }
    if ($select.attr('multiple') === 'multiple') {
      return multiple_select($select, $options, label);
    } else {
      return single_select($select, $options, label);
    }
  });

  $document.on('click', 'ul.etabs', function(event) {
    var $content_box, $existing_files, $target, attachment_type, id, skip, target, url;
    target = event.target;
    if (target.tagName === 'A') {
      skip = false;
      $(this).find("[data-href]").each(function() {
        var $this;
        if (this !== target) {
          $this = $(this);
          return $this.attr('href', $this.data('href')).removeAttr('data-href');
        } else {
          return skip = true;
        }
      });
      if (!skip) {
        $target = $(target);
        id = $target.attr('href');
        $target.attr('data-href', id).removeAttr('href');
        $content_box = $(id);
        attachment_type = $content_box.data('attachment_type');
        url = $content_box.data('url');
        if (/existing/.test(id)) {
          $existing_files = $(document.getElementById("existing-files-" + id));
          if ($existing_files.length === 0) {
            return $.ajax({
              url: url + '.json',
              dataType: 'json',
              success: function(data) {
                $existing_files = $("<ul id='existing-files-" + id + "' class='files'></ul>");
                $.each(data.records, function(index, record) {
                  var alt, target_id;
                  target_id = $content_box.data('target_id');
                  alt = record.label;
                  $.each(record.values, function(index, value) {
                    if (value.field.slug === 'file-description') {
                      alt = value.current_revision.contents;
                      return false;
                    }
                  });
                  if (attachment_type === 'images') {
                    return $existing_files.append("<li><a class='file_select' title='" + record.label + "' href='" + url + "/" + record.slug + "' data-record_id='" + record.id + "' data-target_id='" + target_id + "'><img src='" + url + "/" + record.slug + "' alt='" + alt + "' /><span class='caption'>" + record.label + "</span></a>\n</li>");
                  }
                });
                return $content_box.append($existing_files);
              },
              error: function(data) {
                var json;
                if (data.status === 404) {
                  json = $.parseJSON(data.responseText);
                  return $id.html("<h1>HttpError: 404</h1><div class='error'>" + json.reason + "</div>");
                }
              }
            });
          }
        }
      }
    }
  });

  $document.on('click', 'a.file_select', function(event) {
    var $target, $this;
    event.preventDefault();
    $this = $(this);
    $target = $(document.getElementById($this.data('target_id')));
    $target.val($this.data('record_id'));
    return $.colorbox.close();
  });

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

      /* replace input buttons cause ie puts an unremovable black border around them use an anchor instead */
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
    $document.on('mousedown', 'label.checkbox', function(event) {
      return $(this).find('input.checkbox').focus();
    });
    $document.on('click', 'a.submit', function(event) {
      event.preventDefault();
      return $(this).closest('form').submit();
    });
    $document.on('focus', 'input, textarea', function(event) {
      var $this;
      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        $this.next().addClass('focused');
      }
      return $this.addClass('focused');
    });
    $document.on('blur', 'input, textarea', function(event) {
      var $this;
      $this = $(this);
      if ($this.hasClass('file') || $this.hasClass('checkbox')) {
        $this.next().removeClass('focused');
      }
      return $this.removeClass('focused');
    });
    $document.on('keydown', 'input[type=file]', function(event) {
      var $this, ref;
      $this = $(this);
      if ((ref = event.which) === 13 || ref === 32) {
        event.preventDefault();
        return $this.click();
      }
    });
    $document.on('keydown', 'input[type=text], input[type=password]', function(event) {
      var $this;
      $this = $(this);
      if (event.which === 13) {
        return $this.closest('form').submit();
      }
    });
  }

  $document.on('mouseup', function(event) {
    $('.active-button').removeClass('active-button');
    return $('.active-checkbox').removeClass('active-checkbox');
  });

  $document.on('mousedown', '.button, button', function(event) {
    return $(this).addClass('active-button');
  });

  $document.on('focus', '.button, button', function(event) {
    return $(this).addClass('focused-button');
  });

  $document.on('blur', '.button, button', function(event) {
    return $(this).removeClass('focused-button');
  });

  if ($html.hasClass('opacity') || $html.hasClass('ie')) {
    $document.on('mousedown', 'label.checkbox', function(event) {
      return $(this).find('span.faux-checkbox').addClass('active-checkbox');
    });
    $('form').each(function() {
      var $this;
      $this = $(this);
      if ($html.hasClass('lt-ie8') === false) {
        $this.find('input.checkbox').each(function() {
          return $(this).after("<span class='faux-checkbox' id='for-" + this.id + "'> <span unselectable='on' class='checkmark'>&#x2713;</span> </span>");
        });
      }
      return $this.find('a.file').each(function() {
        var $first_tab, $tabs, $uploader, id, myDropzone, previewTemplate, record, target_id, token;
        $this = $(this);
        id = this.href.split('#')[1];
        record = $this.data('prefix') + '/records/create?type=' + id;
        token = $this.data('token');
        target_id = $this.data('target_id');
        $uploader = getUploader(id, $this.data('path'), target_id, $this.data('attachment_type'));
        $('body').append($uploader);
        previewTemplate = "<li><a class='dz-preview dz-file-preview'><img data-dz-thumbnail /><div class='dz-progress'><span class='dz-upload' data-dz-uploadprogress></span></div><span class='caption'>" + lang_global_forms.uploading + "</span></a>\n</li>";
        myDropzone = new Dropzone("#" + id + "-new", {
          url: $this.data('url'),
          thumbnailWidth: 500,
          thumbnailHeight: 500,
          previewTemplate: previewTemplate,
          dictDefaultMessage: lang_global_forms.dropzone
        });
        myDropzone.on('sending', function(file, xhr, formData) {
          return formData.append('_token', token);
        });
        myDropzone.on('success', function(file, response) {
          var $anchor, success_mark, uri;
          uri = response.uri;
          record = response.record;
          success_mark = "<div class='dz-success-mark'><span class='button-icon'>" + icons['fa-check'] + "</span></div>";
          $anchor = $(file.previewElement).children().first();
          $anchor.addClass('dz-success').addClass('file_select');
          $anchor.attr('title', record.label).attr('href', uri + "/" + record.slug);
          $anchor.data('record_id', record.id).data('target_id', target_id);
          $anchor.find('img').attr('alt', record.label);
          return $anchor.find('.caption').first().html(record.label).after(success_mark);
        });
        myDropzone.on('error', function(file, error_message) {
          var $anchor, $error_message, height;
          if (typeof error_message === 'object') {
            error_message = error_message.error;
          } else {
            $error_message = $(error_message);
            error_message = $error_message.find('h1').first().html();
          }
          $anchor = $(file.previewElement).children().first();
          height = 0;
          $anchor.children().each(function() {
            return height += $(this).outerHeight(true);
          });
          $error_message = $("<div class='dz-error-message' style='height:" + height + "px'><span>" + error_message + "</span></div>");
          $anchor.removeAttr('href').addClass('dz-error');
          $anchor.html("").append($error_message);
          return $anchor.on('click', function(event) {
            return event.preventDefault();
          });
        });
        $tabs = $(document.getElementById(id + '-tabs'));
        $tabs.easytabs({
          updateHash: false
        });
        $first_tab = $tabs.find('ul.etabs a').first();
        $first_tab.attr('data-href', $first_tab.attr('href')).removeAttr('href');
        return $this.colorbox({
          inline: true,
          fixed: true,
          width: '50%',
          height: '80%',
          scrolling: false,
          onComplete: function() {
            var $colorbox, tab_bar_height;
            $('body').css({
              'overflow': 'hidden'
            });
            $colorbox = $(document.getElementById('cboxLoadedContent'));
            $colorbox.find('.colorbox').css({
              'height': $colorbox.height() + 'px'
            });
            $colorbox.find('.dropzone').first().focus();
            $colorbox.get(0).scrollTop = 0;
            tab_bar_height = $colorbox.find('.etabs').first().outerHeight();
            return $colorbox.find('.tab-contents').each(function() {
              var spacing;
              $this = $(this);
              spacing = $this.outerHeight() - $this.height();
              return $(this).height($colorbox.height() - tab_bar_height - spacing);
            });
          },
          onCleanup: function() {
            id = $this.closest('.colorbox').attr('id');
            return $("a[href=#" + id + "-new]").click();
          },
          onClosed: function() {
            return $('body').css({
              'overflow': 'auto'
            });
          }
        });
      });
    });

    /* firefox doesn't support focus psuedo-class on input type file */
    $document.on('focus', 'input.file', function(event) {
      return $(this).next().addClass('focused-button');
    });
    $document.on('blur', 'input.file', function(event) {
      return $(this).next().removeClass('focused-button');
    });

    /* workaround browsers that have two-tab focus on file input */
    if (navigator.appName === 'Opera') {
      $document.on('keydown', '*[tabindex]', function(event) {
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

              /* opera 10 doesn't prevent default, so blur target and set smallest timeout to focus */
              $target.blur();
              return setTimeout(function() {
                return $tabindexed.eq(current_index - 1).focus();
              }, 1);
            }
          } else {
            if (current_index !== max_index) {
              event.preventDefault();
              event.stopPropagation();

              /* opera 10 doesn't prevent default, so blur target and set smallest timeout to focus */
              $target.blur();
              return setTimeout(function() {
                return $tabindexed.eq(current_index + 1).focus();
              }, 1);
            }
          }
        }
      });

      /* unify browser accessibility experience for opera */
      $document.on('keydown', 'input.file', function(event) {
        var $this, ref;
        if ((ref = event.which) === 13 || ref === 32) {
          $this = $(this);
          event.preventDefault();
          event.stopPropagation();
          return $this.click();
        }
      });
    }
    if ($html.hasClass('ie')) {

      /* unify browser accessibility experience for ie */

      /* this.id.substring(4) removes the 'for-' from the id */
      $document.on('keydown', 'div.upload a.button', function(event) {
        var $this, ref;
        if ((ref = event.which) === 13 || ref === 32) {
          $this = $(this);
          event.preventDefault();
          event.stopPropagation();
          return $this.click();
        }
      });

      /* this.id.substring(4) removes the 'for-' from the id */
      $document.on('click', 'div.upload a.button', function(event) {
        var $file;
        event.preventDefault();
        $file = $(document.getElementById(this.id.substring(4)));
        $(':focus').blur();
        $file.click();
        return $(this).focus();
      });
    }
    $document.on('click', 'label.checkbox', function(event) {
      var $this;
      event.preventDefault();
      $this = $(this);
      return $('#' + $this.attr('for')).click();
    });
    $document.on('click', 'input.checkbox', function(event) {
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


  /*
   * open global/gallery.coffee
   */

  $gallery = $(document.getElementById('gallery'));

  $gallery.find('.gallery').colorbox({
    rel: 'gallery',
    photo: true,
    maxHeight: '90%',
    maxWidth: '90%'
  });


  /*
   * close global/gallery.coffee
   */


  /*
   * open global/tabular.coffee
   */

  $(document).on('click', 'ul.tabular > li > a', function(event) {
    var $icon, $this;
    ulSlideToggle(event, this);
    $this = $(this);
    $icon = $(this).find('span.icon');
    if ($this.parent().hasClass('active')) {
      return $icon.html(icons['fa-caret-square-o-down']);
    } else {
      return $icon.html(icons['fa-caret-square-o-up']);
    }
  });


  /*
   * close global/tabular.coffee
   */


  /*
   * open global/flexnav.coffee
   */


  /*
    FlexNav.js 1.1
  
    Created by Jason Weaver http://jasonweaver.name
    Released under http://unlicense.org/
  
  //
   */

  $.fn.flexNav = function(options) {
    var $nav, breakpoint, flag, resetMenu, resizer, selector, settings, showMenu;
    settings = $.extend({
      'animationSpeed': 250,
      'transitionOpacity': true,
      'buttonSelector': '.menu-button',
      'hoverIntent': false,
      'hoverIntentTimeout': 150
    }, options);
    $nav = $(this);
    flag = false;
    $nav.addClass('with-js');
    if (settings.transitionOpacity === true) {
      $nav.addClass('opacity');
    }
    $nav.find("li").each(function() {
      if ($(this).has("ul").length) {
        return $(this).addClass("item-with-ul").find("ul").hide();
      }
    });
    if ($nav.data('breakpoint')) {
      breakpoint = $nav.data('breakpoint');
    }
    showMenu = function() {
      if ($nav.hasClass('lg-screen') === true) {
        if (settings.transitionOpacity === true) {
          return $(this).find('>ul').addClass('show').stop(true, true).animate({
            height: ["toggle", "swing"],
            opacity: "toggle"
          }, settings.animationSpeed);
        } else {
          return $(this).find('>ul').addClass('show').stop(true, true).animate({
            height: ["toggle", "swing"]
          }, settings.animationSpeed);
        }
      }
    };
    resetMenu = function() {
      if ($nav.hasClass('lg-screen') === true && $(this).find('>ul').hasClass('show') === true) {
        if (settings.transitionOpacity === true) {
          return $(this).find('>ul').removeClass('show').stop(true, true).animate({
            height: ["toggle", "swing"],
            opacity: "toggle"
          }, settings.animationSpeed);
        } else {
          return $(this).find('>ul').removeClass('show').stop(true, true).animate({
            height: ["toggle", "swing"]
          }, settings.animationSpeed);
        }
      }
    };
    resizer = function() {
      if ($(window).width() <= breakpoint) {
        $nav.removeClass("lg-screen").addClass("sm-screen");
        return $('.one-page li a').on('click', function() {
          return $nav.removeClass('show');
        });
      } else if ($(window).width() > breakpoint) {
        $nav.removeClass("sm-screen").addClass("lg-screen");
        $nav.removeClass('show');
        if (settings.hoverIntent === true) {
          return $('.item-with-ul').hoverIntent({
            over: showMenu,
            out: resetMenu,
            timeout: settings.hoverIntentTimeout
          });
        } else if (settings.hoverIntent === false) {
          return $('.item-with-ul').on('mouseenter', showMenu).on('mouseleave', resetMenu);
        }
      }
    };
    $(settings['buttonSelector']).data('navEl', $nav);
    selector = '.item-with-ul, ' + settings['buttonSelector'];
    $(selector).append('<span class="touch-button"><i class="navicon">&#9660;</i></span>');
    selector = settings['buttonSelector'] + ', ' + settings['buttonSelector'] + ' .touch-button';
    $(selector).on('touchstart click', function(e) {
      var $btnParent, $thisNav, bs;
      e.preventDefault();
      e.stopPropagation();
      bs = settings['buttonSelector'];
      $btnParent = $(this).is(bs) ? $(this) : $(this).parent(bs);
      $thisNav = $btnParent.data('navEl');
      if (flag === false) {
        flag = true;
        setTimeout(function() {
          return flag = false;
        }, 301);
        return $thisNav.toggleClass('show');
      }
    });
    $('.touch-button').on('touchstart click', function(e) {
      var $sub, $touchButton;
      e.preventDefault();
      e.stopPropagation();
      $sub = $(this).parent('.item-with-ul').find('>ul');
      $touchButton = $(this).parent('.item-with-ul').find('>span.touch-button');
      if (flag === false) {
        flag = true;
        setTimeout(function() {
          return flag = false;
        }, 301);
        if ($nav.hasClass('lg-screen') === true) {
          $(this).parent('.item-with-ul').siblings().find('ul.show').removeClass('show').hide();
        }
        if ($sub.hasClass('show') === true) {
          $sub.removeClass('show').slideUp(settings.animationSpeed);
          return $touchButton.removeClass('active');
        } else if ($sub.hasClass('show') === false) {
          $sub.addClass('show').slideDown(settings.animationSpeed);
          return $touchButton.addClass('active');
        }
      }
    });
    $nav.find('.item-with-ul *').focus(function() {
      $(this).parent('.item-with-ul').parent().find(".open").not(this).removeClass("open").hide();
      return $(this).parent('.item-with-ul').find('>ul').addClass("open").show();
    });
    resizer();
    return $(window).on('resize', resizer);
  };

  $(".flexnav").flexNav();


  /*
   * close global/flexnav.coffee
   */


  /*
   * open global/postsetup.coffee
   */

  initializeTabindex = (function() {
    var $focusElement, $focusables;
    $focusables = getFocusables($(document));
    $focusElement = $focusables.first();
    rebuildTabindex($focusables, $focusElement);
    return $('select').attr('tabindex', '-1');
  })();


  /*
   * close global/postsetup.coffee
   */

}).call(this);
