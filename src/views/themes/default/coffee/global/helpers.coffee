# package:global/ready
###
 * open global/helpers.coffee
###

$ = jQuery

$('body').removeClass('nojs')

###
 start icon codes from font awesome
###
icons = {
  'fa-check': "&#xf00c;"
  'fa-times': "&#xf00d;"
  'fa-sort': "&#xf0dc;"
  'fa-times-circle': "&#xf057;"
  'fa-caret-square-o-down': "&#xf150;"
  'fa-caret-square-o-up': "&#xf151;"
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
  return $element.find('a[href], input, select, button, textarea, *[contenteditable="true"]').filter(':not(.disabled)').filter(':visible')
#return

rebuildTabindex = ($focusables, $focusElement) ->
  $('*[tabindex="*"]').attr('tabindex', '-1')
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
        $focusables = getFocusables($(document))
        $focusElement = getFocusables($(this)).first()
        rebuildTabindex($focusables, $focusElement)
        $root = $(this).closest('ul.select')
        if $root.length > 0
          pinToBottom($root, $root.find('a.label'), $root.find('a.close'), 'right')
      #return
    )
  else
    $this.next().slideUp(
      'fast'
      ->
        $item.removeClass('active').addClass('inactive')
        $focusables = getFocusables($(document))
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
initializeTabindex = do ->
  $focusables = getFocusables($(document))
  $focusElement = $focusables.first()
  #endif
  rebuildTabindex($focusables, $focusElement)
#return

###
 * close global/helpers.coffee
###
