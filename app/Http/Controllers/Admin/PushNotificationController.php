<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\PushNotificationRequest;
use App\Providers\NotificationServiceProvider;
use Session;
use Auth;
use Illuminate\Support\Facades\Input;

class PushNotificationController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', ['except' => ['sendPushNotificationByAdmin']]);
    }

    /**
     * Controller function to view all notification send by admin
     * @return array
     */
    public function index() {
        try {
            return view('admin.view-push-notifications');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function to view all notification send by admin
     * @return JSON
     */
    public function pushNotificationListByAjax() {
        try {
            $return = NotificationServiceProvider::getNotificationsByAdmin();
            return json_encode($return);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for view the form of push notification
     * @return array
     */
    public function create() {
        try {
            return view('admin.add-push-notification');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Controller function for sending the push notification
     * @return array
     */
    public function store(PushNotificationRequest $request) {
        try {
            $request['sender_id'] = Auth::user()->id;
            CommonUtility::sendPushNotification($request->all());
            Session::flash('status_sent', trans('messages.admin.push_notification_sent'));
            return redirect('admin/push-notification');
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Send push notification by admin to all user by background script
     * @return boolean
     */
    public function sendPushNotificationByAdmin() {
        $data = Input::all();
        return NotificationServiceProvider::sendNotificationByAdmin($data);
    }

    /**
     * Get the users list who received notifications
     * @param integer $id
     * @return array
     */
    public function show($id) {
        try {
            $receivers = NotificationServiceProvider::getNotificationReceivers($id);
            if ($receivers) {
                return view('admin.notification-recipients', ['notification_recipients' => $receivers]);
            } else {
                Session::flash('status', trans('messages.admin.recipient_not_found'));
                return redirect('/admin/push-notification');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

    /**
     * Get the users list who received notifications
     * @param object $request
     * @return array
     */
    public function notificationRecipientListByAjax(Request $request) {
        try {
            $receivers = NotificationServiceProvider::getNotificationReceivers($request->user_id);
            if ($receivers) {
                return json_encode($receivers);
            } else {
                Session::flash('status', trans('messages.admin.recipient_not_found'));
                return redirect('/admin/push-notification');
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            return $this->responseServerError(trans('messages.error.exception'));
        }
    }

}
