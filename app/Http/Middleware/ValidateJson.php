<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

/**
 * class to validate request JSON.
 */
class ValidateJson {

    protected $router;

    function __construct(Router $router) {
        $this->router = $router;
    }

    public function handle($request, Closure $next) {
        $this->jsonData = json_decode($request->getContent(), true);
        $response = array(
            'success' => 0,
            'message' => '',
            'statusCode' => '',
        );
        if (empty($this->jsonData)) {
            $response['message'] = trans('messages.error.empty_json');
            $response['statusCode'] = Config::get('codes.pre_condition_fail');
            return response()->json($response);
        } else {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $error = ''; // JSON is valid
                    break;
                case JSON_ERROR_DEPTH:
                    $error = trans('messages.JSON_ERROR_DEPTH');
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = trans('messages.JSON_ERROR_STATE_MISMATCH');
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = trans('messages.JSON_ERROR_CTRL_CHAR');
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = trans('messages.JSON_ERROR_SYNTAX');
                    break;
                // only PHP 5.3+
                case JSON_ERROR_UTF8:
                    $error = trans('messages.JSON_ERROR_UTF8');
                    break;
                default:
                    $error = 'Unknown JSON error occured.';
                    break;
            }
            if ($error !== '') {
                $response['message'] = $error;
                $response['statusCode'] = Config::get('codes.invalid_json');
                return response()->json($response);
            }
            return $response = $next($request);
        }
    }

}
