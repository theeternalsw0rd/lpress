# package:global/ready
###
 * open global/forms.coffee
###
###
 uppercase booleans easier to read, custom code in watcher to change it, not a coffee feature.
###
$html = $('html')
$body = $('body')
$page = $(document.getElementById('page'))
getUploader = (id, upload_url, path, single, dragndrop) ->
  if single 
    input = "<input id='#{id}-input' class='file' type='file' name='files' data-url='#{upload_url}' />"
  else
    input = "<input id='#{id}-input' class='file' type='file' name='files[]' data-url='#{upload_url}' multiple />"
  #endif
  if dragndrop
    dropzone = "<p class='center'>This box is also a file drop zone.</p>"
  else
    dropzone = ""
  #endif
  $("""
    <div id='#{id}' class='colorbox'>
      <div id='#{id}-tabs' class='tabs'>
        <ul class='etabs clear-fix'>
          <li class='tab'><a href='##{id}-new'>New</a></li>
          <li class='tab'><a href='##{id}-existing'>Existing</a></li>
        </ul>
        <div id='#{id}-new' class='tab-contents'>#{dropzone}
          <div class='upload'>
            #{input}
          </div>
          <div class='progress'>
            <div class='bar'></div>
          </div>
        </div>
        <div id='#{id}-existing' class='tab-contents' data-url='#{path}'>
        </div>
      </div>
    </div>
  """)
#return
$(document).on(
  'click'
  'ul.etabs'
  (event) ->
    target = event.target
    if target.tagName is 'A'
      skip = FALSE
      $(this).find("[data-href]").each(
        ->
          if this isnt target
            $this = $(this)
            $this.attr('href', $this.data('href')).removeAttr('data-href')
          else
            skip = TRUE
          #endif
        #return
      )
      if not skip
        $target = $(target)
        $target.attr('data-href', $target.attr('href')).removeAttr('href')
        id = $target.data('href')
        $id = $(id)
        url = $id.data('url')
        if /existing/.test(id)
          $.ajax({
            url: url + '.json'
            dataType: 'json'
            success:  (data) ->
              $gallery = $("<ul id='gallery'></ul>")
              $.each(data.records, (index, item) ->
                record = item
                caption = ""
                $.each(record.values, (index, item) ->
                  value = item
                  if value.field.slug is 'file' and value.description isnt ""
                    caption = "<span class='caption'>#{value.description}</span>"
                    return false
                  #endif
                )
                $gallery.append("""
                  <li>
                    <a title='#{record.label}' href='#{url}/#{record.slug}'>
                      <img src='#{url}/#{record.slug}' />#{caption}
                    </a>
                  </li>
                """)
              )
              $id.append($gallery)
            #return
            error: (data) ->
              if data.status is 404
                json = $.parseJSON(data.responseText)
                $id.html("<h1>HttpError: 404</h1><div class='error'>#{json.reason}</div>")
              #endif
            #return
          })
        #endif
      #endif
    #endif
  #return
)
if $html.hasClass('lt-ie8')
  css = '*{noFocusLine: expression(this.hideFocus=true);}'
  head = document.getElementsByTagName('head')[0]
  style = document.createElement('style')
  style.type = 'text/css'
  style.styleSheet.cssText = css
  head.appendChild(style)
  if $body.height() > $page.height()
    $page.css('height', $body.height())
  $('form').each(
    ->
      $this = $(this)
      ### replace input buttons cause ie puts an unremovable black border around them use an anchor instead ###
      $this.find('input[type=submit]').each(
        ->
          $this = $(this)
          $this.after(
            "<a href='#' tabindex='#{$this.attr('tabindex')}' class='button submit'>#{$this.val()}</a>"
          ).remove()
        #return
      )
      $this.find('input[type=reset]').each(
        ->
          $this = $(this)
          $this.after(
            "<a href='#' tabindex='#{$this.attr('tabindex')}' class='button reset'>#{$this.val()}</a>"
          ).remove()
        #return
      )
      $this.find('input[type=button]').each(
        ->
          $this = $(this)
          $this.after(
            "<a href='#' tabindex='#{$this.attr('tabindex')}' class='button'>#{$this.val()}</a>"
          ).remove()
        #return
      )
    #return
  )
  $(document).on(
    'mousedown'
    'label.checkbox'
    (event) ->
      $(this).find('input.checkbox').focus()
    #return
  )
  $(document).on(
    'click'
    'a.submit'
    (event) ->
      event.preventDefault()
      $(this).closest('form').submit()
    #return
  )
  $(document).on(
    'focus'
    'input, textarea'
    (event) ->
      $this = $(this)
      if $this.hasClass('file') or $this.hasClass('checkbox')
        $this.next().addClass('focused')
      #endif
      $this.addClass('focused')
    #return
  )
  $(document).on(
    'blur'
    'input, textarea'
    (event) ->
      $this = $(this)
      if $this.hasClass('file') or $this.hasClass('checkbox')
        $this.next().removeClass('focused')
      #endif
      $this.removeClass('focused')
    #return
  )
  $(document).on(
    'keydown'
    'input[type=file]'
    (event) ->
      $this = $(this)
      if event.which in [13, 32]
        event.preventDefault()
        $this.click()
      #endif
    #return
  )
  $(document).on(
    'keydown'
    'input[type=text], input[type=password]'
    (event) ->
      $this = $(this)
      if event.which is 13 then $this.closest('form').submit()
    #return
  )
