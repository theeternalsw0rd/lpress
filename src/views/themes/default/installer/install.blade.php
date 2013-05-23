@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>Create User</h1>
	<div class='form'>
		{{ Form::open(array('route' => 'lpress-user-update')) }}
		<div class='text'>
			{{ Form::label('Username:') }}
			{{ Form::text('username', '', array('autofocus' => 'autofocus')) }}
		</div>
		<div class='text'>
			{{ Form::label('Password:') }}
			{{ Form::password('password') }}
		</div>
		<div class='text'>
			{{ Form::label('Verify Password:') }}
			{{ Form::password('verify_password') }}
		</div>
		<div class='text'>
			{{ Form::label('Email Address:') }}
			{{ Form::input('email', 'email') }}
		</div>
		<div class='checkbox'>
			{{ Form::faux_checkbox('email-public', 'Allow email to be displayed publicly.') }}
		</div>
		<div class='text'>
			{{ Form::label('Title:') }}
			{{ Form::text('first_name') }}
		</div>
		<div class='text'>
			{{ Form::label('First Name:') }}
			{{ Form::text('first_name') }}
		</div>
		<div class='text'>
			{{ Form::label('Last Name:') }}
			{{ Form::text('last_name') }}
		</div>
		<div class='text'>
			{{ Form::label('Name Suffix:') }}
			{{ Form::text('suffix') }}
		</div>
		<div class='text'>
			{{ Form::label('Bio:') }}
			{{ Form::textarea('bio') }}
		</div>
		<div class='file'>
			<label class='file' for='image'>
				<input type='button' class='faux-file' value='Image' />
				<input id='image' name='image' class='file' type='file' />
			</label>
		</div>
		<div class='submit'>
			{{ Form::submit('Submit') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
@section('footer_scripts')
	@parent
@stop
