<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Bracket;
use App\Models\PushNotification;
use DB;
use App\Utility\CommonUtility;
use App\Models\Transaction;
use App\Models\Receipt;

/**
 * NotificationServiceProvider class contains methods for notification management
 */
class NotificationServiceProvider extends BaseServiceProvider {

    public static function bracketRoundStartPush() {
        $users = User::select('id as user_id', DB::raw("DATE_FORMAT(CONVERT_TZ(now(),'+00:00',tz_diff_sec),'%H:%i') as now"), 'is_paid')
                ->where('role', 2)
                ->where('is_push_notification', 1)
                ->where('is_profile_completed', 1)
                ->havingRaw("now >= '11:00' and now <= '16:00'")
                ->get();

        if (!empty($users)) {
            $running_bracket_ids = Bracket::where('is_completed', 0)->pluck('user_id')->toArray();
            $push_data = config('constants.push_notification');
            foreach ($users as $user) {
                if (in_array($user->user_id, $running_bracket_ids) && $user->is_paid == 0) {
                    // send 2 and 4 pm
                    if ($user->now == '14:00') {
                        $push_data['seven']['sender_user_id'] = NULL;
                        $push_data['seven']['receiver_user_id'] = $user->user_id;
                        PushNotification::sendPushNotification($push_data['seven']['sender_user_id'], $push_data['seven']['receiver_user_id'], $push_data['seven']);
                    } else if ($user->now == '16:00') {
                        $push_data['eight']['sender_user_id'] = NULL;
                        $push_data['eight']['receiver_user_id'] = $user->user_id;
                        PushNotification::sendPushNotification($push_data['eight']['sender_user_id'], $push_data['eight']['receiver_user_id'], $push_data['eight']);
                    }
                } else {
                    // send 11 am
                    if ($user->now == '11:00') {
                        $login_user = User::find($user->user_id);
                        $bracket_constants = config('constants.bracket');
                        if ($login_user->free_bracket_count > 0 || $login_user->earn_bracket_count > 0 || $login_user->paid_bracket_count > 0) {
                            $users = Bracket::createBracket($login_user, $bracket_constants, $user->user_id);
                            if (count($users) == $bracket_constants['members_count']) {
                                $push_data['six']['sender_user_id'] = NULL;
                                $push_data['six']['receiver_user_id'] = $user->user_id;
                                if ($login_user->is_paid == 0) {
                                    $push_data['six']['push_message'] = $push_data['six']['push_message_1'];
                                    $push_data['six']['message'] = $push_data['six']['message_1'];
                                } else {
                                    $push_data['six']['push_message'] = $push_data['six']['push_message_2'];
                                    $push_data['six']['message'] = $push_data['six']['message_2'];
                                }
                                PushNotification::sendPushNotification($push_data['six']['sender_user_id'], $push_data['six']['receiver_user_id'], $push_data['six']);
                            }
                        }
                    }
                }
            }
        }

        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = array('user' => $users);
        return static::$response;
    }

    /**
     * Function to send push notification from admin
     * @param array $request
     */
    public static function sendNotificationByAdmin($request) {
        $payload = array();
        $payload['message'] = htmlspecialchars_decode(htmlspecialchars_decode($request['notification_message']));
        $payload['title'] = htmlspecialchars_decode(htmlspecialchars_decode($request['notification_title']));
        $payload['thread_id'] = NULL;

        $type = config('constants.push_notification.admin.type');

        if ($request['send_to_radios'] == 'all') {
            $users = User::fetchUserForNotification('all');
            $payload['type'] = $type . '1';
        }
        if ($request['send_to_radios'] == 'male') {
            $users = User::fetchUserForNotification('male');
            $payload['type'] = $type . '2';
        }
        if ($request['send_to_radios'] == 'female') {
            $users = User::fetchUserForNotification('female');
            $payload['type'] = $type . '3';
        }
        if ($request['send_to_radios'] == 'selected_users') {
            $users = $request['js_users'];
            $payload['type'] = $type . '4';
        }
        if ($request['send_to_radios'] == 'selected_location') {
            foreach ($request['js_location'] as $userid) {
                $location[] = User::getLocationNameByUser($userid);
            }
            $users = User::fetchUserForNotification($location);
            $payload['type'] = $type . '5';
        }
        foreach ($users as $user) {
            PushNotification::sendPushNotification($request['sender_id'], $user, $payload);
        }
    }

    /**
     * View notifications send by admin
     */
    public static function getNotificationsByAdmin() {
        $type = config('constants.push_notification.admin.sub_type');
        return PushNotification::getNotificationsByAdmin($type);
    }

    /**
     * Get recipients of the notification
     * @param integer $id
     * @return array
     */
    public static function getNotificationReceivers($id) {
        return PushNotification::getNotificationsReceivers($id);
    }

