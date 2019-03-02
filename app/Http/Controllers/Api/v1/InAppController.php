<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\InAppServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\InAppRequest;
use DB;

/**
 * InApp Controller class contains methods for chat thread
 */
class InAppController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => []]);
        $this->middleware('apiAuth', ['except' => []]);
        parent:: __construct();
    }

    /**
     * show method to fetch user particular chat thread
     * @param Request $request
     * @return type JSON
     */
    public function store(InAppRequest $request) {
        try {
            DB::beginTransaction();
            $response = InAppServiceProvider::inAppPurchase($request->user_id, $request->all());
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

}