#endif
$(document).on(
  'mouseup'
  (event) ->
    $('.active-button').removeClass('active-button')
    $('.active-checkbox').removeClass('active-checkbox')
  #return
)
$(document).on(
  'mousedown'
  '.button, button'
  (event) ->
    $(this).addClass('active-button')
  #return
)
$(document).on(
  'focus'
  '.button, button'
  (event) ->
    $(this).addClass('focused-button')
  #return
)
$(document).on(
  'blur'
  '.button, button'
  (event) ->
    $(this).removeClass('focused-button')
  #return
)
if $html.hasClass('opacity') or $html.hasClass('ie')
  $(document).on(
    'mousedown'
    'label.checkbox'
    (event) ->
      $(this).find('span.faux-checkbox').addClass('active-checkbox')
    #return
  )
  $('form').each(
    ->
      $this = $(this)
      if $html.hasClass('lt-ie8') is false
        $this.find('input.checkbox').each(
          ->
            $(this).after(
              "<span class='faux-checkbox' id='for-#{this.id}'>
                  <span unselectable='on' class='checkmark'>&#x2713;</span>
              </span>"
            )
          #return
        )
      #endif
      $this.find('a.file').each(
        ->
          $this = $(this)
          id = this.href.split('#')[1]
          dragndrop = !!FileReader and Modernizr.draganddrop
          record = $this.data('prefix') + '/+record/create?type=' + id
          $uploader = getUploader(
            id
            $this.data('url')
            $this.data('path')
            $this.hasClass('single')
            dragndrop
          )
          label = $this.attr('title').replace(/Select/, 'Upload')
          if $html.hasClass('ie')
            $uploader.find('.upload').append(
              "<a unselectable='on' id='for-#{id}-input' class='button'>#{label}</a>"
            )
          else
            $uploader.find('.upload').append(
              "<span unselectable='on' id='for-#{id}-input' class='button'>#{label}</span>"
            )
          #endif
          $('body').append($uploader)
          $(document.getElementById(id + '-input')).fileupload({
            dataType: 'json'
            done: (e, data) ->
              $uploaded = $(document.getElementById(id + '-uploaded'))
              if $uploaded.length is 0
                $uploaded = $("<div id='#{id}-uploaded' class='tab-contents'><ul class='files'></ul></div>")
                $tabs = $(document.getElementById(id + '-tabs'))
                $tabs.append($uploaded)
                easytabs = $tabs.data('easytabs')
                $tablist = $tabs.find('ul.etabs')
                $newtab = $("<li class='tab'><a href='##{id}-uploaded'>Uploaded</a></li>")
                $tablist.append($newtab)
                easytabs.tabs.removeClass(easytabs.settings.tabActiveClass)
                easytabs.panels.removeClass(easytabs.settings.panelActiveClass)
                $tablist.find("[data-href]").each(
                  ->
                    $this = $(this)
                    $this.attr('href', $this.data('href')).removeAttr('data-href')
                  #return
                )
                easytabs.init()
              #endif
              $files = $uploaded.find('ul.files')
              $.each(data.result.files, (index, file) ->
                $files.append("""
                  <li>
                    <h3>#{file.name}</h3>
                    <p>#{record}</p>
                  </li>
                """)
              )
              $("a[href=##{id}-uploaded]").click()
            #return
            error: (e, data) ->
              $error = $("<div class='error' style='display:none'>500 Internal Server Error</div>")
              $(document.getElementById(id + '-new')).prepend($error)
              $error.slideDown('slow').delay(3000).animate({
                height: 0
                opacity: 0
              }
              'slow'
              ->
                $(this).remove()
              )
            #return
          })
          $tabs = $(document.getElementById(id + '-tabs'))
          $tabs.easytabs({updateHash: false})
          $first_tab = $tabs.find('ul.etabs a').first()
          $first_tab.attr('data-href', $first_tab.attr('href')).removeAttr('href')
          $this.colorbox({
            inline: TRUE
            fixed: TRUE
            width: '50%'
            height: '80%'
            scrolling: FALSE
            onComplete: ->
              $('body').css({'overflow': 'hidden'})
              $colorbox = $(document.getElementById('cboxLoadedContent'))
              $colorbox.find('.colorbox')
                .css({'height': $colorbox.height() + 'px'})
              $colorbox.find('a.file, input.file')
                .first().focus()
              $button = $colorbox.find('.upload')
              midpoint = ($colorbox.width() - $button.width()) / 2
              $button.css({'left': midpoint + 'px'})
              $colorbox.get(0).scrollTop = 0
            #return
            onClosed: ->
              $('body').css({'overflow': 'auto'})
            #return
          })
        #return
      )
    #return
  )
  ### firefox doesn't support focus psuedo-class on input type file ###
  $(document).on(
    'focus'
    'input.file'
    (event) ->
      $(this).next().addClass('focused-button')
    #return
  )
  $(document).on(
    'blur'
    'input.file'
    (event) ->
      $(this).next().removeClass('focused-button')
    #return
  )
  ### workaround browsers that have two-tab focus on file input ###
  if navigator.appName is 'Opera'
    $(document).on(
      'keydown'
      '*[tabindex]'
      (event) ->
        if event.which is 9
          $target = $(event.target)
          $tabindexed = $('*[tabindex]').not('[tabindex="-1"]').sort(
            (a, b) ->
              index_a = parseInt($(a).attr('tabindex'))
              index_b = parseInt($(b).attr('tabindex'))
              return index_a - index_b
            #return
          )
          current_index = $tabindexed.index($target)
          max_index = $tabindexed.length - 1
          if event.shiftKey # shift + tab
            if current_index isnt 0
              event.preventDefault()
              event.stopPropagation()
              ### opera 10 doesn't prevent default, so blur target and set smallest timeout to focus ###
              $target.blur()
              setTimeout(
                ->
                  $tabindexed.eq(current_index - 1).focus()
                #return
                1
              )
            #endif
          else
            if current_index isnt max_index
              event.preventDefault()
              event.stopPropagation()
              ### opera 10 doesn't prevent default, so blur target and set smallest timeout to focus ###
              $target.blur()
              setTimeout(
                ->
                  $tabindexed.eq(current_index + 1).focus()
                #return
                1
              )
            #endif
          #endif
        #endif
      #return
    )
    ### unify browser accessibility experience for opera ###
    $(document).on(
      'keydown'
      'input.file'
      (event) ->
        if event.which in [13, 32]
          $this = $(this)
          event.preventDefault()
          event.stopPropagation()
          $this.click()
        #endif
      #return
    )
  #endif
  if $html.hasClass('ie')
    ### unify browser accessibility experience for ie ###
    ### this.id.substring(4) removes the 'for-' from the id ###
    $(document).on(
      'keydown'
      'div.upload a.button'
      (event) ->
        if event.which in [13, 32]
          $this = $(this)
          event.preventDefault()
          event.stopPropagation()
          $this.click()
        #endif
      #return
    )
    ### this.id.substring(4) removes the 'for-' from the id ###
    $(document).on(
      'click'
      'div.upload a.button'
      (event) ->
        event.preventDefault()
        $file = $(document.getElementById(this.id.substring(4)))
        $(':focus').blur()
        $file.click()
        $(this).focus()
      #return
    )
  #endif
  $(document).on(
    'click'
    'label.checkbox'
    (event) ->
      event.preventDefault()
      $this = $(this)
      $('#' + $this.attr('for')).click()
    #return
  )
  $(document).on(
    'click'
    'input.checkbox'
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
#endif
###
 * close global/forms.coffee
###
