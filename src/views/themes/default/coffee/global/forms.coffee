# package:global/ready
if msie is on
  $('html').addClass('ie')
$(document).on(
  'mousedown'
  'input.button'
  (event) ->
    $(this).addClass('active-button')
  #return
)
$(document).on(
  'mouseup'
  (event) ->
    $('.active-button').removeClass('active-button')
  #return
)
$('.opacity form').each(
  ->
    $(this).find('input.file').attr('tabindex', '-1')
  #return
)
$('.opacity form').on(
  'focus'
  'input.file'
  (event) ->
    $this = $(this)
    if $.isEmptyObject(event.originalEvent) is false
      $this.parent().find('input.faux-file').focus()
    else
      setTimeout(
        ->
          $this.parent().find('input.faux-file').focus()
        #return
        300
      )
    #endif
  #return
)
$('.opacity form').on(
  'click'
  'input.faux-file'
  (event) ->
    $this = $(this)
    #focus is for older opera
    event.preventDefault()
    $(this).focus()
    $(this).parent().find('input.file').trigger('focus', jQuery.Event('focus')).click()
  #return
)
$('.opacity form').on(
  'keydown'
  'input.faux-file'
  (event) ->
    key = event.which
    switch key
      when 13, 32 # enter or space
        event.preventDefault()
        $(this).click()
      #end cases
    #end switch
  #return
)
$('.opacity form').on(
  'change'
  'input.file'
  (event) ->
    console.log($(this).val())
  #return
)
$('.opacity form').on(
  'click'
  'label.checkbox'
  (event) ->
    event.preventDefault()
    $this = $(this)
    $('#' + $this.attr('for')).click()
  #return
)
$('.opacity form').on(
  'click'
  'input.real-checkbox'
  (event) ->
    event.stopPropagation()
    $this = $(this)
    if $this.is(':checked') is true
      $this.parent().addClass('checked')
    else
      $this.parent().removeClass('checked')
    #endif
  #return
)
