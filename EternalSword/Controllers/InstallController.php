<?php namespace EternalSword\Controllers;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use EternalSword\Lib\PrefixGenerator;
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
use EternalSword\Lib\HTMLMin;

class InstallController extends BaseController {
	public function getInstaller() {
		extract(parent::prepareMake());	
		$user = Auth::user();
		if($user && $user->username == 'lpress') {
			$route_prefix = (new PrefixGenerator)->getPrefix();
			$dashboard_prefix = '+' . Config::get('lpress::settings.dashboard_route');
			$form_url = $route_prefix . $dashboard_prefix . '/users/1';
			return HTMLMin::html(View::make($view_prefix . '.dashboard.installer',
				array(
					'view_prefix' => $view_prefix,
					'title' => Lang::get('l-press::titles.newModel', array('model_basename' => 'User')),
					'install' => true,
					'form_url' => $form_url
				)
			));
		}
		if($user && $user->username != 'lpress') {
			return Redirect::route('lpress-dashboard')->with(
				'std_errors',
				array(Lang::get('l-press::errors.applicationAlreadyInstalled'))
			);
		}
	}
}
