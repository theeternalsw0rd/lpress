# package:global/ready
###
 * open global/expand_down.coffee
###
#
$(document).on(
  'click'
  'ul.tabular > li > a'
  (event) ->
    $item = $(this).parent()
    if $item.hasClass('inactive')
      $item.find('ul').slideDown(
        'fast'
        ->
          $item.removeClass('inactive').addClass('active')
        #return
      )
    else
      $item.find('ul').slideUp(
        'fast'
        ->
          $item.removeClass('active').addClass('inactive')
        #return
      )
    #endif
  #return
)

###
 * close global/expand_down.coffee
###