    /**
     * cron job method to send thread termination push
     */
    public static function chatThreadUltimatumPush() {
        $users = DB::select("
            select now(), time_to_sec(timediff(end_time_for_response,now())) / 60 as winner_diff,
            time_to_sec(timediff(start_time_for_request,now())) / 60 as dater_diff,
            user_id, winner_user_id, chat_window.id, u1.first_name as winner_first_name, 
            u2.first_name as dater_first_name, end_time_for_response, start_time_for_request
            from `chat_window`
            inner join `user` as `u1` on `u1`.`id` = `chat_window`.`winner_user_id`
            inner join `user` as `u2` on `u2`.`id` = `chat_window`.`user_id`
            where u1.is_push_notification = 1 and u2.is_push_notification = 1 and
            u1.deleted_at is null and u2.deleted_at is null and
            chat_window.deleted_at is null having (dater_diff >= 116 and dater_diff <= 120 )
            or (winner_diff >= 116 and winner_diff <= 120) "
        );
        if (!empty($users)) {
            $push_data = config('constants.push_notification.eleven');
            $name = '';
            foreach ($users as $user) {
                $push_data['sender_user_id'] = NULL;
                if (($user->winner_diff >= 116) && ($user->winner_diff <= 120)) {
                    $name = $user->dater_first_name;
                    $push_data['receiver_user_id'] = $user->winner_user_id;
                } else if (($user->dater_diff >= 116) && ($user->dater_diff <= 120)) {
                    $name = $user->winner_first_name;
                    $push_data['receiver_user_id'] = $user->user_id;
                }
                $push_data['push_message'] = $push_data['push_message_1'] . $name . $push_data['push_message_2'];
                $push_data['message'] = $push_data['message_1'] . $name . $push_data['message_2'];
                $push_data['thread_id'] = $user->id;
                PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
            }
        }
        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = array('user' => $users);
        return static::$response;
    }

    public static function bracketCountManager() {
        //maintain users daily free bracket count
        $free_users = DB::select("select id, datediff(now(),daily_cron_time) as diff from user 
            where is_paid = 0 and role = 2 and deleted_at is null having diff >= 1");
        $paid_users_id = $free_users_id = array();
        if (!empty($free_users)) {
            $free_users_id = array_column($free_users, 'id');
            User::whereIn('id', $free_users_id)
                    ->update(['free_bracket_count' => 1, 'daily_cron_time' => DB::raw('now()')]);
        }

        //maintain paid users daily free bracket count
        $paid_users = DB::select("select id, datediff(now(),daily_cron_time) as diff from user 
            where is_paid = 1 and deleted_at is null and role = 2
            having diff >= 1");

        if (!empty($paid_users)) {
            $paid_users_id = array_column($paid_users, 'id');
            User::whereIn('id', $paid_users_id)
                    ->update(['free_bracket_count' => 2, 'daily_cron_time' => DB::raw('now()')]);
        }

        if (!empty($free_users) || !empty($paid_users)) {
            //marking daily there bracket as complete
            $users_id = array_merge($free_users_id, $paid_users_id);
            Bracket::where('is_completed', 0)
                    ->where('type', 0)
                    ->whereIn('user_id', $users_id)
                    ->update(['is_completed' => 1]);
        }

        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = array('daily_users' => $free_users, 'paid_users' => $paid_users);
        return static::$response;
    }

    public static function inAppReceiptValidator() {
        //daily validating for receipt validation
        $environment = config('environment.in_app_environment');
        if ($environment == 'sandbox') {
            $transactions = DB::select("
            select t1.*, r1.id as receipt_id, r1.receipt_data,
            time_to_sec(timediff(now(),t1.end_date)) as diff          
            from transaction t1 join user u1 on u1.id = t1.user_id 
            join receipt r1 on r1.id = t1.receipt_id
            where t1.created_at = ( select MAX(t2.created_at) 
            from transaction t2 where t2.user_id = t1.user_id)
            having diff >= -30  and diff <= 30");
        } else {
            $transactions = DB::select("
            select t1.*, r1.id as receipt_id, r1.receipt_data,
            datediff(now(),t1.end_date) as diff            
            from transaction t1 join user u1 on u1.id = t1.user_id 
            join receipt r1 on r1.id = t1.receipt_id
            where t1.created_at = ( select MAX(t2.created_at) 
            from transaction t2 where t2.user_id = t1.user_id)
            having diff = 0 and diff <=2");
        }

        foreach ($transactions as $transaction) {
            $response = CommonUtility::validateInAppReceipt($environment, $transaction->receipt_data);
            if ($response['status'] == 1) {
                $latest_receipt_info = end($response['receipt_data']->latest_receipt_info);
                $end_date = CommonUtility::generateDate($latest_receipt_info->expires_date_ms);
                if ($end_date != $transaction->end_date) {
                    Receipt::where('subscription_id', $transaction->subscription_id)
                            ->where('id', $transaction->receipt_id)
                            ->update(['receipt_data' => $response['receipt_data']->latest_receipt]);
                    $transaction_data = array(
                        'subscription_id' => $transaction->subscription_id,
                        'receipt_id' => $transaction->receipt_id,
                        'start_date' => $transaction->end_date,
                        'end_date' => $end_date,
                        'user_id' => $transaction->user_id
                    );
                    Transaction::insert($transaction_data);
                } else {
                    NotificationServiceProvider::setBracketCount($transaction->user_id);
                }
            } else {
                NotificationServiceProvider::setBracketCount($transaction->user_id);
            }
        }
        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = array('monthly_users' => $transactions);
        return static::$response;
    }

    public static function setBracketCount($user_id) {
        $count = Bracket::where('user_id', $user_id)
                ->where('type', 0)
                ->whereRaw('Date(created_at) = CURDATE()')
                ->count();
        if ($count >= 1) {
            User::where('id', $user_id)->update([
                'free_bracket_count' => 0,
                'is_paid' => 0
            ]);
        } else {
            User::where('id', $user_id)->update([
                'free_bracket_count' => 1,
                'is_paid' => 0
            ]);
        }
        return true;
    }

}
