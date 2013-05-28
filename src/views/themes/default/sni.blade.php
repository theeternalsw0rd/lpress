@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>SSL Requires SNI</h1>
	<div class='error'>
		<p>
			It appears you are using Internet Explorer on Windows XP.
			The secure portions of this website requires SNI support
			which is not available in your browser. Please try an alternative,
			such as <a href='http://chrome.google.com'>Google Chrome</a> or
			<a href='http://www.mozilla.com/firefox'>Mozilla Firefox</a>.
		</p>
	</div>

@stop
@section('footer_scripts')
	@parent
@stop
