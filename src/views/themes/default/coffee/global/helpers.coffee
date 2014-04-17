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

initializeTabindex = do ->
  $focusables = getFocusables($(document))
  $focusElement = $focusables.first()
  #endif
  rebuildTabindex($focusables, $focusElement)
#return

###
 * close global/helpers.coffee
###
