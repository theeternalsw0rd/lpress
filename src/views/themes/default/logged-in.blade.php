<!DOCTYPE html>
<html>
	<head>
		<title>Already Logged In</title>
	</head>
	<body>
		<h1>You are currently logged in as {{ Auth::user()->username }}</h1>
		<p>If this is not your account, please click {{ Link::link_to_route('lpress-logout-login', 'here') }} and try again.</p>
	</body>
</html>
