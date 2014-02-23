@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>Site: {{ $site->label }}</h2>
	{{ Form::open(array('url' => Request::url())) }}
	<div class='text'>
		{{ Form::text_input(
			'text',
			'label',
			'Label:',
			$site->label,
			array(
				'autofocus' => 'autofocus',
				'tabindex' => '1'
			)
		) }}
	</div>
	<div class='text'>
		{{ Form::text_input(
			'text',
			'domain',
			'Domain:',
			$site->domain,
			array(
				'tabindex' => '2'
			)
		) }}
	</div>
	<div class='checkbox'>
		{{ Form::checkbox_input('in_production', 'Site is in production.', array('tabindex' => '3')) }}
	</div>
	<div class='select'>
		{{ Form::select_input('theme_id', 'Theme', $theme_list, $site->theme_id, array('tabindex' => '4')) }}
	</div>
	<div class='submit'>
		{{ Form::icon_button('OK', 'submit', array('class' => 'button', 'tabindex' => '5'), 'icon-ok') }}
	</div>
	{{ Form::close() }}
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
