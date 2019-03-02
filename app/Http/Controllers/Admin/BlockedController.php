<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use App\Providers\BlockedServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BlockedController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Controller function for list of all blocked daters
     * @return array
     */
    public function index() {
        try {
            $return = BlockedServiceProvider::getBlockedUnblockedUser();
            return view('admin.view-blocked-users', ['blocked_users' => $return]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of blocked users by pagination
     * @return JSON
     */
    public function blockedUserListByAjax() {
        try {
            $return = BlockedServiceProvider::getBlockedUnblockedUser();
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view profile of the blocked user
     * @param integer $id
     * @return array
     */
    public function show($id) {
        try {
            $user = BlockedServiceProvider::getBlockedUnblockedUserDetail($id);
            if ($user['total'] != 0) {
                return view('admin.view-blocked-user-details', ['blocked_by_users' => $user]);
            } else {
                Session::flash('status', trans('messages.admin.user_not_found'));
                return redirect('/admin/blocked-user');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view profile of the blocked user by pagination
     * @param Request $request
     * @return JSON
     */
    public function blockedByUserListByAjax(Request $request) {
        try {
            $user = BlockedServiceProvider::getBlockedUnblockedUserDetail($request->id);
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
     * Controller function to filter blocked user by pagination
     * @param Request $request
     * @return JSON
     */
    public function searchBlockedByUserList(Request $request) {
        try {
            $user = BlockedServiceProvider::getDateFilterBlockedUnblockedUsers($request->all());
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
