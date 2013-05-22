<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Session;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\HTML;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;

	class InstallController extends BaseController {
		public function getInstaller() {
			$user = Auth::user();
			if($user && $user->username == 'lpress') {
				$view_prefix = 'l-press::themes.' . THEME;
				parent::setMacros();
				return View::make($view_prefix . '.installer.install',
					array(
						'view_prefix' => $view_prefix,
						'title' => 'Update root user'
					)
				);
			}
		}
	}
