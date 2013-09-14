<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\Response;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;
	use EternalSword\LPress\ThirdParty\Blueimp\Uploader\UploadHandler as UploadHandler;

	class UploadController extends BaseController {
		private $configuration_error = "Error: couldn't load upload options from configuration so aborting upload.";

		protected function getOptions() {
			$options = array();
			$options['image_versions'] = array();
			$asset_path = parent::getAssetPath(TRUE);
			$options['upload_dir'] = $asset_path . Input::get('path');
			$asset_domain = Config::get('l-press::asset_domain');
			$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
			$route_prefix = parent::getRoutePrefix();
			$options['script_url'] = "//" . DOMAIN . $route_prefix . '/upload';
			$uri = Input::get('uri');
			$options['upload_url'] = "//" . $asset_domain . $uri ;
			return $options;
		}

		public function postFile() {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}
			$handler = new UploadHandler($options, FALSE);
			return Response::json($handler->post(FALSE));
		}

		public function getURL() {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}
			$handler = new UploadHandler($options);
			return Response::json($handler->get(FALSE));
		}

		public function deleteFile() {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}
			$options['filename'] = Input::get('file');
			$handler = new UploadHandler($options);
			return Response::json($handler->delete(FALSE));
		}
	}
