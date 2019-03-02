<?php

namespace App\Providers;

use App\Models\ChatWindow;
use App\Models\PushNotification;
use DB;
use App\Models\User;
use Carbon;

/**
 * ChatThreadServiceProvider class contains methods for chat thread management
 */
class ChatServiceProvider extends BaseServiceProvider {
    /*
     * fetch dater chat thread list
     * @param type user_id, page_no and limit
     * @return type array
     */

    public static function fetchChatThreadList($user_id, $chat_thread_id = NULL) {
        $limit = config('constants.record_per_page');
        $chat_thread = ChatWindow::select(
                        'chat_window.id as chat_thread_id', 'chat_window.created_at as thread_created_at', 'winner_user_id', 'u1.first_name as winner_first_name', 'u1.last_name as winner_last_name', DB::raw("(CASE WHEN u1.profile_picture IS NOT NULL AND u1.profile_picture <>'' THEN   CONCAT('" . config('environment.s3_url') . '600X600/' . "',u1.profile_picture) ELSE u1.profile_picture END) AS winner_profile_picture"), DB::raw("(CASE WHEN u1.profile_picture IS NOT NULL AND u1.profile_picture <>'' THEN   CONCAT('" . config('environment.s3_url') . '600X600/' . "',u1.profile_picture) ELSE u1.profile_picture END) AS winner_thumb_picture"), 'u1.ejabberd_username as winner_ejabberd_username', 'u1.ejabberd_password as winner_ejabberd_password', 'end_time_for_response as winner_max_time', 'winner_offline_batch', 'chat_window.user_id as dater_user_id', 'u2.first_name as dater_first_name', 'u2.last_name as dater_last_name', DB::raw("(CASE WHEN u2.profile_picture IS NOT NULL AND u2.profile_picture !='' THEN   CONCAT('" . config('environment.s3_url') . '600X600/' . "',u2.profile_picture) ELSE u2.profile_picture END) AS dater_profile_picture"), DB::raw("(CASE WHEN u2.profile_picture IS NOT NULL AND u2.profile_picture !='' THEN   CONCAT('" . config('environment.s3_url') . '600X600/' . "',u2.profile_picture) ELSE u2.profile_picture END) AS dater_thumb_picture"), 'u2.ejabberd_username as dater_ejabberd_username', 'u2.ejabberd_password as dater_ejabberd_password', 'start_time_for_request as dater_max_time', 'user_offline_batch as dater_offline_batch', DB::raw('NOW() AS time')
                )
                ->join('user as u1', 'u1.id', '=', 'chat_window.winner_user_id')
                ->join('user as u2', 'u2.id', '=', 'chat_window.user_id')
                ->where(function ($query) use ($user_id) {
                    $query->where('chat_window.user_id', $user_id)
                    ->orWhere('chat_window.winner_user_id', $user_id);
                })
                ->orderBy('chat_window.updated_at', 'desc');

        if ($chat_thread_id) {
            $chat_thread = $chat_thread->where('chat_window.id', $chat_thread_id)->first()->toArray();
            static::$response['result'] = array(
                'chat_thread' => $chat_thread,
                'chat_unread_count' => ChatWindow::unReadChatThreadCount($user_id),
                'new_chat_count' => ChatWindow::newChatCount($user_id)
            );
        } else {
            $chat_thread_list = $chat_thread->paginate($limit);
            static::$response['result'] = array(
                'chat_thread_list' => $chat_thread_list,
                'chat_unread_count' => ChatWindow::unReadChatThreadCount($user_id),
                'new_chat_count' => ChatWindow::newChatCount($user_id)
            );
        }
        return static::$response;
    }

    /*
     * delete thread
     * @param type int user_id
     * @param type int push_id
     * @return type array
     */

    public static function deleteChatThread($user_id, $chat_thread_id) {
        $response = ChatWindow::where('id', $chat_thread_id)
                ->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)
                    ->orWhere('winner_user_id', $user_id);
                })
                ->delete();
        PushNotification::where('type', 10)->where('thread_id', $chat_thread_id)->delete();
        if ($response) {
            static::$response['message'] = trans('messages.success.chat_thread_delete');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.chat_thread_not_found');
        }
        return static::$response;
    }

    /*
     * update chat thread
     * @param type int user_id
     * @param type int chat_thread_id
     * @return type array
     */

    public static function updateChatThread($user_id, $chat_thread_id) {
        $chat_thread = ChatWindow::find($chat_thread_id);
        if ($chat_thread->user_id == $user_id) {
            $chat_thread->start_time_for_request = NULL;
        } else {
            $chat_thread->end_time_for_response = NULL;
        }
        $chat_thread->save();
        if ($chat_thread) {
            static::$response['message'] = trans('messages.success.chat_thread_updated');
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.chat_thread_not_found');
        }
        return static::$response;
    }

    public static function sendOfflinePushMessage($data) {
        $push_data = config('constants.push_notification.thirteen');
        $from_user = User::select('id', 'ejabberd_username', 'first_name')
                ->where('ejabberd_username', $data['from_ejabberd_id'])
                ->first();
        $to_user = User::select('id', 'ejabberd_username')
                ->where('ejabberd_username', $data['to_ejabberd_id'])
                ->first();
        $from_user_id = $from_user->id;
        $to_user_id = $to_user->id;

        $thread = ChatWindow::select('id')
                ->where(function ($query) use ($from_user_id, $to_user_id) {
                    $query->where('user_id', $from_user_id)
                    ->where('winner_user_id', $to_user_id);
                })
                ->orWhere(function ($query) use ($from_user_id, $to_user_id) {
                    $query->where('user_id', $to_user_id)
                    ->where('winner_user_id', $from_user_id);
                })
                ->first();

        $push_data['sender_user_id'] = $from_user_id;
        $push_data['receiver_user_id'] = $to_user_id;
        $push_data['message'] = $data['message'];
        $push_data['title'] = $from_user->first_name;
        $push_data['thread_id'] = $thread->id;
        PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
        static::$response['success'] = config('constants.status.success');
        return static::$response;
    }

    public static function clearOflineMesgBatch($user_id, $chat_thread_id) {
        $user = User::find($user_id);
        $chat_thread = ChatWindow::find($chat_thread_id);
        if ($chat_thread->user_id == $user_id) {
            $user->push_badge = $user->push_badge - $chat_thread->user_offline_batch;
            $chat_thread->user_offline_batch = 0;
        } else {
            $user->push_badge = $user->push_badge - $chat_thread->winner_offline_batch;
            $chat_thread->winner_offline_batch = 0;
        }
        $chat_thread->updated_at = Carbon\Carbon::now();
        $chat_thread->save();
        $user->save();

        if ($chat_thread) {
            static::$response['message'] = trans('messages.success.chat_thread_updated');
            static::$response['success'] = config('constants.status.success');
            static::$response['result'] = array(
                'chat_unread_count' => ChatWindow::unReadChatThreadCount($user_id)
            );
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.chat_thread_not_found');
        }
        return static::$response;
    }

}
