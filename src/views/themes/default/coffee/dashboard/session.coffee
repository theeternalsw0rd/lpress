# package:dashboard/ready
###
 * open dashboard/session.coffee
###
setTimeout(
  ->
    $.ajax({
      url: document.URL.split("?")[0].split("#")[0].replace(/\/$/, "") + '/session.json',
      dataType: 'json',
      success: (data) ->
        console.log(data)
        if(!data.active) then document.location.reload()
      #return
    })
  #return
  100
)

###
 * close dashboard/session.coffee
###
