# package:global/ready
###
 * open global/tabular.coffee
###
#
$(document).on(
  'click'
  'ul.tabular > li > a'
  (event) ->
    ulSlideDown(event, this)
  #return
)

###
 * close global/tabular.coffee
###
