<?php

namespace App\Http\Controllers\Api\v1;

use App\Providers\QuestionServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * QuestionController class contains methods for question management
 */
class QuestionController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => ['index', 'getInsertQuestions']]);
        $this->middleware('apiAuth', ['except' => ['getInsertQuestions']]);
        parent:: __construct();
    }

    /**
     * to fetch questions list
     * @param Request $request
     * @return type JSON
     */
    public function index() {
        try {
            $response = QuestionServiceProvider::fetchQestionsList();
            if ($response['success'] === 1) {
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

}
