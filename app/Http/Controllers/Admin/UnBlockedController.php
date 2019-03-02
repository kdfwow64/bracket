<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use App\Providers\BlockedServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class UnBlockedController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Controller function for list of all unblocked daters
     * @return array
     */
    public function index() {
        try {
            $return = BlockedServiceProvider::getBlockedUnblockedUser(Config::get('constants.unblocked_flag'));
            return view('admin.view-blocked-users', ['blocked_users' => $return, 'flag' => Config::get('constants.unblocked_flag')]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of unblocked users by pagination
     * @return JSON
     */
    public function unBlockedUserListByAjax() {
        try {
            $return = BlockedServiceProvider::getBlockedUnblockedUser(Config::get('constants.unblocked_flag'));
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view profile of the unblocked user
     * @param integer $id
     * @return array
     */
    public function show($id) {
        try {
            $user = BlockedServiceProvider::getBlockedUnblockedUserDetail($id, Config::get('constants.unblocked_flag'));
            if ($user['total'] != 0) {
                return view('admin.view-blocked-user-details', ['blocked_by_users' => $user, 'flag' => 1]);
            } else {
                Session::flash('status', trans('messages.admin.user_not_found'));
                return redirect('/admin/unblocked-user');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view profile of the unblocked user by pagination
     * @param Request $request
     * @return JSON
     */
    public function unBlockedByUserListByAjax(Request $request) {
        try {
            $user = BlockedServiceProvider::getBlockedUnblockedUserDetail($request->id, Config::get('constants.unblocked_flag'));
            if ($user) {
                return json_encode($user);
            } else {
                Session::flash('status', trans('messages.admin.user_not_found'));
                return redirect('/admin/user');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to filter unblocked user by pagination
     * @param Request $request
     * @return JSON
     */
    public function searchUnBlockedByUserList(Request $request) {
        try {
            $user = BlockedServiceProvider::getDateFilterBlockedUnblockedUsers($request->all(), Config::get('constants.unblocked_flag'));
            if ($user) {
                return json_encode($user);
            } else {
                Session::flash('status', trans('messages.admin.user_not_found'));
                return redirect('/admin/user');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

}
