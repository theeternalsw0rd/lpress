<?php namespace EternalSword\Exceptions;

use Exception;
use \Illuminate\Contracts\Config\Repository as Configuration;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Debug\ExceptionHandler as BaseExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use EternalSword\Controllers\BaseController;
use GrahamCampbell\HTMLMin\Facades\HTMLMin;

class ExceptionHandler extends BaseExceptionHandler {

	public function __construct(Configuration $config, LoggerInterface $log) {
		parent::__construct($config, $log);
	}

	public static function renderHttpException(HttpException $exception) {
		extract(BaseController::prepareMake());
		$status_code = $exception->getStatusCode();
		$message = $exception->getMessage() ?: Lang::get('l-press::errors.httpStatus500');
		if(Request::ajax()) {
			$json = new \stdClass;
			$json->error = $message;
			return Response::json($json, $status_code);
		}
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
		if(Request::ajax()) {
			$json = new \stdClass;
			$json->error = $message;
			return Response::json($json, $status_code);
		}
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
