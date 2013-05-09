<!DOCTYPE html>
<html>
	<head>
		<title>Hello World</title>
	</head>
	<body>
		<h1>Hello {{ $domain }}</h1>
		{{-- Example url usage --}}
		{{ HTML::url('http://www.google.com', 'Google', array('class' => 'google')) }}
	</body>
</html>
