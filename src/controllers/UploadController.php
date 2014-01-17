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
	protected $type_error = "Type of upload (new/update) not passed. Cannot process request.";

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
		$options['mkdir_mode'] = 0770;
		return $options;
	}

	public function postFile() {
		$user = Auth::user();
		$user->load('groups.permissions');
		$json = new \stdClass;
		$code = 403;
		$json->error = $this->permission_error;
		$route = parent::slugsToRoute(Input::get('uri'));
		if($route->throw404) {
			$code = 404;
			$json->error = $this->notfound_error;
		}
		else {
			$record_type = $route->record_type;
			if(Input::has('type')) {
				switch(Input::get('type')) {
					case 'new': {
						if($user->hasPermission('create')) {
							$code = 200;
						}
						break;
					}
					case 'update': {
						if(Input::has('record')) {
							$record_id = Input::get('record');
							$record = is_int($record_id) ? Record::find($record_id) : Record::find(0);
							if($record->isEmpty()) {
								$code = 404;
								$json->error = $this->notfound_error;
							}
							else {
							}
						}
						else {
							$code = 500;
							$json->error = $this->record_error;
						}
						break;
					}
					default: {
						$code = 500;
						$json->error = $this->type_error;
					}
				}
				$options = $this->getOptions();
				if(!$options) {
					$code = 500;
					$json->error = $this->configuration_error;
				}
				if($code == 200) {
					$records = array();
					$statuses = array();
					$destination_path = $options['upload_dir'];
					$file = Input::file('file');
					$increment = 2;
					$random = '';
					do {
						$extension = $file->getClientOriginalExtension();
						$file_name = Str::slug(basename($file->getClientOriginalName(), $extension)) . $random . '.' . $extension;
						$file_path = $destination_path . $file_name;
						$random = '-' . $increment++;
					} while (File::exists($file_path));
					$upload_success = Input::file('file')->move($destination_path, $file_name);
					if($upload_success) {
						$record = RecordController::createAttachmentRecord($file_path, $user)->toArray();
						$status = 200;
					}
					else {
						$record = NULL;
						$status = 500;
					}
					$json->record = $record;
					$json->uri = Input::get('uri');
					$json->status = $status;
					unset($json->error);
				}
			}
			else {
				$code = 500;
				$json->error = $this->type_error;
			}
		}
		return Response::json($json, $code);
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
