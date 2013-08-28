# package:global/ready
###
 * open global/forms.coffee
###
$html = $('html')
$body = $('body')
$page = $(document.getElementById('page'))
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
          upload_url = $this.data('url')
          if $this.hasClass('single')
            $uploader = $("""
              <div id='#{id}' class='colorbox'>
                <div class='upload'>
                  <input id='#{id}_input' class='file' type='file' name='file' data-url='#{upload_url}' />
                </div>
                <div class='progress'>
                  <div class='bar'></div>
                </div>
              </div>
            """)
          else
            $uploader = $("""
              <div id='#{id}' class='colorbox'>
                <div class='upload'>
                  <input id='#{id}_input' class='file' type='file' name='files[]' data-url='#{upload_url}' multiple />
                </div>
                <div class='progress'>
                  <div class='bar'></div>
                </div>
              </div>
            """)
          #endif
          if $html.hasClass('ie')
            $uploader.find('.upload').append(
              "<a unselectable='on' id='for-#{id}_input' class='button'>#{$this.attr('title')}</a>"
            )
          else
            $uploader.find('.upload').append(
              "<span unselectable='on' id='for-#{id}_input' class='button'>#{$this.attr('title')}</span>"
            )
          #endif
          if !!FileReader and Modernizr.draganddrop
            $uploader.prepend("<p>This box is also a file drop zone.</p>")
          #endif
          $('body').append($uploader)
          $this.colorbox({
            inline: true
            width: '50%'
            height: '80%'
            onComplete: ->
              $colorbox = $(document.getElementById('cboxLoadedContent'))
              $colorbox.find('a, input').first().focus()
              $button = $colorbox.find('.upload')
              midpoint = ($colorbox.width() - $button.width()) / 2
              $button.css({'left': midpoint + 'px'})
              $(document.getElementById('cboxTitle')).hide()
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
