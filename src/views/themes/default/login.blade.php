<!DOCTYPE html>
<html>
	<head>
		<title>Hello World</title>
	</head>
	<body>
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
				{{ Form::text('username') }}
			</div>
			<div class='text'>
				{{ Form::label('Password') }}<br />
				{{ Form::text('password') }}
			</div>
			<div class='checkbox'>
				{{ Form::label('Remember me') }}
				{{ Form::checkbox('remember') }}
			</div>
			{{ Form::token() }}
			<div class='input-button'>
				{{ Form::submit('Submit') }}
			</div>
			{{ Form::close() }}
		</div>
	</body>
</html>
