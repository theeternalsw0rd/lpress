@extends($view_prefix . '.layouts.master')
@section('content')
	@if ($install)
		<h1>{{ Lang::get('l-press::headers.installer') }}</h1>
	@else
		<h1>{{ Lang::get('l-press::headers.login') }}</h1>
	@endif
	@if($login_failed)
		<div class='error'>
			<p>{{ Lang::get('l-press::errors.loginFailed') }}</p>
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
					Lang::get('l-press::labels.application_key') . Lang::get('l-press::labels.label_separator'),
					''
				) }}
			</div>
			{{ Form::hidden('username', 'lpress') }}
		@else
			{$ $button_index = 4 $}
			<div class='text'>
				{{ Form::text_input(
					'text',
					'username',
					Lang::get('l-press::labels.username') . Lang::get('l-press::labels.label_separator'),
					''
				) }}
			</div>
			<div class='text'>
				{{ Form::text_input(
					'password',
					'password',
					Lang::get('l-press::labels.password') . Lang::get('l-press::labels.label_separator'),
					''
				) }}
			</div>
			<div class='checkbox'>
				{{ Form::checkbox_input(
					'remember_me',
					Lang::get('l-press::labels.remember_me')
				) }}
			</div>
		@endif
		{{ Form::token() }}
		<div class='submit'>
			{{ Form::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button', 'tabindex' => '${button_index}'), 'fa-check') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
