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
			{$ $button_index = 2 $}
			<div class='text'>
				{{ Form::text_input(
					'password',
					'password',
					'Application Key:',
					'',
					array(
						'autofocus' => 'autofocus',
						'tabindex' => '1'
					)
				) }}
			</div>
			{{ Form::hidden('username', 'lpress') }}
		@else
			{$ $button_index = 4 $}
			<div class='text'>
				{{ Form::text_input(
					'text',
					'username',
					'Username:',
					'',
					array(
						'autofocus' => 'autofocus',
						'tabindex' => '1'
					)
				) }}
			</div>
			<div class='text'>
				{{ Form::text_input(
					'password',
					'password',
					'Password:',
					'',
					array(
						'tabindex' => '2'
					)
				) }}
			</div>
			<div class='checkbox'>
				{{ Form::checkbox_input(
					'remember_me',
					'Remember me',
					array(
						'tabindex' => '3'
					)
				) }}
			</div>
		@endif
		{{ Form::token() }}
		<div class='submit'>
			{{ Form::icon_button('OK', 'submit', array('class' => 'button', 'tabindex' => '${button_index}'), 'fa-check') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
