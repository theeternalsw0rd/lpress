<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class BaseController extends Controller {

	protected static function processModelForm($model_name, $id = NULL) {
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
		$model->saveItem($action);
		if(Session::has('model_post_redirect')) {
			$url = Session::get('model_post_redirect');
			Session::forget('model_post_redirect');
		}
		else {
			$url = URL::previous();
		}
		return Redirect::to($url);
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
		$url = URL::previous();
		$url = preg_replace('/\/[0-9]+$/', '', $url);
		return Redirect::to($url);
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
			return View::make($slug_view, $pass_to_view);
		}
		return View::make($view_prefix . '.dashboard.models.form', $pass_to_view);
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
			return View::make($slug_view, $pass_to_view);
		}
		return View::make($view_prefix . '.dashboard.models.index', $pass_to_view);
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
			return View::make($slug_view, $pass_to_view);
		}
		return View::make($view_prefix . '.dashboard.models.trash', $pass_to_view);
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
