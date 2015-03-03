@extends($view_prefix . '.layouts.master')
@section('content')
	<h1>{{ Lang::get('l-press::headers.logged_in_as', array('user' => Auth::user()->username)) }}</h1>
	<p>{{ Lang::get('l-press::messages.check_user', array('href' => URL::route('lpress-logout-login'))) }}</p>
@stop
