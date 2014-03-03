# package:global/ready
###
 * open global/helpers.coffee
###

$ = jQuery

$('body').removeClass('nojs')

parseURI = (uri) ->
  query_string = uri.split('?')[1]
  queries = {}
  query_string.replace(/([^&=]+)=?([^&]*)(?:&+|$)/g, (match, key, value) ->
    (queries[key] = queries[key] || []).push(value)
  )
  return queries
#return

ulSlideDown = (event, clickElement) ->
  event.preventDefault()
  $this = $(clickElement)
  $item = $this.parent()
  if $item.hasClass('inactive')
    $this.next().slideDown(
      'fast'
      ->
        $item.removeClass('inactive').addClass('active')
      #return
    )
  else
    $this.next().slideUp(
      'fast'
      ->
        $item.removeClass('active').addClass('inactive')
      #return
    )
  #endif
#return

###
 * close global/helpers.coffee
###
