@extends($view_prefix . '.layouts.master')
@section('content')
	<h1>Login</h1>
	@if($login_failed)
		<div class='error'>
			<p>Login failed, please try again.</p>
		</div>
	@endif
	<div class='form'>
		{{ Form::open() }}
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
		{{ Form::token() }}
		<div class='submit'>
			{{ Form::submit('Submit', array('class' => 'button', 'tabindex' => '3')) }}
		</div>
		{{ Form::close() }}
	</div>
@stop
