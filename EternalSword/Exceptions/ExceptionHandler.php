<?php namespace EternalSword\Exceptions;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use EternalSword\Controllers\BaseController;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;

class ExceptionHandler {

	public static function renderError($status_code, $message) {
		extract(BaseController::prepareMake());
		return HTMLMin::live(Response::view($view_prefix . '.errors', array(
			'view_prefix' => $view_prefix,
			'title' => 'HttpError: ' + $status_code,
			'status_code' => $status_code,
			'message' => $message
		), $status_code));
	}

	public static function renderTokenMismatchException() {
		$status_code = 403;
		$message = Lang::get('l-press::errors.tokenMismatch');
		extract(BaseController::prepareMake());
		return HTMLMin::live(Response::view($view_prefix . '.errors', array(
			'view_prefix' => $view_prefix,
			'title' => 'HttpError: ' + $status_code,
			'status_code' => $status_code,
			'message' => $message
		), $status_code));
	}

	public function render($request, Exception $e) {
		if($e instanceof HttpException) {
			return self::renderHttpException($e);
		}
		if($e instanceof TokenMismatchException) {
			return self::renderTokenMismatchException($e);
		}
		extract(BaseController::prepareMake());
		$message = Lang::get('l-press::errors.httpStatus500');
		if(Request::ajax()) {
			$json = new \stdClass;
			$json->error = $message;
			return Response::json($json, $status_code);
		}
		return HTMLMin::live(Response::view($view_prefix . '.errors', array(
			'view_prefix' => $view_prefix,
			'title' => 'HttpError: ' + 500,
			'status_code' => 500,
			'message' => $message
		), $status_code));
	}

}
