# package:global/ready
###
 * open global/gallery.coffee
###

$gallery = $(document.getElementById('gallery'))
$gallery.find('.gallery').colorbox({
  rel: 'gallery'
  photo: true
  maxHeight: '90%'
  maxWidth: '90%'
})

###
 * close global/gallery.coffee
###
