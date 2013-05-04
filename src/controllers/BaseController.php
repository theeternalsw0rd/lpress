<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Html;

	class BaseController extends Controller {
		/**
		 * Setup the html macros used by all controllers.
         *
         * @return void
         */
		protected function setupMacros()
        {
			// this overrides the built-in url macro which can't take external links
			Html::macro('url', function($url, $text = null, $attributes = array()) {
				$attribute_string = '';
				$has_title = FALSE;
				if(is_array($attributes) && count($attributes) > 0) {
					foreach($attributes as $attribute => $value) {
						if($attribute == 'title') {
							$title = $value;
							$has_title = TRUE;
						}
						else {
							$attribute_string .= " $attribute='$value'";
						}
					}
				}
				$text = is_null($text) ? $url : $text;
				$title = $has_title ? $title : $text;
				return "<a href='$url' title='$title'$attribute_string>$title</a>";

			});
        }
	}
