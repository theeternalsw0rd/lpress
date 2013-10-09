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

###
 * close global/helpers.coffee
###
