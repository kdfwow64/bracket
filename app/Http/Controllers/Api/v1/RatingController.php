<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\RatingServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\UserRateRequest;

/**
 * RatingController class contains methods for user rating
 */
class RatingController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => ['index', 'checkCumulativeRating']]);
        $this->middleware('apiAuth', ['except' => ['checkCumulativeRating']]);
        parent:: __construct();
    }

    /**
     * index method to fetch user for rating
     * @param Request $request
     * @return type JSON
     */
    public function index(Request $request) {
        try {
            $response = RatingServiceProvider::fetchUserForRating($request->user_id);
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
     * save user rating
     * @param RateUserRequest $request
     * @return type JSON
     */
    public function store(UserRateRequest $request) {
        try {
            $request_data = json_decode($request->getContent(), true);
            $response = RatingServiceProvider::rateUser($request_data, $request->user_id);
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
