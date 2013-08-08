<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\Response;
	use Illuminate\Support\Facades\Config;
	use EternalSword\LPress\ThirdParty\Blueimp\Uploader\UploadHandler as UploadHandler;

	class UploadController extends BaseController {
		private $configuration_error = "Error: couldn't load upload options from configuration so aborting upload.";

		protected function getOptions() {
			$options = array();
			$options['upload_dir'] = parent::getAssetPath(TRUE);
			$asset_domain = Config::get('l-press::asset_domain');
			$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
			$route_prefix = self::getRoutePrefix();
			$options['upload_url'] = "//" . $asset_domain . $route_prefix ."/uploads/";

			return $options;
		}

		public function postFile($filename) {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}

			$options['filename'] = $filename;

			$handler = new UploadHandler($options);
			$response = Response::make(json_encode($handler->post(FALSE)), 200);
			$response->headers->set('Content-Type', 'application/json');

			return $response;
		}

		public function getURL() {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}

			$handler = new UploadHandler($options);
			$response = Response::make(json_encode($handler->get(FALSE)), 200);
			$response->headers->set('Content-Type', 'application/json');

			return $response;
		}

		public function deleteFile() {
			$options = $this->getOptions();
			if(!$options) {
				return Response::make($this->configuration_error, 500);
			}

			$options['filename'] = $filename;

			$handler = new UploadHandler($options);
			$response = Response::make(json_encode($handler->delete(FALSE)), 200);
			$response->headers->set('Content-Type', 'application/json');

			return $response;
		}
	}
