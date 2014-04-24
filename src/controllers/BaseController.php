<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

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

	public static function checkSSL($type = 'all') {
		switch($type) {
			case 'dashboard': {
				$config = 'l-press::dashboard_require_ssl';
				break;
			}
			case 'login': {
				$config = 'l-press::login_require_ssl';
				break;
			}
			default: {
				$config = 'l-press::require_ssl';
			}
		}
		if(Config::get($config) && !Request::secure()) {
			if(!Config::get('l-press::ssl_is_sha2') || UserAgent::supportsSHA2()) {
				return Redirect::secure(Request::getRequestUri());
			}
			return Redirect::route('lpress-sha2');
		}
	}

	public static function getAttributeString($attributes) {
		$attribute_string = '';
		if(is_array($attributes) && count($attributes) > 0) {
			foreach($attributes as $attribute => $value) {
				$attribute_string .= " ${attribute}='${value}'";
			}
		}
		return $attribute_string;
	}

	public static function getValidationError($name) {
		$error = "";
		if(Session::has('errors')) {
			$errors = Session::get('errors');
			if($errors->has($name)) {
				$error = $errors->first($name, " <span class='error'>:message</span>");
			}
		}
		return $error;
	}

	public static function getAssetPath($attachment = FALSE) {
		$path = '';
		if($attachment) {
			$attachment_config = Config::get('l-press::attachments');
			$attachment_path_base = $attachment_config['path_base'];
			switch($attachment_path_base) {
				case 'package': {
					$path = PATH . '/';
					break;
				}
				case 'laravel': {
					$path = base_path() . '/';
					break;
				}
				default: {
					$path = $attachment_path_base . '/';
				}
			}
		}
		else {
			$theme_config = Config::get('l-press::themes');
			$theme_path_base = $theme_config['path_base'];
			switch($theme_path_base) {
				case 'package':
					$path = PATH . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				case 'laravel':
					$path = base_path() . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				default:
					$path = $theme_path_base . '/' . $theme_config['path'] . '/' . THEME . '/assets';
			}
		}
		return $path;
	}

	public static function prepareMake() {
		if(!defined('THEME')) {
			echo Lang::get('l-press::errors.httpStatus500');
			die;
		}
		$view_prefix = 'l-press::themes.' . THEME . '.templates';
		self::setMacros();
		return array("view_prefix" => $view_prefix, "site" => Site::find(SITE));
	}

	public static function slugsToRoute($path) {
		$route = new \stdClass;
		$route->throw404 = FALSE;
		$route->json = FALSE;
		$route_prefix = (new PrefixGenerator)->getPrefix();
		if(!empty($route_prefix)) {
			$real_path = explode($route_prefix, $path);
			$real_path = $real_path[1];
		}
		else {
			$real_path = $path;
		}
		$segments = preg_split('@/@', $real_path, NULL, PREG_SPLIT_NO_EMPTY);
		$slugs = array();
		$slug_types = array();
		$last_index = count($segments) - 1;
		$last_slug = $segments[$last_index];
		$last_slug = explode('.', $last_slug);
		if(count($last_slug) > 1 && $last_slug[1] == 'json') {
			$segments[$last_index] = $last_slug[0];
			$route->json = TRUE;
		}
		if($last_index > 0) {
			$base_index = $last_index - 1;
			// process base path first since last item has more complex logic
			for($i=$base_index;$i>=0;$i--) {
				$slug = $segments[$i];
				$record_type = RecordType::where('slug', '=', $slug)->first();
				if(count($record_type) === 0) {
					$route->throw404 = TRUE;
					break;
				}
				$parent = RecordType::find($record_type->parent_id);
				if($i > 0) {
					if($parent->depth > 0 && $parent->slug != $segments[$i-1]) {
						$route->throw404 = TRUE;
					}
				}
				else {
					$route->root_record_type = $parent;
				}
				if($i == $base_index) {
					$base_type = $record_type;
				}
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
			}
			if(!$route->throw404) {
				$found = FALSE;
				$slug = $segments[$last_index];
				$record_type = RecordType::where('slug', '=', $slug)
				->where('parent_id', '=', $base_type->id)
				->first();
				if(count($record_type) > 0) {
					array_unshift($slug_types, 'record_type');
					array_unshift($slugs, $slug);
					$route->record_type = $record_type;
					$found = TRUE;
				}
				if(!$found) {
					$record = Record::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($record) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $record;
					}
				}
				if(!$found) {
					$symlink = Symlink::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($symlink) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $symlink->record;
					}
				}
				if($found) {
					$route->slug_types = $slug_types;
					$route->slugs = $slugs;
				}
				else {
					$route->throw404 = TRUE;
				}
			}
		}
		else {
			$found = FALSE;
			$slug = $segments[$last_index];
			$record_type = RecordType::where('slug', '=', $slug)
			->where('depth', '=', 1)
			->first();
			if(count($record_type) > 0) {
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
				$route->record_type = $record_type;
				$found = TRUE;
			}
			if($found) {
				$route->slug_types = $slug_types;
				$route->slugs = $slugs;
			}
			else {
				$route->throw404 = TRUE;
			}
		}
		return $route;
	}

	public static function getBoolInput($key) {
		return Input::has($key) ? Input::get($key) : NULL;
	}

	public static function setMacros() {
		Form::macro('text_input', function($type, $name, $label, $value, $attributes = array()) {
			$error = self::getValidationError($name);
			$attribute_string = self::getAttributeString($attributes);
			$value = Input::old($name, $value);
			if($type == 'textarea') {
				$size = "";
				if(!array_key_exists('cols', $attributes)) {
					$size .= " cols='50' ";
				}
				if(!array_key_exists('rows', $attributes)) {
					$size .= " rows='10' ";
				}
				$input = "<textarea id='${name}' name='${name}' ${size} ${attribute_string}>${value}</textarea>";
			}
			else {
				$input = "<input id='${name}' name='${name}' type='${type}' value='${value}' ${attribute_string} />";
			}
			return "<div class='text'><label for='${name}'>${label}${error}</label>$input</div>";
		});
		Form::macro('checkbox_input', function($name, $label, $value, $attributes = array()) {
			$error = self::getValidationError($name);
			$value = Input::old($name, $value) ? " checked='checked'" : "";
			return "
				<div class='checkbox'>
					<label for='${name}' class='checkbox'>
						<input id='${name}' name='${name}' class='checkbox' type='checkbox'" . $value . self::getAttributeString($attributes) . " />
						<span unselectable='on' class='checkbox-label' data-for='${name}'>${label}${error}</span>
					</label>
				</div>
			";
		});
		Form::macro('select_input', function($name, $label, $options, $selected, $attributes = array()) {
			$html = "
				<div class='select'>
					<label for='${name}' class='select'>${label}</label>
					<select id='${name}' name='${name}'" . self::getAttributeString($attributes) . ">
			";
			foreach($options as $value => $label) {
				$attributes = $selected == $value ? " selected='selected'" : "";
				$html .= "<option value='${value}'${attributes}>${label}</option>";
			}
			$html .= "</select></div>";
			return $html;
		});
		Form::macro('file_input', function($slug, $upload_command = 'create', $single = TRUE, $value = '', $attributes = array()) {
			$type = RecordType::where('slug', '=', $slug)->first();
			if(count($type) === 0) {
				return "<div class='error'>" . Lang::get('l-press::errors.missingRecordType', array('slug' => $slug)) . "</div>";
			}
			$label = Lang::get('l-press::labels.file_select', array('type' => $type->label));
			$file_path = $slug;
			$url_path = $slug;
			while($type->depth > 1) {
				$type = $type->parent_type()->first();
				$attachment_type = $type->slug;
				$file_path = $attachment_type . '/' . $file_path;
				$url_path = $attachment_type . '/' . $url_path;
			}
			$site = Site::find(SITE);
			$root_type = $type->parent_type()->first();
			$file_path = $root_type->slug . '/' . $site->domain . '/' . $file_path;
			if ($root_type->slug != 'attachments') {
				return "<div class='error'>" . Lang::get('l-press::errors.invalidRecordType', array('slug' => $slug)) . "</div>";
			}
			$prefix_generator = new PrefixGenerator;
			$prefix = $prefix_generator->getPrefix();
			$prefix_generator->setType('dashboard');
			$dashboard_prefix = $prefix_generator->getPrefix();
			$url_path = $prefix . '/' . $url_path;
			$url = $dashboard_prefix . "/upload?path=${file_path}/&uri=${url_path}/&upload_command=${upload_command}";
			$attribute_string = self::getAttributeString($attributes);
			$token = csrf_token();
			$class = $single ? 'single file' : 'multiple file';
			$data = "data-token='${token}' data-attachment_type='${attachment_type}' data-prefix='${dashboard_prefix}' data-path='${url_path}' data-url='${url}'";
			$hidden_name = $attributes['data-target_id'];
			$value = Input::old($hidden_name, $value);
			$hidden = "<input id='${hidden_name}' name='${hidden_name}' type='hidden' value='${value}' />";
			return "<div class='file'><a href='#${slug}' title='${label}'  class='${class}' ${data} ${attribute_string}>${label}</a>${hidden}</div>";
		});
		Form::macro('icon_button', function($label, $type = 'button', $attributes = array(), $icon_class = '') {
			$icon = '';
			$icon_font = new IconFont;
			$icon = $icon_font->getIcon($icon_class);
			if($icon !== '') {
				$icon = "<span class='button-icon ${icon_class}'>$icon</span>";
			}
			return "<button type='$type'" . self::getAttributeString($attributes) . ">$icon<span class='button-label'>$label</span></button>";
		});
		HTML::macro('asset', function($type, $path, $attributes = array()) {
			$asset_domain = Config::get('l-press::asset_domain');
			$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
			$route_prefix = (new PrefixGenerator)->getPrefix();
			$open = '';
			$close = '';
			switch($type) {
				case 'css': {
					$path = "css/" . PRODUCTION . '/' . $path;
					$open .= "<link rel='stylesheet' type='text/css' href='//" . $asset_domain . $route_prefix ."/+assets/" . $path . "?v";
					$close .= "'>";
					break;
				}
				case 'js': {
					$path = "js/" . PRODUCTION . '/' . $path;
					$open .= "<script type='text/javascript' src='//" . $asset_domain . $route_prefix . "/+assets/" . $path . "?v";
					$close .= "'></script>";
					break;
				}
				case 'img': {
					$open .= "<img src='//" . $asset_domain . $route_prefix . "/+assets" . $path . "?v";
					$close .= "'" . self::getAttributeString($attributes) . "/>";
					break;
				}
				default: {
					break;
				}
			}
			$version = '';
			$version = @filemtime(self::getAssetPath() . '/' . $path);
			if($version == '') {
				$close = "' data-err='$path could not be found" . $close;
			}
			return $open . $version . $close;
		});
		Form::macro('model_delete', function($url) {
			$html = Form::open(array('url' => $url));
			$html .= Form::icon_button(
				Lang::get('l-press::labels.delete_button'),
				'submit',
				array(
					'class' => 'button',
					'name' => '_method',
					'value' => 'DELETE'
				),
				'fa-trash-o'
			);
			$html .= Form::close();
		});
		Form::macro('model_form', function($model, $url = NULL) {
			if(is_null($url)) {
				$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
				$url = $dashboard_prefix . '/' . $model->getTable() . '/create';
			}
			$html = Form::open(array('url' => $url));
			$columns = $model->getColumns();
			$rules = $model->getRules();
			$special = $model->getSpecialInputs();
			foreach($columns as $column) {
				$property = $column['name'];
				$label = $column['label'];
				$type = $column['type'];
				$value = $model->$property;
				if(array_key_exists($property, $special)) {
					$type = explode(':', $special[$property]);
					if(count($type) > 0) {
						switch($type[0]) {
							case 'attachment': {
								$attachment_html = Form::file_input($type[1], 'create', FALSE, $value, array('data-target_id' => $property));
								break;
							}
							default: {
								$text_type = $type[1];
							}
						}
					}
					$type = $type[0];
				}
				else {
					if(array_key_exists($property, $rules)) {
						$rule = explode('|', $rules[$property]);
					}
					else {
						$rule = array();
					}
					if(Str::endsWith($property, '_id')) {
						$related_property = substr($property, 0, -3);
						$namespace = 'EternalSword\\LPress\\';
						$label = Lang::get("l-press::labels.${related_property}", array(), 'en');
						$class = $namespace.str_replace(' ', '', $label);
						if(class_exists($class) && is_subclass_of($class, $namespace.'BaseModel')) {
							$items = $class::all();
							if(in_array('required', $rule)) {
								$options_list = array();
							}
							else {
								$options_list = array(Lang::get('l-press::labels.null_option'), 'NULL');
							}
							$type = 'selection';
							foreach($items as $item) {
								$options_list[$item->id] = $item->label;
							}
							if(is_null($value)) {
								reset($options_list);
								$value = key($options_list);
							}
						}
					}
				}
				switch($type) {
					case 'attachment': {
						$html .= $attachment_html;
						break;
					}
					case 'selection': {
						$label .= Lang::get('l-press::labels.label_separator');
						$html .= Form::select_input($property, $label, $options_list, $value, array());
						break;
					}
					case 'boolean': {
						$attributes = array();
						$html .= Form::checkbox_input($property, $label, $value, $attributes);
						break;
					}
					default: {
						$label .= Lang::get('l-press::labels.label_separator');
						if(!isset($text_type)) {
							$text_type = 'text';
						}
						$html .= Form::text_input($text_type, $property, $label, $value, array());
					}
				}
			}
			$html .= "<div class='submit'>";
			$html .= Form::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button'), 'fa-check');
			$html .= "</div>";
			$html .= Form::close();
			return $html;
		});
		HTML::macro('new_model_link', function($model) {
			$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
			$url = $dashboard_prefix . '/' . $model->getTable() . '/create';
			$label = Lang::get('l-press::labels.new_model');
			return "<a href='${url}' class='create model' data-model='" . get_class($model) . "'>${label}</a>";
		});
		HTML::macro('trash_bin_link', function($model) {
			$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
			$url = $dashboard_prefix . '/' . $model->getTable() . '/trash';
			$label = Lang::get('l-press::labels.trash');
			return "<a href='${url}' class='trash model' data-model='" . get_class($model) . "'>${label}</a>";
		});
		HTML::macro('collection_editor', function($collection, $type = 'standard') {
			$rows = array();
			$html = "<ul class='collection'>";
			$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
			$icon_font = new IconFont;
			$trash_icon = $icon_font->getIcon('fa-trash-o');
			$trash_title = Lang::get('l-press::labels.delete_button');
			$edit_icon = $icon_font->getIcon('fa-pencil-square-o');
			$edit_title = Lang::get('l-press::labels.update_button');
			$restore_icon = $icon_font->getIcon('fa-undo');
			$restore_title = Lang::get('l-press::labels.restore_button');
			foreach($collection as $model) {
				$url = $dashboard_prefix . '/' . $model->getTable() . '/' . $model->id;
				$html .= "<li class='item'><span class='label'>" . $model->label . "</span>";
				switch($type) {
					case 'trash': {
						$html .= "<a href='${url}/delete?type=force' class='button-icon force delete' title='${trash_title}'>$trash_icon</a>";
						$html .= "<a href='${url}/restore' class='button-icon restore' title='${restore_title}'>$restore_icon</a>";
						break;
					}
					default: {
						$html .= "<a href='${url}/delete' class='button-icon delete' title='${trash_title}'>$trash_icon</a>";
						$html .= "<a href='${url}' class='button-icon' title='${edit_title}'>$edit_icon</a>";
					}
				}
				$html .= "</li>";
			}
			$html .= "</ul>";
			return $html;
		});
		HTML::macro('url', function($url, $text = null, $attributes = array()) {
			$attribute_string = '';
			$has_title = FALSE;
			if(is_array($attributes) && count($attributes) > 0) {
				foreach($attributes as $attribute => $value) {
					if($attribute == 'title') {
						$title = $value;
						$has_title = TRUE;
					}
					else {
						$attribute_string .= " ${attribute}='${value}'";
					}
				}
			}
			$text = is_null($text) ? $url : $text;
			$title = $has_title ? $title : $text;
			return "<a href='${url}' title='${title}'${attribute_string}>${title}</a>";
		});
		HTML::macro('imageAlt', function($record) {
			if(is_array($record)) {
				$alt = $record['label'];
				foreach($record['values'] as $value) {
					if($value['field']['slug'] == 'file-description') {
						$alt = $value['current_revision']['contents'];
					}
				}
			}
			else {
				$alt = $record->label;
				foreach($record->values as $value) {
					if($value->field->slug == 'file-description') {
						$alt = $value->current_revision->contents;
					}
				}
			}
			return $alt;
		});
		Blade::extend(function($value) {
			return preg_replace('/\{\$(.+)\$\}/', '<?php ${1} ?>', $value);
		});
	}
}
