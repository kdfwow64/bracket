<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\BracketServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\v1\BracketCreateRequest;
use App\Http\Requests\Api\v1\BracketUpdateRequest;
use App\Http\Requests\Api\v1\BracketRequest;
use Illuminate\Http\Request;

/**
 * BracketController class contains methods for bracket management
 */
class BracketController extends Controller {

    public function __construct() {

        $this->middleware('validateJson', ['except' => ['store', 'shareBracket', 'getServerTime']]);
        $this->middleware('apiAuth', ['except' => ['getServerTime']]);
        parent:: __construct();
    }

    /**
     * Create bracket
     * @param BracketRequest $request
     * @return type JSON
     */
    public function store(BracketCreateRequest $request) {
        try {
            DB::beginTransaction();
            $response = BracketServiceProvider::createBracket($request->all(), $request->user_id);
            if ($response['success'] === 1) {
                DB::commit();
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * Update bracket
     * @param BracketUpdateRequest $request
     * @return type JSON
     */
    public function updateBracket(BracketUpdateRequest $request) {
        try {
            DB::beginTransaction();
            $response = BracketServiceProvider::updateBracket($request->all(), $request->user_id);
            if ($response['success'] === 1) {
                DB::commit();
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * Send push to wild card members bracket
     * @param BracketRequest $request
     * @return type JSON
     */
    public function sendPushToWildCards(BracketRequest $request) {
        try {
            $response = BracketServiceProvider::sendPushToWildCards($request->all(), $request->user_id);
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

    /*
     * share bracket on facebook
     * @param Request $request
     * @return type JSON
     */

    public function shareBracket(Request $request) {
        try {
            $response = BracketServiceProvider::shareBracket($request->user_id);
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
     * return server current time
     * @return type JSON
     */
    public function getServerTime() {
        try {
            $response = BracketServiceProvider::getServerCurrentTime();
            $response = $this->responseSuccess($response['message'], $response['result']);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

}
