@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	{$ $is_root = $user->isRoot() $}
	<!--<h2>Welcome, {{ $user->username }} {{-- HTML::linkRoute('lpress-logout', 'Logout') --}} </h2>-->
	@if($is_root)
		<p>
			As the root user of the wildcard site, you have access to control all aspects
			of this installation at the global level and at the individual site level.
		</p>
		<h2>Site Management</h2>
		{{ HTML::collection_table($sites, $columns) }}
	@endif
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
