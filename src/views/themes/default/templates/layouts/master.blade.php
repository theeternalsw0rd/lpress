<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{{ $title }}</title>
		@section('head_scripts')
			{{ HTML::asset('js', 'vendor/modernizr/modernizr.js') }}
		@show
		<script>
			var msie=/*@cc_on!@*/0; // detect internet explorer for file-upload click workaround
			if(msie) document.documentElement.className += ' ie';
		</script>
		@section('styles')
			{{ HTML::asset('css', 'vendor/h5bp/normalize.css') }}
			{{ HTML::asset('css', 'vendor/h5bp/main.css') }}
			{{ HTML::asset('css', 'vendor/dropzone/basic.css') }}
			{{ HTML::asset('css', 'vendor/colorbox/colorbox.css') }}
			{{ HTML::asset('css', 'compiled/global/master.css') }}
		@show
	</head>
	<body class='nojs'>
		<div id='page'>
			<!--[if lt IE 8]>
				<div class="message">
					<p>You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
				</div>
			<![endif]-->
			@yield('content')
		</div>
		@section('hidden')
		@show
		@section('footer_scripts')
			{{ HTML::asset('js', 'vendor/jquery/jquery.js') }}
			{{ HTML::asset('js', 'vendor/colorbox/colorbox.js') }}
			{{ HTML::asset('js', 'vendor/dropzone/dropzone.js') }}
			{{ HTML::asset('js', 'vendor/jquery-easytabs/jquery.easytabs.js') }}
			{{ HTML::asset('js', 'compiled/global/ready.js') }}
		@show
	</body>
	<!-- this file was built on top of the HTML5 Boilerplate <https://github.com/h5bp/html5-boilerplate> included index.html file -->
</html>
