<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use Illuminate\Http\Request;
use App\Providers\UserServiceProvider;
use App\Http\Requests\Admin\ChangePasswordRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Auth;

class UserController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', ['except' => ['resetPassword', 'sendUserExcelMail']]);
        parent:: __construct();
    }

    /**
     * Reset password for admin
     * @param object $request
     * @return void
     */
    public function resetPassword(Request $request) {
        UserServiceProvider::resetPassword($request->all());
        return redirect('admin/login');
    }

    /**
     * Controller function for change password page
     * @return void
     */
    public function changePassword() {
        return view('admin.change-password');
    }

    /**
     *  Controller function for change password form submit
     * @param ChangePasswordRequest $request
     * @return void
     */
    public function changePasswordSubmit(ChangePasswordRequest $request) {
        try {
            UserServiceProvider::changeAdminPassword($request->all());
            return redirect('admin/change-password');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of all users
     * @return array
     */
    public function index() {
        try {
            $return = UserServiceProvider::getUser();
            return view('admin.view-users', ['projects' => $return]);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of searched users
     * @param array $request
     * @return JSON
     */
    public function searchUser(Request $request) {
        try {
            $return = UserServiceProvider::getSearchUser($request->all());
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of users by pagination
     * @return JSON
     */
    public function userListByAjax() {
        try {
            $return = UserServiceProvider::getUser();
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Download dters in CSV
     * @return JSON
     */
    public function downloadDatersCSV() {
        try {
            $pagination = 'on';
            $loggedinEmail = Auth::user()->email;
            $data['email'] = $loggedinEmail;
            CommonUtility::sendUserExcelMail($data);
            Session::flash('status_sent', trans('messages.admin.excel_mail_sent'));
            return redirect('admin/user');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Send user Excel mail to admin by background script
     * @return boolean
     */
    public function sendUserExcelMail() {
        try {
            ini_set('max_execution_time', 0);
            $data = Input::all();
            $pagination = 'on';
            $reqData = UserServiceProvider::getUser($pagination);
            UserServiceProvider::downloadDaterExcel($data, $reqData);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view profile of the user
     * @param integer $id
     * @return array
     */
    public function show($id) {
        try {
            $user = UserServiceProvider::getUserDetail($id);
            if ($user) {
                $user_arr = $user->toArray();
                return view('admin.user-profile', ['projects' => $user_arr]);
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
     * Controller function to view user Rating List
     * @param array $request
     * @return JSON
     */
    public function userRatingList(Request $request) {
        try {
            $user_rating = UserServiceProvider::userRatingList($request->all());
            return json_encode($user_rating);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for list of ratings date filtered by pagination
     * @return JSON
     */
    public function searchUserRatingList(Request $request) {
        try {
            $return = UserServiceProvider::getDateFilterRating($request->all());
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

}
