# package:global/ready
###
 * open global/forms.coffee
###
###
 uppercase booleans easier to read, custom code in watcher to change it, not a coffee feature.
###

###
 start icon codes from font awesome
###
icons = {
  'fa-check': "&#xf00c;"
  'fa-sort': "&#xf0dc;"
}
###
 end icon codes
###
Dropzone.autoDiscover = false
$html = $('html')
$body = $('body')
$page = $(document.getElementById('page'))
$(document).on(
  'click'
  'ul.select > li > a'
  (event) ->
    ulSlideDown(event, this)
  #return
)
$(document).on(
  'click'
  'a.option'
  (event) ->
    event.preventDefault()
    $this = $(this)
    $target = $($this.attr('href'))
    $target.val($this.data('value'))
    $target.find('option[selected="selected"]').removeAttr('selected')
    $label = $this.closest('ul').prev()
    $label.find('span.current').html(' ' + $this.html())
    $label.click()
  #return
)
getUploader = (id, path, target_id, attachment_type) ->
  return $("""
    <div id='#{id}' class='colorbox'>
      <div id='#{id}-tabs' class='tabs'>
        <ul class='etabs clear-fix'>
          <li class='tab'><a href='##{id}-new'>New</a></li>
          <li class='tab'><a href='##{id}-existing'>Existing</a></li>
        </ul>
        <ul id='#{id}-new' class='tab-contents dropzone files'>
        </ul>
        <div id='#{id}-existing' class='tab-contents' data-url='#{path}' data-attachment_type='#{attachment_type}' data-target_id='#{target_id}'>
        </div>
      </div>
    </div>
  """)
#return
$('select').each(
  ->
    $select = $(this)
    $label = $select.prev().hide()
    label = $label.html()
    $options = $select.find('option')
    current = $select.find('option[selected="selected"]').html()
    if $options.length > 1
      html = "<ul class='select'><li class='inactive'><a href='#' class='label'>#{label}<span class='current'> #{current}</span><span class='icon'>#{icons['fa-sort']}</span></a><ul class='options'>"
      $options.each(
        ->
          $option = $(this)
          html += "<li><a class='option' href='##{ $select.attr('id') }' data-value='#{ $option.attr('value') }'>#{ $option.html() }</a></li>"
        #return
      )
      html += "</ul></li></ul>"
    else
      html = "<ul class='select'><li><a class='label disabled'>#{label} #{current}<span class='icon disabled'>#{icons['fa-sort']}</span></a></li></ul>"
    #endif
    $select.after(html)
  #return
)
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
        id = $target.attr('href')
        $target.attr('data-href', id).removeAttr('href')
        $content_box = $(id)
        attachment_type = $content_box.data('attachment_type')
        url = $content_box.data('url')
        if /existing/.test(id)
          $existing_files = $(document.getElementById("existing-files-#{id}"))
          if $existing_files.length is 0
            $.ajax({
              url: url + '.json'
              dataType: 'json'
              success:  (data) ->
                $existing_files = $("<ul id='existing-files-#{id}' class='files'></ul>")
                $.each(data.records, (index, record) ->
                  target_id = $content_box.data('target_id')
                  alt = record.label
                  $.each(record.values, (index, value) ->
                    if value.field.slug is 'file-description'
                      alt = value.current_revision.contents
                      return false
                    #endif
                  )
                  if attachment_type is 'images'
                    $existing_files.append("""
                      <li>
                        <a class='file_select' title='#{record.label}' href='#{url}/#{record.slug}' data-record_id='#{record.id}' data-target_id='#{target_id}'>
                          <img src='#{url}/#{record.slug}' alt='#{alt}' />
                          <span class='caption'>#{record.label}</span>
                        </a>
                      </li>
                    """)
                  #endif
                )
                $content_box.append($existing_files)
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
    #endif
  #return
)
$(document).on(
  'click',
  'a.file_select',
  (event) ->
    event.preventDefault()
    $this = $(this)
    $target = $(document.getElementById($this.data('target_id')))
    $target.val($this.data('record_id'))
    $.colorbox.close()
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
          record = $this.data('prefix') + '/records/create?type=' + id
          token = $this.data('token')
          target_id = $this.data('target_id')
          $uploader = getUploader(
            id
            $this.data('path')
            target_id
            $this.data('attachment_type')
          )
          $('body').append($uploader)
          previewTemplate = """
            <li>
              <a class='dz-preview dz-file-preview'>
                <img data-dz-thumbnail />
                <div class='dz-progress'><span class='dz-upload' data-dz-uploadprogress></span></div>
                <span class='caption'>Uploading...</caption>
              </a>
            </li>
          """
          myDropzone = new Dropzone(
            "##{id}-new"
            {
              url: $this.data('url')
              thumbnailWidth: 500
              thumbnailHeight: 500
              previewTemplate: previewTemplate
            }
          )
          myDropzone.on(
            'sending'
            (file, xhr, formData) ->
              formData.append('_token', token)
            #return
          )
          myDropzone.on(
            'success'
            (file, response) ->
              uri = response.uri
              record = response.record
              success_mark = "<div class='dz-success-mark'><span class='button-icon'>#{icons['fa-check']}</span></div>"
              $anchor = $(file.previewElement).children().first()
              $anchor.addClass('dz-success').addClass('file_select')
              $anchor.attr('title', record.label).attr('href', "#{uri}/#{record.slug}")
              $anchor.data('record_id', record.id).data('target_id', target_id)
              $anchor.find('img').attr('alt', record.label)
              $anchor.find('.caption').first().html(record.label).after(success_mark)
            #return
          )
          myDropzone.on(
            'error'
            (file, error_message) ->
              if typeof(error_message) is 'object'
                error_message = error_message.error
              else
                $error_message = $(error_message)
                error_message = $error_message.find('h1').first().html()
              #endif
              $anchor = $(file.previewElement).children().first()
              height = 0
              $anchor.children().each(
                ->
                  height += $(this).outerHeight(TRUE)
                #return
              )
              $error_message = $("<div class='dz-error-message' style='height:#{height}px'><span>#{error_message}</span></div>")
              $anchor.removeAttr('href').addClass('dz-error')
              $anchor.html("").append($error_message)
              $anchor.on(
                'click'
                (event) ->
                  event.preventDefault()
                #return
              )
            #return
          )
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
              $colorbox.find('.dropzone')
                .first().focus()
              $colorbox.get(0).scrollTop = 0
              tab_bar_height = $colorbox.find('.etabs').first().outerHeight()
              $colorbox.find('.tab-contents').each(
                ->
                  $this = $(this)
                  spacing = $this.outerHeight() - $this.height()
                  $(this).height($colorbox.height() - tab_bar_height - spacing)
                #return
              )
            #return
            onCleanup: ->
              id = $this.closest('.colorbox').attr('id')
              $("a[href=##{id}-new]").click()
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
