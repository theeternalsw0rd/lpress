# package:global/ready
###
 * open global/helpers.coffee
###

$ = jQuery
$document = $(document)
###
Thanks css-tricks.com/snippets/jquery/make-jquery-contains-case-insensitive
###
$.expr[":"].containsNS = $.expr.createPseudo(
  (arg) ->
    (elem) ->
      text = $(elem).text().substring(0, arg.length).toUpperCase()
      return text == arg.toUpperCase()
    #return
  #return
)
$('body').removeClass('nojs')
filter = ($list, needle) ->
  $items = $list.children().removeClass('filtered')
  if needle isnt ''
    $items.has(':not(:containsNS("' + needle + '"))').not('.filter').addClass('filtered')
  rebuildTabindex(getFocusables($document), $(':focus'))
  #endif
#return
nextFocusable = () ->
  $currentFocusables = getFocusables($document)
  focusIndex = $currentFocusables.index(':focus')
  $focusElement = $currentFocusables.get(++focusIndex)
  return $focusElement
#return
setLastItem = ($root, selector, fallback) ->
  fallback = fallback || '';
  $root.find('.last').removeClass('last')
  $last = $root.find(selector).last()
  if $last.length == 0 && fallback != ''
    $last = $root.find(fallback)
  #endif
  $last.addClass('last')
#return
###
 start icon codes from font awesome
###
icons = {
  'fa-check': "&#xf00c;"
  'fa-times': "&#xf00d;"
  'fa-sort': "&#xf0dc;"
  'fa-trash-o': "&#xf014;"
  'fa-times-circle': "&#xf057;"
  'fa-caret-square-o-down': "&#xf150;"
  'fa-caret-square-o-up': "&#xf151;"
  'fa-search': "&#xf002;"
}
###
 end icon codes
###

parseURI = (uri) ->
  query_string = uri.split('?')[1]
  queries = {}
  query_string.replace(/([^&=]+)=?([^&]*)(?:&+|$)/g, (match, key, value) ->
    (queries[key] = queries[key] || []).push(value)
  )
  return queries
#return

getFocusables = ($element) ->
  return $element.find('a[href], input, button, textarea, *[contenteditable="true"]').filter(':not(.disabled)').filter(':visible')
#return

rebuildTabindex = ($focusables, $focusElement) ->
  $('*[tabindex]').attr('tabindex', '-1')
  $focusables.each(
    (index, element) ->
      $this = $(element)
      $this.attr('tabindex', index + 1)
    #return
  )
  $focusElement.focus()
#return

ulSlideToggle = (event, clickElement) ->
  event.preventDefault()
  $this = $(clickElement)
  $item = $this.parent()
  if $item.hasClass('inactive')
    $this.next().slideDown(
      'fast'
      ->
        $item.removeClass('inactive').addClass('active')
        $focusables = getFocusables($document)
        $focusElement = getFocusables($(this)).first()
        rebuildTabindex($focusables, $focusElement)
        $root = $(this).closest('ul.select')
        setLastItem($root, 'a.option:visible', 'li.filter')
        if $root.length > 0
          pinToBottom($root, $root.find('a.label'), $root.find('a.close'), 'right')
      #return
    )
  else
    $this.next().slideUp(
      'fast'
      ->
        $item.removeClass('active').addClass('inactive')
        $focusables = getFocusables($document)
        $focusElement = $this
        rebuildTabindex($focusables, $focusElement)
      #return
    )
  #endif
#return
pinToBottom = ($root, $width_element, $target, position) ->
  offset = $width_element.offset()
  left = offset.left
  if position is 'right'
    x = left + $width_element.outerWidth()
  else
    x = left - $target.outerWidth()
  $target.css({'left': x + 'px'})
  if topIsVisible($root)
    $root.addClass('top_is_visible')
  else
    $root.removeClass('top_is_visible')
  if bottomIsVisible($root)
    $target.css({'position': 'absolute', 'left': $width_element.outerWidth() + 'px'})
  else
    $target.css({'position': 'fixed', 'left': left + $width_element.outerWidth() + 'px'})
  #endif
  $focusables = getFocusables($document)
  $focusElement = $(':focus')
  if $focusElement.length == 0
    $focusElement = getFocusables($root).first()
  #endif
  rebuildTabindex($focusables, $focusElement)
#return
topIsVisible = ($target) ->
  top = $target.offset().top
  height = $target.outerHeight()
  $window = $(window)
  window_height = $window.height()
  scroll_top = $window.scrollTop()
  return window_height + scroll_top > top && scroll_top < top
#return
bottomIsVisible = ($target) ->
  top = $target.offset().top
  height = $target.outerHeight()
  $window = $(window)
  window_height = $window.height()
  scroll_top = $window.scrollTop()
  return window_height + scroll_top > height + top
#return


###
 * close global/helpers.coffee
###
