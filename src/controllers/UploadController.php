<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UploadController extends BaseController {
	protected $configuration_error = "Couldn't load upload options from configuration so aborting upload.";
	protected $notfound_error = "Valid RecordType could not be located from uri path.";
	protected $permission_error = "You do not have permission to upload files.";
	protected $record_error = "Record id not passed. Cannot process request.";
	protected $command_error = "Upload command (create/edit) not passed. Cannot process request.";

	protected function getOptions() {
		$options = array();
		$options['image_versions'] = array();
		$asset_path = parent::getAssetPath(TRUE);
		$date = new \DateTime();
		$date = $date->format('Y/m/');
		$options['upload_dir'] = $asset_path . Input::get('path') . $date;
		$asset_domain = Config::get('l-press::asset_domain');
		$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
		$route_prefix = parent::getRoutePrefix();
		$options['script_url'] = "//" . DOMAIN . $route_prefix . '/+upload';
		$uri = Input::get('uri');
		$options['upload_url'] = "//" . $asset_domain . $uri;
		return $options;
	}

	protected function placeFile($destination_path, &$file_path, &$file_name) {
		$file = Input::file('file');
		$increment = 2;
		$random = '';
		do {
			$extension = $file->getClientOriginalExtension();
			$file_name = Str::slug(basename($file->getClientOriginalName(), $extension)) . $random . '.' . $extension;
			$file_path = $destination_path . $file_name;
			$random = '-' . $increment++;
		} while (File::exists($file_path));
		return Input::file('file')->move($destination_path, $file_name);
	}

	protected function processFile($user, $route, $handler) {
		$options = $this->getOptions();
		$json = new \stdClass;
		$file_path = '';
		$file_name = '';
		if($this->placeFile($options['upload_dir'], $file_path, $file_name)) {
			$uri = Input::get('uri');
			$mime_types = explode('/', $uri);
			$mime_handler = new MimeHandler($file_path, $file_name, $mime_types[0]);
			$mime = $mime_handler->getMime();
			$status_code = $mime_handler->getStatusCode();
			switch($status_code) {
				case 200: {
					$record = call_user_func_array(
						$handler,
						array($file_path, $user)
					);
					$record = $record->toArray();
					$json->record = $record;
					$json->uri = $uri;
					$json->status_code = $status_code;
					return Response::json($json, $status_code);
				}
				case 403: {
					File::delete($file_path);
					$json->error = $mime . ' is not allowed by the current RecordType.';
					return Response::json($json, $status_code);
				}
				default: {
					File::delete($file_path);
					$json->error = 'An unexpected error occurred verifying the mimetype.';
					return Response::json($json, $status_code);
				}
			}
		}
		$json->record = NULL;
		$json->status_code = 500;
		$json->error = 'An unexpected error occurred saving the record.';
		return Response::json($json, $status_code);
	}

	protected function verifyRecord($user, $route) {
		if(Input::has('record')) {
			$record_id = Input::get('record');
			$record = is_int($record_id) ? Record::find($record_id) : Record::find(0);
			if($record->isEmpty()) {
				$status_code = 404;
				$json->status_code = $status_code;
				$json->error = $this->notfound_error;
				return $json;
			}
			$status_code = 200;
			$json->status_code = $status_code;
			$json->original_record = $record;
			return $json;
		}
		$status_code = 500;
		$json->status_code = $status_code;
		$json->error = $this->record_error;
		return $json;
	}

	protected function processPost($user, $route) {
		$json = new \stdClass;
		$command = Input::get('upload_command');
		$handler = __NAMESPACE__ . '\RecordController::' . $command . 'AttachmentRecord';
		if(is_callable($handler) && $user->hasPermission($command)) {
			if($command == 'edit') {
				$json = $this->verifyRecord($user, $route);
				$status_code = $json->status_code;
				if($status_code == 200) {
					return $this->processFile($user, $route, $handler, $json);
				}
				return Response::json($json, $status_code);
			}
			return $this->processFile($user, $route, $handler);
		}
		$status_code = 500;
		$json->status_code = $status_code;
		$json->error = $this->command_error;
		return Response::json($json, $status_code);
	}

	public function postFile() {
		$user = Auth::user();
		$user->load('groups.permissions');
		$json = new \stdClass;
		$route = parent::slugsToRoute(Input::get('uri'));
		if($route->throw404) {
			$status_code = 404;
			$json->error = $this->notfound_error;
			$json->status_code = $status_code;
			return Response::json($json, $status_code);
		}
		if(Input::has('upload_command')) {
			return $this->processPost($user, $route);
		}
		$json->error = $this->command_error;
		$status_code = 500;
		$json->status_code = $status_code;
		return Response::json($json, $status_code);
	}

	public function deleteFile() {
		$options = $this->getOptions();
		if(!$options) {
			return Response::json($this->configuration_error, 500);
		}
		$file_path = $options['upload_dir'] . '/' . Input::get('file');
		return Response::json(File::delete($file_path));
	}
}
