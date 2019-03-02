<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use App\Providers\InAppServiceProvider;

class InAppController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Controller function for list of in-app purchases
     * @return array
     */
    public function index() {
        try {
            $total_purchases = InAppServiceProvider::getTotalPurchases();
            return view('admin.in-app-purchase', ['total_purchases' => $total_purchases]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of in-app purchase by pagination
     * @return JSON
     */
    public function inAppUserListByAjax() {
        try {
            $return = InAppServiceProvider::getSubscriptionUsers();
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

}
