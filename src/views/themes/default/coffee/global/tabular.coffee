# package:global/ready
###
 * open global/tabular.coffee
###
#
$(document).on(
  'click'
  'ul.tabular > li > a'
  (event) ->
    ulSlideToggle(event, this)
    $this = $(this)
    $icon = $(this).find('span.icon')
    if($this.parent().hasClass('active'))
      $icon.html(icons['fa-caret-square-o-down'])
    else
      $icon.html(icons['fa-caret-square-o-up'])
    #endif
  #return
)

###
 * close global/tabular.coffee
###
