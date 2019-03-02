<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\PushServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\PushNotificationRequest;

/**
 * PushNotificationController class contains methods for push management
 */
class PushNotificationController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => ['index', 'destroy', 'update']]);
        $this->middleware('apiAuth', ['except' => ['']]);
        parent:: __construct();
    }

    /**
     * index method to list push
     * @param Request $request
     * @return type JSON
     */
    public function index(Request $request) {
        try {
            $response = PushServiceProvider::fetchPushList($request->user_id);
            $response = $this->responseSuccess($response['message'], $response['result']);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * soft delete push
     * @param Request $request
     * @return type JSON
     */
    public function destroy(PushNotificationRequest $request) {
        try {
            $response = PushServiceProvider::deletePush($request->user_id, $request->push_id);
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

    /**
     * update push read  flag
     * @param Request $request
     * @return type JSON
     */
    public function update(PushNotificationRequest $request) {
        try {
            $response = PushServiceProvider::markPushRead($request->user_id, $request->push_id);
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
