<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController {
	public static function hasPermission($id = NULL) {
		$user = Auth::user();
		if(is_null($id)) {
			return $user->hasPermission('user-manager');
		}
		return $user->id == $id || $user->hasPermission('user-manager');
	}

	public static function getRedirect($id = NULL) {
		$route_prefix = (new PrefixGenerator)->getPrefix();
		$dashboard_route = '+' . Config::get('l-press::dashboard_route');
		$prefix = $route_prefix . '/' . $dashboard_route;
		if(is_null($id)) {
			return $prefix . '/users/:id:/groups';
		}
		return $prefix;
	}

	public static function getPivotEditor($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column = array()) {
		$user = Auth::user();
		$user->load('groups');
		$extra_column = array();
		$sites = array('name' => 'site', 'labels' => array(), 'ids' => array());
		foreach($user->groups as $group) {
			if($group->id == 1 || $group->id == 2) {
				$site_id = $group->pivot->site_id;
				if($site_id == 0) {
					$sites_cache = Site::all();
					$sites['ids'] = $sites_cache->lists('id');
					$sites['labels'] = $sites_cache->lists('label');
					array_unshift($sites['ids'], 0);
					array_unshift(
						$sites['labels'],
						Lang::get(
							'l-press::labels.all',
							array('type' => Lang::get('l-press::labels.sites'))
						)
					);
					break;
				}
				if($site_id == SITE) {
					$sites['ids'][] = $site_id;
					$site = Site::find($site_id);
					$sites['labels'][] = $site->label;
				}
			}
		}
		$extra_column = $sites;
		return parent::getPivotEditor($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column);
	}

	public static function processPivotModelForm($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column = array()) {
		$rules = array(
			'site' => 'exists_or_zero:sites,id'
		);
		$validator = Validator::make(Input::only('site'), $rules, CustomValidator::getOwnMessages());
		$validator->setAttributeNames(Lang::get('l-press::labels'));
		if($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$extra_column = array('site_id' => Input::get('site'));
		return parent::processPivotModelForm($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column);
	}
}
