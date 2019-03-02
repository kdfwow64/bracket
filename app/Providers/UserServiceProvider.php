<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\User;
use App\Models\BlockedUser;
use App\Models\Rating;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Utility\CommonUtility;
use App\Models\PushNotification;
use App\Models\ChatWindow;
use Excel;

/**
 * UserServiceProvider class contains methods for user management
 */
class UserServiceProvider extends BaseServiceProvider {

    /**
     * fetch user data and insert device info
     * @param type JSON
     * @return array
     */
    public static function postSignIn($data) {
        $facebook_id = $data['facebookId'];
        $device_type = $data['deviceInfo']['deviceType'];
        $device_token = $data['deviceInfo']['deviceToken'];
        $user = User::getUserProfile($facebook_id, config('constants.search.facebook_id'), 3);
        if (!is_object($user)) {
            $user = User::createUser($data);
            $token = Device::registerDevice($user->user_id, $device_type, $device_token);
            User::sendPushCompleteProfile($user->user_id);
            static::$response['message'] = trans('messages.success.sign_up');
        } else {
            User::where('id', $user->user_id)->update(['first_sign_in' => config('constants.status.fail')]);
            $token = Device::registerDevice($user->user_id, $device_type, $device_token);
            static::$response['message'] = trans('messages.success.sign_in');
        }
        if (is_object($user)) {
            $push_unread_count = PushNotification::fetchUserUnreadPushCount($user->user_id);
            $chat_unread_count = ChatWindow::unReadChatThreadCount($user->user_id);
            static::$response['result'] = array('user' => $user,
                'access_token' => $token,
                'push_unread_count' => $push_unread_count,
                'chat_unread_count' => $chat_unread_count
            );
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /**
     * update user profile
     * @param type JSON
     * @return array
     */
    public static function updateUserProfile($data) {
        $user = User::updateUserProfile($data);
        if (is_object($user)) {
            $push_unread_count = PushNotification::fetchUserUnreadPushCount($user->user_id);
            $chat_unread_count = ChatWindow::unReadChatThreadCount($user->user_id);
            static::$response['result'] = array('user' => $user,
                'push_unread_count' => $push_unread_count,
                'chat_unread_count' => $chat_unread_count
            );
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /**
     * update user preference
     * @param type JSON
     * @return array
     */
    public static function updateUserPreference($data) {
        $user = User::updateUserPreference($data);
        if (is_object($user)) {
            static::$response['result'] = array('user' => $user);
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /**
     * user sign out
     * @param type char
     * @return type array
     */
    public static function userSignOut($access_token) {
        Device::unRegisterDevice($access_token);
        static::$response['success'] = config('constants.status.success');
        static::$response['message'] = trans('messages.success.user_sign_out');
        return static::$response;
    }

    /*
     * user push on/off change
     * @param type array
     * @return type array
     */

    public static function updatePushStatus($data) {
        $response = User::updatePushStatus($data['user_id'], $data['flag']);
        if ($response) {
            static::$response['message'] = trans('messages.success.user_push_status');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /*
     * delete user
     * @param type array
     * @return type array
     */

    public static function deleteUser($login_user_id, $delete_user_id) {
        if ($login_user_id == $delete_user_id) {
            $response = User::deleteUser($login_user_id);
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
            return static::$response;
        }

        if ($response) {
            static::$response['message'] = trans('messages.success.user_delete');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /*
     * view user profile
     * @param type array
     * @return type array
     */

    public static function viewProfile($user_id) {
        $user = User::getUserProfile($user_id, config('constants.search.user_id'), 3, 1);
        if (is_object($user)) {
            static::$response['result'] = array('user' => $user);
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /*
     * view facebook ids data
     * @param type array
     * @return type array
     */

    public static function fetchMutualFriends($facebook_ids, $user_id) {
        $select = User::generateUserProfileSelectStatement(3);
        $blocked_users_id = BlockedUser::where('blocked_by_user_id', $user_id)
                        ->pluck('blocked_user_id')->toArray();
        $users = User::select($select)
                ->whereNotIn('id', $blocked_users_id)
                ->whereIn('facebook_id', $facebook_ids)
                ->get();
        if (is_object($users)) {
            foreach ($users as $user) {
                $user['is_blocked'] = 0;
            }
            static::$response['result'] = array('user' => $users);
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.user_not_found');
        }
        return static::$response;
    }

    /**
     * to block unblock user
     * @param type $user_id
     * @param type $blocked_user_id
     * @param type $reason
     * @return type array
     */
    public static function blockUnblockUser($user_id, $blocked_user_id, $reason) {
        $blocked_user = BlockedUser::where('blocked_user_id', $blocked_user_id)
                ->where('blocked_by_user_id', $user_id)
                ->first();
        if (is_object($blocked_user)) {
            BlockedUser::where('blocked_user_id', $blocked_user_id)
                    ->where('blocked_by_user_id', $user_id)
                    ->delete();
            static::$response['message'] = trans('messages.success.un_blocked_done');
        } else {
            $data = array(
                'blocked_user_id' => $blocked_user_id,
                'blocked_by_user_id' => $user_id,
                'reason' => $reason
            );
            BlockedUser::insert($data);
            $thread_ids = ChatWindow::
                    where(function ($query) use ($user_id, $blocked_user_id) {
                        $query->where('user_id', $user_id)
                        ->where('winner_user_id', $blocked_user_id);
                    })
                    ->orWhere(function ($query) use ($user_id, $blocked_user_id) {
                        $query->where('user_id', $blocked_user_id)
                        ->where('winner_user_id', $user_id);
                    })
                    ->pluck('id');
            ChatWindow::whereIn('id', $thread_ids)->delete();
            PushNotification::where('type', 10)
                    ->whereIn('thread_id', $thread_ids)
                    ->delete();
            static::$response['message'] = trans('messages.success.block_done');
        }
        static::$response['success'] = config('constants.status.success');
        return static::$response;
    }

    /**
     * fetch blocked users
     * @param type $user_id
     * @return type array
     */
    public static function fetchBlockedUsers($user_id) {
        $blocked_user = User::fetchBlockedUser($user_id);
        static::$response['result'] = array('user' => $blocked_user);
        static::$response['success'] = config('constants.status.success');
        return static::$response;
    }

    /**
     * Function to reset the password
     * @param type $request
     * @return boolean
     */
    public static function resetPassword($request) {
        /* Generate random password */
        $random_password = CommonUtility::randomString(10);
        $userid = User::getUserByEmail($request['email']);
        if (!isset($userid) || empty($userid)) {
            Session::flash('status_fail', trans('messages.admin.user_not_found'));
        } else {
            User::updatePassword($userid->id, $random_password, 1);
            /* mail to user */
            $org_name = config('constants.organisation_name');
            $mail_from = config('environment.admin_email');
            Mail::send('emails.forgotPassword', ['org_name' => $org_name, 'password' => $random_password], function ($m) use ($request, $mail_from) {
                $m->from($mail_from, 'Password Reset');
                $m->to($request['email'])->subject('Bracket: New Password Request');
            });
            /* Set flash message */
            Session::flash('status', trans('messages.admin.password_reset'));
        }
        return true;
    }

    /**
     * Change admin password
     * @param Array $request
     * @return Array
     */
    public static function changeAdminPassword($request) {
        if (Hash::check($request['old_password'], Auth::user()->password)) {
            if ($request['old_password'] == $request['new_password']) {

                Session::flash('status_fail', trans('messages.admin.repeat_password_validation'));
            } else {
                User::updatePassword(Auth::user()->id, $request['new_password'], 0);
                Session::flash('status', trans('messages.admin.password_updated'));
            }
        } else {
            Session::flash('status_fail', trans('messages.admin.old_password_validation'));
        }
        return true;
    }

    /**
     * Function to get the list of users
     * @return array
     */
    public static function getUser($pagination = NULL) {
        return User::getUser($pagination);
    }

    /** Download analytics in Excel format
     * @param array $data
     * @param string $file_name
     * @return excel
     */
    public static function downloadDaterExcel($data, $reqData) {

        $fileName = 'daters_info_' . date('Y-m-d_his');
        $user_excel_mail_to = $data['email'];

        Excel::create($fileName, function($excel) use ($reqData) {
            $excel->sheet('Daters', function($sheet) use ($reqData) {
                $data_header = array('S. No.','First Name', 'Last Name', 'Email', 'Age', 'Gender', 'Place', 'School', 'Height (inches)', 'Bio', 'Registered On', 'Question 1', 'Answer 1', 'Question 2', 'Answer 2', 'Question 3', 'Answer 3');
                $sheet->fromArray(array($data_header), null, 'A1', false, false);
                for ($i = 0; $i < count($reqData); $i++) {
                    $sheet->fromArray(array(array_values($reqData[$i])), null, 'A1', false, false);
                }
                $sheet->cells('A1:Q1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setFontSize(12);
                });
            });
        })->store('csv', public_path('uploads'));
        
        $org_name = config('constants.organisation_name');
        $mail_from = config('environment.admin_email');
        $emails = array($user_excel_mail_to);
        Mail::raw('Please find the excel for users attached with this email.', function ($m) use ($mail_from, $fileName, $emails) {
            $m->from($mail_from, 'Excel attached');
            $m->to($emails)->subject('Bracket: User excel attached');
            $m->attach(public_path('/uploads') . '/' . $fileName . '.csv');
        });
        unlink(public_path('/uploads') . '/' . $fileName . '.csv');
        return true;
    }

    /**
     * Function to get the list of searched users
     * @param array $request
     * @return array
     */
    public static function getSearchUser($request) {
        if (isset($request['query']) && !isset($request['push_notification']) && $request['query'] == "") {
            $user_list = User::getUser();
        } else {
            $user_list = User::getSearchUser($request);
        }
        return $user_list;
    }

    /**
     * Function to get the profile for a user
     * @param integer $id
     * @return array
     */
    public static function getUserDetail($id) {
        return User::getUserById($id);
    }

    /**
     * Function to get the list of user ratings
     * @param array $request
     * @return array
     */
    public static function userRatingList($request) {
        $userid = $request['userid'];
        $user_ratings['users'] = Rating::getRatingForUserId($userid);
        $user_ratings['rating_array'] = config('constants.img_rating');
        return $user_ratings;
    }

    /**
     * Function to get the list of rating for the user by date filter
     * @param array $request
     * @return array
     */
    public static function getDateFilterRating($request) {
        $userid = $request['userid'];
        $from_date = $request['from_date'];
        if (isset($request['to_date'])) {
            $to_date = $request['to_date'];
        }
        if ($from_date == "NaN/NaN/NaN NaN:NaN:NaN") {
            $user_ratings['users'] = Rating::getRatingForUserId($userid);
        } else {
            $user_ratings['users'] = Rating::getDateFilterRating($userid, $from_date, $to_date);
        }
        $user_ratings['rating_array'] = config('constants.img_rating');
        return $user_ratings;
    }

    /**
     * method to update user country
     * @param type $user_id
     * @param type $data
     * @return type
     */
    public static function updateUserCountry($user_id, $data) {
        $status = User::where('id', $user_id)
                ->update(['country' => $data['country']]);
        if ($status) {
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
        }
        return static::$response;
    }

}
