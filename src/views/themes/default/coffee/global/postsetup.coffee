# package:global/ready
###
 * open global/postsetup.coffee
###

initializeTabindex = do ->
  $focusables = getFocusables($(document))
  $focusElement = $focusables.first()
  #endif
  rebuildTabindex($focusables, $focusElement)
  $('select').attr('tabindex', '-1')
#return

###
 * close global/postsetup.coffee
###
