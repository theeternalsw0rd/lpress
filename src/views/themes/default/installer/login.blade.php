@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	@if($login_failed)
		<div class='error'>
			<p>Incorrect application key, please use the one that was active during package database seeding.</p>
		</div>
	@endif
	<h1>Welcome to the LPress Installer</h1>
	<div class='form'>
		{{ Form::open() }}
		<div class='text'>
			{{ Form::label('Application Key:') }}
			{{ Form::password('password') }}
		</div>
		{{ Form::hidden('username', 'lpress') }}
		<div class='submit'>
			{{ Form::submit('Submit') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
@section('footer_scripts')
	@parent
@stop
