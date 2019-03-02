<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use App\Providers\WildcardServiceProvider;
use Illuminate\Http\Request;

class WildcardController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Controller function for list of all wildcard daters
     * @return array
     */
    public function index() {
        try {
            return view('admin.view-wildcard-users');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of wildcard users by pagination
     * @return JSON
     */
    public function wildcardUserListByAjax() {
        try {
            $return = WildcardServiceProvider::getWildcardUser();
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to filter wildcard user by pagination
     * @param Request $request
     * @return JSON
     */
    public function searchWildcardUserList(Request $request) {
        try {
            $user = WildcardServiceProvider::getDateFilterWildcardUsers($request->all());
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
