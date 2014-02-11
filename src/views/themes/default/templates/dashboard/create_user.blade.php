@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>Create User</h2>
	<div class='form clearfix'>
		@if ($install)
			{{ Form::open(array('route' => 'lpress-dashboard.update-user')) }}
			{{ Form::hidden('install', $install) }}
			{{ Form::hidden('user_id', 1) }}
		@else
			{{ Form::open(array('route' => 'lpress-dashboard.create-user')) }}
		@endif
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
		<div class='text'>
			{{ Form::text_input(
				'password',
				'verify_password',
				'Verify Password:',
				'',
				array(
				'tabindex' => '3'
				)
			) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'email',
				'email',
				'Email Address:',
				'',
				array(
				'tabindex' => '4'
				)
			) }}
		</div>
		<div class='checkbox'>
			{{ Form::checkbox_input('email_visible', 'Allow email to be displayed publicly.', array('tabindex' => '5')) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'text',
				'name_prefix',
				'Title (eg Mr.):',
				'',
				array(
				'tabindex' => '6'
				)
			) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'text',
				'first_name',
				'First Name:',
				'',
				array(
				'tabindex' => '7'
				)
			) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'text',
				'last_name',
				'Last Name:',
				'',
				array(
				'tabindex' => '8'
				)
			) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'text',
				'suffix',
				'Name Suffix (eg Jr.)',
				'',
				array(
				'tabindex' => '9'
				)
			) }}
		</div>
		<div class='text'>
			{{ Form::text_input(
				'textarea',
				'bio',
				'Bio:',
				'',
				array(
				'tabindex' => '10'
				)
			) }}
		</div>
		<div class='file'>
			{{ Form::file_input(
				'avatars',
				'create',
				FALSE,
				'',
				array(
					'tabindex' => '11',
					'data-target_id' => 'user_image'
				)
			) }}
		</div>
		{{ Form::token() }}
		<div class='submit'>
			{{ Form::icon_button('OK', 'submit', array('class' => 'button', 'tabindex' => '12'), 'icon-ok') }}
		</div>
		{{ Form::close() }}
	</div>
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
