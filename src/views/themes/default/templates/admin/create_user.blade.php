@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>Create User</h1>
	<div class='form clearfix'>
		@if ($install)
			{{ Form::open(array('route' => 'lpress-user-update')) }}
		@else
			{{ Form::open(array('route' => 'lpress-user-create')) }}
		@endif
		<div class='text'>
			{{ Form::label('Username:') }}
			{{ Form::text('username', '', array('autofocus' => 'autofocus', 'tabindex' => '1')) }}
		</div>
		<div class='text'>
			{{ Form::label('Password:') }}
			{{ Form::password('password', array('tabindex' => '2')) }}
		</div>
		<div class='text'>
			{{ Form::label('Verify Password:') }}
			{{ Form::password('verify_password', array('tabindex' => '3')) }}
		</div>
		<div class='text'>
			{{ Form::label('Email Address:') }}
			{{ Form::input('email', 'email', '', array('tabindex' => '4')) }}
		</div>
		<div class='checkbox'>
			{{ Form::faux_checkbox('email-public', 'Allow email to be displayed publicly.', array('tabindex' => '5')) }}
		</div>
		<div class='text'>
			{{ Form::label('Title:') }}
			{{ Form::text('first_name', '', array('tabindex' => '6')) }}
		</div>
		<div class='text'>
			{{ Form::label('First Name:') }}
			{{ Form::text('first_name', '', array('tabindex' => '7')) }}
		</div>
		<div class='text'>
			{{ Form::label('Last Name:') }}
			{{ Form::text('last_name', '', array('tabindex' => '8')) }}
		</div>
		<div class='text'>
			{{ Form::label('Name Suffix:') }}
			{{ Form::text('suffix', '', array('tabindex' => '9')) }}
		</div>
		<div class='text'>
			{{ Form::label('Bio:') }}
			{{ Form::textarea('bio', '', array('tabindex' => '10')) }}
		</div>
		<div class='file'>
			{{ Form::faux_file('image', 'Upload Image', 'images', array('tabindex' => '11')) }}
		</div>
		{{ Form::hidden('install', $install) }}
		<div class='submit'>
			{{ Form::submit('Submit', array('class' => 'button', 'tabindex' => '12')) }}
		</div>
		{{ Form::close() }}
	</div>
@stop
@section('footer_scripts')
	@parent
@stop
