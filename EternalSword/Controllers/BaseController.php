<?php namespace EternalSword\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use EternalSword\Lib\ClassLoader;
use EternalSword\Lib\CustomValidator;
use EternalSword\Lib\MacroLoader;
use EternalSword\Lib\PrefixGenerator;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;

class BaseController extends Controller {
	protected static function getRedirect() {
		if(Session::has('model_post_redirect')) {
			return Session::get('model_post_redirect');
		}
		return URL::previous();
	}

	protected static function processModelForm($model_name, $redirect_url, $id = NULL) {
		if(is_null($id)) {
			$model = new $model_name();
			$action = 'create';
		}
		else {
			$model = $model_name::find($id);
			$action = 'update';
			if(is_null($model)) {
				return Redirect::back()->withInput()->with(
					'errors',
					array(
						Lang::get(
							'l-press::errors.modelIdNotFound',
							array('id' => $id)
						)
					)
				);
			}
		}
		$validator = Validator::make(Input::all(), $model->processRules(), CustomValidator::getOwnMessages());
		$validator->setAttributeNames(Lang::get('l-press::labels'));
		if($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$model->fill(Input::all());
		if(Input::has('password')) {
			$model->password = Hash::make(Input::get('password'));
		}
		$model->saveItem($action);
		$redirect_url = str_replace(':id:', $model->id, $redirect_url);
		return Redirect::to($redirect_url);
	}

	protected static function restore($model_name, $id) {
		$model = $model_name::onlyTrashed()->where('id', $id)->first();
		if(is_null($model)) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get(
						'l-press::errors.modelIdNotFound',
						array('id' => $id)
					)
				)
			);
		}
		$model->restoreItem();
		$url = URL::previous();
		$url = preg_replace('/\/[0-9]+$/', '', $url);
		return Redirect::to($url);
	}

	protected static function delete($model_name, $id) {
		if(Input::has('type') && Input::get('type') == 'force') {
			self::forceDelete($model_name, $id);
		}
		else {
			self::trash($model_name, $id);
		}
		$url = URL::previous();
		$url = preg_replace('/\/[0-9]+$/', '', $url);
		return Redirect::to($url);
	}

	protected static function trash($model_name, $id) {
		$model = $model_name::find($id);
		if(is_null($model)) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get(
						'l-press::errors.modelIdNotFound',
						array('id' => $id)
					)
				)
			);
		}
		$model->deleteItem();
	}

	protected static function forceDelete($model_name, $id) {
		$model = $model_name::withTrashed()->where('id', $id);
		if(is_null($model)) {
			return Redirect::back()->with(
				'std_errors',
				array(
					Lang::get(
						'l-press::errors.modelIdNotFound',
						array('id' => $id)
					)
				)
			);
		}
		$model->forceDelete();
	}


	public static function getModelForm($slug, $model_name, $id = NULL) {
		extract(self::prepareMake());
		Session::set('model_post_redirect', URL::previous());
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		if(is_null($id)) {
			$model = new $model_name();
			$title = Lang::get(
				'l-press::titles.newModel',
				array(
					'model_basename' => $model_basename
				)
			);
		}
		else {
			$model = $model_name::find($id);
			if(is_null($model)) {
				return App::abort(
					404, 
					Lang::get(
						'l-press::errors.modelIdNotFound',
						array('id' => $id)
					)
				);
			}
			$title = Lang::get(
				'l-press::titles.updateModel',
				array(
					'model_basename' => $model_basename,
					'model_label' => $model->label
				)
			);
		}
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => $title,
			'model' => $model,
			'model_basename' => $model_basename
		);
		$slug_view = $view_prefix . '.dashboard.' . $slug . '.form';
		if(View::exists($slug_view)) {
			return HTMLMin::live(View::make($slug_view, $pass_to_view));
		}
		return HTMLMin::live(View::make($view_prefix . '.dashboard.models.form', $pass_to_view));
	}

	protected static function verifyPivot($model_name, $model_id, $pivot) {
		$model = $model_name::find($model_id);
		if(is_null($model)) {
			return array(
				'code' => 404,
				'data' => Lang::get(
					'l-press::errors.modelIdNotFound',
					array('id' => $id)
				)
			);
		}
		try {
			$model->load($pivot);
		} catch(\Exception $e) {
			return array(
				'code' => 404,
				'data' => Lang::get(
					'l-press::errors.pivotNotFound',
					array('model_name' => $model_name, 'pivot' => $pivot)
				)
			);
		}
		return array(
			'code' => 200,
			'data' => $model
		);
	}

	public static function getPivotEditor($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column = array()) {
		extract(self::prepareMake());
		$pivot_test = self::verifyPivot($model_name, $model_id, $pivot);
		$code = $pivot_test['code'];
		if($code != 200) {
			return App::abort($code, $pivot_test['data']);
		}
		$model = $pivot_test['data'];
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		$pivot_basename = explode('\\', $pivot_name);
		$pivot_basename = end($pivot_basename);
		$pivot_label = Str::title($pivot);
		$title = Lang::get(
			'l-press::titles.updateModel',
			array(
				'model_basename' => $model_basename,
				'model_label' => $model->label
			)
		);
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => $title,
			'model' => $model,
			'pivot_basename' => $pivot_basename,
			'pivot_name' => $pivot_name,
			'pivot' => $pivot,
			'pivot_label' => $pivot_label,
			'model_basename' => $model_basename,
			'extra_column' => $extra_column
		);
		$slug_view = $view_prefix . '.dashboard.' . $slug . '.' . $pivot . '.index';
		if(View::exists($slug_view)) {
			return HTMLMin::live(View::make($slug_view, $pass_to_view));
		}
		return HTMLMin::live(View::make($view_prefix . '.dashboard.models.pivot.index', $pass_to_view));
	}

	public static function processPivotModelForm($slug, $model_name, $model_id, $pivot, $pivot_name, $extra_column = array()) {
		$pivot_test = self::verifyPivot($model_name, $model_id, $pivot);
		$code = $pivot_test['code'];
		if($code != 200) {
			return App::abort($code, $pivot_test['data']);
		}
		$model = $pivot_test['data'];
		$rules = array($pivot => 'pivot:' . $pivot_name);
		$validator = Validator::make(Input::only($pivot), $rules, CustomValidator::getOwnMessages());
		$validator->setAttributeNames(Lang::get('l-press::labels'));
		if($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		}
		$pivot_ids = Input::get($pivot);
		if(count($extra_column) > 0) {
			for($i=count($pivot_ids);$i>0;) {
				$pivot_data[$pivot_ids[--$i]] = $extra_column;
			}
		}
		else {
			$pivot_data = $pivot_ids;
		}
		$model->{$pivot}()->sync($pivot_data);
		$route_prefix = (new PrefixGenerator)->getPrefix();
		$dashboard_route = '+' . Config::get('lpress::settings.dashboard_route');
		$prefix = $route_prefix . '/' . $dashboard_route;
		return Redirect::to($prefix . '/' . $slug);
	}


	public static function getModelIndex($slug, $model_name, $per_page) {
		extract(self::prepareMake());
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		$collection = $model_name::paginate($per_page);
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => Lang::get('l-press::headers.model_management', array('model' => $model_basename)),
			'collection' => $collection,
			'new_model' => new $model_name()
		);
		$slug_view = $view_prefix . '.dashboard.' . $slug . '.index';
		if(View::exists($slug_view)) {
			return HTMLMin::live(View::make($slug_view, $pass_to_view));
		}
		return HTMLMin::live(View::make($view_prefix . '.dashboard.models.index', $pass_to_view));
	}

	public static function getModelTrash($slug, $model_name, $per_page) {
		extract(self::prepareMake());
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		$collection = $model_name::onlyTrashed()->paginate($per_page);
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => Lang::get('l-press::headers.model_trash_bin', array('model' => $model_basename)),
			'collection' => $collection
		);
		$slug_view = $view_prefix . '.dashboard.' . $slug . '.trash';
		if(View::exists($slug_view)) {
			return HTMLMin::live(View::make($slug_view, $pass_to_view));
		}
		return HTMLMin::live(View::make($view_prefix . '.dashboard.models.trash', $pass_to_view));
	}

	public static function hasPermission() {
		return Auth::user()->isRoot();
	}

	public static function getModels() {
		$namespace = __NAMESPACE__;
		$base_class = $namespace . '\\BaseModel';
		$result = array();
		$autoload_path = dirname(PATH) . '/vendor/autoload.php';
		foreach (ClassLoader::getClasses($autoload_path, $namespace) as $class) {
			if (is_subclass_of($class, $base_class))
				$result[] = $class;
		}
		return $result;
	}

	public static function prepareMake() {
		if(!defined('THEME')) {
			echo Lang::get('l-press::errors.httpStatus500');
			die;
		}
		$view_prefix = 'l-press::themes.' . THEME . '.templates';
		$macro_loader = new MacroLoader;
		$macro_loader->loadMacros();
		$macro_loader->loadMacros('form');
		$macro_loader->loadMacros('blade');
		return array("view_prefix" => $view_prefix, "site" => Site::find(SITE));
	}
}
