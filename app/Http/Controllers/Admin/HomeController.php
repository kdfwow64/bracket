<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use App\Providers\HomeServiceProvider;
use Illuminate\Http\Request;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return array
     */
    public function index() {
        try {
            $return = HomeServiceProvider::getDashboardDetails();
            return view('admin.home', ['dashboard_data' => $return]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Get filter by date data of analytics to show in dashboard
     * @param Request $request
     * @return JSON
     */
    public function filterAnalyticsData(Request $request) {
        try {
            $return = HomeServiceProvider::getDashboardDetails($request->all());
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Function to download analytics of dater in CSV. Declared file name on the basis of selection of filter
     * @param Request $request
     * @return boolean
     */
    public function downloadExcel(Request $request) {
        try {
            $data = HomeServiceProvider::getDashboardDetails($request->all());
            if (!empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
                $file_name = 'dater_analytics_from_' . $request['from_date'] . '_to_' . $request['to_date'];
            } else {
                $file_name = 'dater_analytics';
            }
            return HomeServiceProvider::downloadExcel($data, $file_name);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Get the details og in app analytics in dashboard
     * @return array
     */
    public function inAppAnalyticsData(Request $request) {
        try {
            $return = HomeServiceProvider::getInAppDetails($request->all());
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Function to download analytics of inapp in CSV. Declared file name on the basis of selection of filter
     * @param Request $request
     * @return boolean
     */
    public function inAppDownloadExcel(Request $request) {
        try {
            $data = HomeServiceProvider::getInAppDetails($request->all());
            if (!empty($request['from_date']) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
                $file_name = 'inapp_analytics_from_' . $request['from_date'] . '_to_' . $request['to_date'];
            } else {
                $file_name = 'inapp_analytics';
            }
            return HomeServiceProvider::inAppDownloadExcel($data, $file_name);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

}
