@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>Site Management {{ Form::new_model_link(new EternalSword\LPress\Site) }}</h2>
	{{ HTML::collection_editor($sites) }}
	{{ $sites->links() }}
@stop
@section('footer_scripts')
	@parent
@stop
