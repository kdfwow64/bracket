<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Config;

/**
 * Controller class contains methods for response management
 */
class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    protected $status_code;
    protected $response;

    public function __construct() {
        $this->status_code = Config::get('codes');
    }

    /*
     * method to return success response
     */

    public function responseSuccess($message = '', $data = array()) {
        $response = array(
            'status' => 1,
            'statusCode' => $this->status_code['success'],
            'message' => $message,
            'result' => (object) Controller::convertToCamelCase($data),
        );
        return response()->json($response, $this->status_code['success']);
    }

    /*
     * method to return custom response
     */

    public function responseCustom($statusCode, $message = '', $data = array()) {
        $response = array(
            'status' => 0,
            'statusCode' => $statusCode,
            'message' => $message,
            'result' => (object) $data,
        );
        return response()->json($response, $statusCode);
    }

    /*
     * method to return unauthorized response
     */

    public function responseUnauthorized($message = '', $data = array()) {
        $response = array(
            'status' => 0,
            'statusCode' => $this->status_code['unauthorized'],
            'message' => $message,
            'result' => (object) $data,
        );
        return response()->json($response, $this->status_code['unauthorized']);
    }

    /*
     * method to return bad request response
     */

    public function responseBadRequest($message = '', $data = array()) {

        $response = array(
            'status' => 0,
            'statusCode' => $this->status_code['bad_request'],
            'message' => $message,
            'result' => (object) $data,
        );

        return response()->json($response, $this->status_code['bad_request']);
    }

    /*
     * method to return response not found
     */

    public function responseNotFound($message = '', $data = array()) {
        $response = array(
            'status' => 0,
            'statusCode' => $this->status_code['not_found'],
            'message' => $message,
            'result' => (object) $data,
        );
        return response()->json($response, $this->status_code['not_found']);
    }

    /*
     * method to return server error response
     */

    public function responseServerError($message = '', $data = array()) {
        $response = array(
            'success' => 0,
            'statusCode' => $this->status_code['server_error'],
            'message' => $message,
            'result' => (object) $data,
        );
        return response()->json($response, $this->status_code['server_error']);
    }

    /*
     * method to convert response key from lower to camel case
     */

    protected function convertToCamelCase($array) {
        $converted_array = [];
        foreach ($array as $old_key => $value) {
            if (is_array($value)) {
                $value = $this->convertToCamelCase($value);
            } else if (is_object($value)) {
                if (($value instanceof Model) || (method_exists($value, 'toArray'))) {
                    $value = $value->toArray();
                } else {
                    $value = (array) $value;
                }
                $value = $this->convertToCamelCase($value);
            }
            $converted_array[camel_case($old_key)] = $value;
        }
        return $converted_array;
    }

}
