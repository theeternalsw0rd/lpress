<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class DashboardController extends BaseController {
	public static function getDashboard() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		if($user->email == 'admin@lpress.local') {
			return Redirect::route('lpress-dashboard.installer');
		}
		$is_root = $user->isRoot();
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => Lang::get('l-press::titles.dashboard'),
			'user' => $user,
			'is_root' => $is_root
		);
		if($is_root) {
			$sites = Site::take(5)->get();
			$pass_to_view['sites'] = $sites;
			$pass_to_view['new_site'] = new Site;
		}
		return View::make($view_prefix . '.dashboard.index', $pass_to_view);
	}

	protected static function getModelInfo($slug) {
		$models = parent::getModels();
		foreach($models as $model_name) {
			$instance = new $model_name();
			if($instance->getTable() == $slug) {
				$controller = $model_name . 'Controller';
				if(!class_exists($controller)) {
					$controller = __NAMESPACE__ . '\\BaseController';
				}
				return array('controller' => $controller, 'model_name' => $model_name);
			}
		}
		return FALSE;
	}

	public static function routeGetAction($slug, $id = NULL) {
		$manager = FALSE;
		if(is_null($id)) {
			$manager = TRUE;
		}
		if($id == 'create' || is_null($id)) {
			$id = NULL;
		}
		else {
			if(!is_numeric($id)) {
				App::abort(422, Lang::get('l-press::errors.invalidIdFormat'));
			}
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === FALSE) {
			return App::abort(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission()) {
			return App::abort(403, Lang::get('l-press::errors.permissionError'));
		}
		if($manager) {
			return $controller::getModelIndex($slug, $model_name, 15);
		}
		return $controller::getModelForm($slug, $model_name, $id);
	}

	public static function routePostAction($slug) {
		if($id == 'create') {
			$id = NULL;
		}
		else {
			if(!is_numeric($id)) {
				App::abort(422, Lang::get('l-press::errors.invalidIdFormat'));
			}
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === FALSE) {
			return App::abort(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission($id)) {
			return App::abort(403, Lang::get('l-press::errors.executePermissionError'));
		}
		return $controller::processModelForm($slug, $model_name, $id);
	}
}
