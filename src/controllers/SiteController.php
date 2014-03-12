<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SiteController extends BaseController {
	public static function getModelForm($slug, $model_name, $id = NULL) {
		if(!Auth::user()->isRoot()) {
			return App::abort(403, 'You do not have permission to modify this model.');
		}
		return parent::getModelForm($slug, $model_name, $id);
	}

	public static function getModelIndex($slug, $model_name, $per_page) {
		if(!Auth::user()->isRoot()) {
			return App::abort(403, 'You do not have permission to modify this model.');
		}
		return parent::getModelIndex($slug, $model_name, $per_page);
	}
}
