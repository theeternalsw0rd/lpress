{$ $ua_class = \EternalSword\Lib\UserAgent::getClass() $}
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
	<!--[if gt IE 8]><!--> <html lang="en" class="no-js{{ $ua_class }}"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ $title }}</title>
		@section('head_scripts')
			{!! HTML::asset('js', 'vendor/modernizr/modernizr.js') !!}
		@show
		<script>
			var msie=/*@cc_on!@*/0; // detect internet explorer cause it's still quirky
			if(msie) document.documentElement.className += ' ie';
		</script>
		@section('styles')
			{!! HTML::asset('css', 'vendor/h5bp/normalize.css') !!}
			{!! HTML::asset('css', 'vendor/h5bp/main.css') !!}
			{!! HTML::asset('css', 'vendor/colorbox/colorbox.css') !!}
			{!! HTML::asset('css', 'compiled/global/master.css') !!}
		@show
	</head>
	<body class='nojs'>
		<div id='page'>
			<!--[if lt IE 8]>
				<div class="message">
					<p>{!! Lang::get('l-press::messages.browse_happy') !!}</p>
				</div>
			<![endif]-->
			@if(Session::has('std_errors'))
				@foreach(Session::get('std_errors') as $error)
					@if(!empty($error))
						<div class='error'>
							<p>{{ $error }}</p>
						</div>
					@endif
				@endforeach
			@endif
			@if(Session::has('messages'))
				@foreach(Session::get('messages') as $message)
					@if(!empty($message))
						<div class='message'>
							<p>{{ $message }}</p>
						</div>
					@endif
				@endforeach
			@endif
			@section('content')
				<div class='message no-js'>{{ Lang::get('l-press::messages.noJS') }}</div>
			@show
		</div>
		@section('hidden')
		@show
		@section('footer_scripts')
			{!! HTML::asset('js', 'compiled/lang/' . Config::get('app.locale') . '.js') !!}
			{!! HTML::asset('js', 'vendor/jquery/jquery.js') !!}
			{!! HTML::asset('js', 'vendor/colorbox/colorbox.js') !!}
			{!! HTML::asset('js', 'vendor/dropzone/dropzone.js') !!}
			{!! HTML::asset('js', 'vendor/jquery-easytabs/jquery.easytabs.js') !!}
			{!! HTML::asset('js', 'compiled/global/ready.js') !!}
		@show
	</body>
	<!-- this file was built on top of the HTML5 Boilerplate <https://github.com/h5bp/html5-boilerplate> included index.html file -->
</html>
