@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<!--<h2>Welcome, {{ $user->username }} {{-- HTML::linkRoute('lpress-logout', 'Logout') --}} </h2>-->
	@if($is_root)
		<p>{{ Lang::get('l-press::messages.dashboard_root_message') }}</p>
		<h2>
			{{ Lang::get('l-press::headers.model_management', array('model' => 'Site')) }}
			<span class='small'>
				{{ HTML::new_model_link($new_site) }}&nbsp;
				{{ HTML::trash_bin_link($new_site) }}
			</span>
		</h2>
		{{ HTML::collection_editor($sites, $new_site) }}
		<a href="{{ URL::route('lpress-dashboard') }}/sites">{{ Lang::get('l-press::messages.manage_all', array('model' => 'sites')) }}</a>
		<h2>
			{{ Lang::get('l-press::headers.model_management', array('model' => 'User')) }}
			<span class='small'>
				{{ HTML::new_model_link($new_user) }}&nbsp;
				{{ HTML::trash_bin_link($new_user) }}
			</span>
		</h2>
		{{ HTML::collection_editor($users, $new_user) }}
		<a href="{{ URL::route('lpress-dashboard') }}/users">{{ Lang::get('l-press::messages.manage_all', array('model' => 'users')) }}</a>
	@endif
	@if($user->hasPermission('user-manager'))
		<h2>
			{{ Lang::get('l-press::headers.model_management', array('model' => 'User')) }}
			<span class='small'>
				{{ HTML::new_model_link($new_user) }}&nbsp;
				{{ HTML::trash_bin_link($new_user) }}
			</span>
		</h2>
		{{ HTML::collection_editor($users, $new_user) }}
		<a href="{{ URL::route('lpress-dashboard') }}/users">{{ Lang::get('l-press::messages.manage_all', array('model' => 'users')) }}</a>
	@endif
@stop
@section('footer_scripts')
	@parent
@stop
