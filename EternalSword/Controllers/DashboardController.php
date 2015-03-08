<?php namespace EternalSword\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use EternalSword\Models\BaseModel;
use EternalSword\Models\Comment;
use EternalSword\Models\Field;
use EternalSword\Models\FieldType;
use EternalSword\Models\Group;
use EternalSword\Models\Permission;
use EternalSword\Models\Record;
use EternalSword\Models\RecordType;
use EternalSword\Models\Revision;
use EternalSword\Models\Site;
use EternalSword\Models\Symlink;
use EternalSword\Models\Theme;
use EternalSword\Models\User;
use EternalSword\Models\Value;
use EternalSword\Exceptions\ExceptionHandler;
use EternalSword\Lib\HTMLMin;

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
		if($is_root || $user->hasPermission('user-manager')) {
			$users = User::take(5)->get();
			$pass_to_view['users'] = $users;
			$pass_to_view['new_user'] = new User;
		}
		return HTMLMin::html(View::make($view_prefix . '.dashboard.index', $pass_to_view));
	}

	protected static function getModelInfo($slug) {
		$models = parent::getModels();
		foreach($models as $model_name) {
			$instance = new $model_name();
			if($instance->getTable() == $slug) {
				$controller = str_replace('Models', 'Controllers', $model_name . 'Controller');
				if(!class_exists($controller)) {
					$controller = __NAMESPACE__ . '\\BaseController';
				}
				return array('controller' => $controller, 'model_name' => $model_name);
			}
		}
		return false;
	}

	public static function routeGetAction($slug, $id = NULL) {
		$manager = false;
		$trash = false;
		if(is_null($id)) {
			$manager = true;
		}
		if($id == 'create' || is_null($id)) {
			$id = NULL;
		}
		else {
			if(!is_numeric($id)) {
				if($id != 'trash') {
					return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
				}
				$trash = true;
			}
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission()) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.permissionError'));
		}
		if($trash) {
			return $controller::getModelTrash($slug, $model_name, 15);
		}
		if($manager) {
			return $controller::getModelIndex($slug, $model_name, 15);
		}
		return $controller::getModelForm($slug, $model_name, $id);
	}

	public static function routePostAction($slug, $id) {
		if($id == 'create') {
			$id = NULL;
		}
		else {
			if(!is_numeric($id)) {
				return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
			}
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission($id)) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.executePermissionError'));
		}
		$redirect_url = $controller::getRedirect($id);
		return $controller::processModelForm($model_name, $redirect_url, $id);
	}

	public static function routeRestoreAction($slug, $id) {
		if(!is_numeric($id)) {
			return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission($id)) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.executePermissionError'));
		}
		return $controller::restore($model_name, $id);
	}

	public static function routeDeleteAction($slug, $id) {
		if(!is_numeric($id)) {
			return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission($id)) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.executePermissionError'));
		}
		return $controller::delete($model_name, $id);
	}

	public static function routeGetPivotAction($slug, $id, $pivot) {
		if(!is_numeric($id)) {
			return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		$pivot_info = self::getModelInfo($pivot);
		if(!$controller::hasPermission($id)) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.executePermissionError'));
		}
		return $controller::getPivotEditor($slug, $model_name, $id, $pivot, $pivot_info['model_name']);
	}

	public static function routePostPivotAction($slug, $id, $pivot) {
		if(!is_numeric($id)) {
			return ExceptionHandler::renderError(422, Lang::get('l-press::errors.invalidIdFormat'));
		}
		$model_info = self::getModelInfo($slug);
		if($model_info === false) {
			return ExceptionHandler::renderError(404, Lang::get('l-press::errors.modelNotFound', array('slug' => $slug)));
		}
		$controller = $model_info['controller'];
		$model_name = $model_info['model_name'];
		if(!$controller::hasPermission($id)) {
			return ExceptionHandler::renderError(403, Lang::get('l-press::errors.executePermissionError'));
		}
		$pivot_info = self::getModelInfo($pivot);
		return $controller::processPivotModelForm($slug, $model_name, $id, $pivot, $pivot_info['model_name']);
	}
}
