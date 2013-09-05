@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>SSL Requires SHA2</h1>
	<div class='error'>
		<p>
			It appears you are using Windows XP.
			The secure portions of this website requires SHA2 support
			which is not available in your browser. Only
			<a href='http://www.mozilla.com/firefox'>Mozilla Firefox</a>
			has been verified to work under Windows XP.
		</p>
	</div>

@stop
@section('footer_scripts')
	@parent
@stop
