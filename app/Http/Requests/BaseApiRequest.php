<?php

namespace App\Http\Requests;

use Symfony\Component\HttpFoundation\Response;

/**
 * This class is base class for all API request
 * @author Shyam
 */
class BaseApiRequest extends Request {

    protected $response = null;

    /**
     * Get data to be validated from the request.
     * This method is used to get JSON input for APIs and validate the data
     * @return array
     */
    protected function validationData() {
        $postData = Request::all();
        return $postData;
    }

    /**
     * This method is used to send custom response when validation fails
     * @param array $errors
     * @return type
     */
    public function response(array $errors) {
        if (!$this->wantsJson()) {
            return parent::response($errors);
        } else {
            if (!$this->isJson()) {
                $this->response['result'] = (object) Array();
                $this->response['message'] = trans('messages.error.invalid_json');
                $this->response['statusCode'] = Response::HTTP_BAD_REQUEST;
                return \Illuminate\Support\Facades\Response::json($this->response, Response::HTTP_BAD_REQUEST)->header('Content-Type', "application/json");
            } else {
                $first_error = '';
                foreach ($errors as $error) {
                    $first_error = $error[0];
                    break;
                }
                $this->response['success'] = 0;
                $this->response['statusCode'] = Response::HTTP_PRECONDITION_FAILED;
                $this->response['message'] = $first_error;
                $this->response['result'] = (object) Array();
                return \Illuminate\Support\Facades\Response::json($this->response, Response::HTTP_PRECONDITION_FAILED)->header('Content-Type', "application/json");
            }
        }
    }

}
