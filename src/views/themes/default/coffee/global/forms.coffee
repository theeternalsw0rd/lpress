# package:global/ready
$('form').on(
  'click'
  'input.faux-file'
  (event) ->
    event.stopPropagation()
    event.preventDefault()
    parent = $(this).parent().find('input.file').click()
)
$('form').on(
  'click'
  'input.file'
  (event) ->
    event.stopPropagation()
)
$('form').on(
  'change'
  'input.file'
  (event) ->
    console.log($(this).val())
)
