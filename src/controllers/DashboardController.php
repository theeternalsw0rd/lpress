<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Auth;
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
			'title' => 'Dashboard',
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

	public static function routeAction($slug, $id = NULL) {
		$manager = FALSE;
		if(is_null($id)) {
			$manager = TRUE;
		}
		if($id == 'create' || is_null($id)) {
			$id = NULL;
		}
		else {
			if(!is_numeric($id)) {
				App::abort(422, "Path must end with either create, or the id number of a model.");
			}
		}
		$models = parent::getModels();
		foreach($models as $model_name) {
			$instance = new $model_name();
			if($instance->getTable() == $slug) {
				$controller = $model_name . 'Controller';
				if(!class_exists($controller)) {
					$controller = __NAMESPACE__ . '\\BaseController';
				}
				if($manager) {
					return $controller::getModelIndex($slug, $model_name, 15);
				}
				return $controller::getModelForm($slug, $model_name, $id);
			}
		}
		App::abort(404, "Could not find model.");
	}
}
