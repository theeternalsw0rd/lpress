<!DOCTYPE html>
<html>
	<head>
		<title>Already Logged In</title>
	</head>
	<body>
		<h1>You are currently logged in as {{ Auth::user()->username }}</h1>
		<p>If this is not your account, please click <a href="/logout/login">here</a> and try again.</p>
	</body>
</html>
