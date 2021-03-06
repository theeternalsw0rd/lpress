<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Blade;

Blade::extend(function($value) {
	return preg_replace('/\{\$(.+)\$\}/', '<?php ${1} ?>', $value);
});
