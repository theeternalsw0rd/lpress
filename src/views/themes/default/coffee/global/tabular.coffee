# package:global/ready
###
 * open global/tabular.coffee
###
#
$(document).on(
  'click'
  'ul.tabular > li > a'
  (event) ->
    event.preventDefault()
    $this = $(this)
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
)

###
 * close global/tabular.coffee
###
