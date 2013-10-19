@extends($view_prefix . '.layouts.master')
@section('content')
	@if ($install)
		<h1>Welcome to the LPress Installer</h1>
	@else
		<h1>Login</h1>
	@endif
	@if($login_failed)
		<div class='error'>
			<p>Login failed, please try again.</p>
		</div>
	@endif
	<div class='form'>
		{{ Form::open() }}
		@if ($install)
			<div class='text'>
				{{ Form::label('Application Key:') }}
				{{ Form::password('password', array('autofocus' => 'autofocus', 'tabindex' => '1')) }}
			</div>
			{{ Form::hidden('username', 'lpress') }}
		@else
			<div class='text'>
				{{ Form::label('Username') }}<br />
				{{ Form::text('username', array('autofocus' => 'autofocus', 'tabindex' => '1')) }}
			</div>
			<div class='text'>
				{{ Form::label('Password') }}<br />
				{{ Form::text('password', array('tabindex' => '2')) }}
			</div>
			<div class='checkbox'>
				{{ Form::label('Remember me') }}
				{{ Form::checkbox('remember') }}
			</div>
		@endif
		{{ Form::token() }}
		<div class='submit'>
			{{ Form::icon_button('OK', 'submit', array('class' => 'button', 'tabindex' => '3'), 'icon-ok') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
