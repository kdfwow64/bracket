<?php

namespace App\Providers;

use App\Models\PushNotification;
use App\Models\User;

/**
 * PushServiceProvider class contains methods for push management
 */
class PushServiceProvider extends BaseServiceProvider {
    /*
     * fetch user push list
     * @param type user_id, page_no and limit
     * @return type array
     */

    public static function fetchPushList($user_id) {
        $limit = config('constants.record_per_page');
        $push_list = PushNotification::select('id as push_id', 'sender_user_id', 'receiver_user_id', 'thread_id', 'type', 'is_read', 'message', 'title', 'created_at')
                        ->where('receiver_user_id', $user_id)
                        ->orderBy('created_at', 'desc')->paginate($limit);
        User::where('id', $user_id)->update(['push_badge' => 0]);
        static::$response['result'] = array(
            'notifications' => $push_list,
            'push_unread_count' => PushNotification::fetchUserUnreadPushCount($user_id)
        );
        return static::$response;
    }

    /*
     * delete push
     * @param type int user_id
     * @param type int push_id
     * @return type array
     */

    public static function deletePush($user_id, $push_id) {
        $response = PushNotification::deletePush($user_id, $push_id);
        $push_unread_count = PushNotification::fetchUserUnreadPushCount($user_id);
        if ($response) {
            static::$response['result'] = array('push_unread_count' => $push_unread_count);
            static::$response['message'] = trans('messages.success.push_delete');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.push_not_found');
        }
        return static::$response;
    }

    /*
     * push read
     * @param type int user_id
     * @param type int push_id
     * @return type array
     */

    public static function markPushRead($user_id, $push_id) {
        $response = PushNotification::where('receiver_user_id', $user_id)
                        ->where('id', $push_id)->update(['is_read' => 1]);
        $push_unread_count = PushNotification::fetchUserUnreadPushCount($user_id);
        if ($response) {
            static::$response['result'] = array('push_unread_count' => $push_unread_count);
            static::$response['message'] = trans('messages.success.push_update');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.push_not_found');
        }
        return static::$response;
    }

}
