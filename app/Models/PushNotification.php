<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repository\IosPushRepository;
use DB;

class PushNotification extends Model {

    use SoftDeletes;

    protected $table = 'push_notification';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /*
     * send push to ios device
     * @param integer $sender_user_id
     * @param integer $receiver_user_id
     * @param array $payload
     * @return true
     */

    public static function sendPushNotification($sender_user_id, $receiver_user_id, $payload) {
        $user = User::select('is_push_notification', 'chat_badge')
                        ->where('id', $receiver_user_id)->first();
        if (!empty($user)) {
            if (($user->is_push_notification == 1) ||
                    (isset($payload['silent_flag']) && $payload['silent_flag'] == 1)) {
                $data = array(
                    'sender_user_id' => $sender_user_id,
                    'receiver_user_id' => $receiver_user_id,
                    'message' => $payload['message'],
                    'type' => $payload['type'],
                    'title' => $payload['title'],
                    'thread_id' => $payload['thread_id']
                );
                $receiver_device_tokens = PushNotification::getUserDeviceDetail($receiver_user_id);
                if (!isset($payload['silent_flag']) &&
                        ($payload['type'] != config('constants.push_notification.thirteen.type'))) {
                    PushNotification::insert($data);
                }
                if (!empty($receiver_device_tokens)) {
                    // if we have device tokens updated batch count
                    $badge = PushNotification::updateBadge($receiver_user_id, $payload['thread_id'], $payload['type']);
                }
                $push_message = $payload['message'];
                if (!empty($payload['push_message'])) {
                    $push_message = $payload['push_message'];
                }
                $payload['push_unread_count'] = PushNotification::fetchUserUnreadPushCount($receiver_user_id);
                $payload['chat_unread_count'] = ChatWindow::unReadChatThreadCount($receiver_user_id);
                PushNotification::initiatePush($receiver_device_tokens, $payload, $badge, $push_message);
            }
        }

        return true;
    }

    /*
     * initiate push to ios device tokens
     * @param integer $device_tokens
     * @return object
     */

    public static function getUserDeviceDetail($userId) {
        return Device::select('device_token')->where('user_id', $userId)->get();
    }

    /*
     * initiate push to ios device tokens
     * @param array $device_tokens
     * @param array $payload
     * @param integer $badge
     * @return true
     */

    public static function initiatePush($device_tokens, $payload, $badge, $push_message) {
        foreach ($device_tokens as $token) {
            $device_length = strlen($token->device_token);
            if (!empty($token->device_token) && $device_length >= 64) {
                $sendPush = new IosPushRepository(config('environment.push_env'));
                $sendPush->send($token->device_token, $push_message, $payload, $badge);
            }
        }
        return true;
    }

    /*
     * update push badge for user
     * @param integer user_id
     * @return integer new_badge
     */

    public static function updateBadge($user_id, $thread_id, $type) {
        if ($thread_id != NULL && $type == config('constants.push_notification.thirteen.type')) {
            $chat_thread = ChatWindow::find($thread_id);
            if ($chat_thread->user_id == $user_id) {
                $chat_thread->user_offline_batch = $chat_thread->user_offline_batch + 1;
            } else {
                $chat_thread->winner_offline_batch = $chat_thread->winner_offline_batch + 1;
            }
            $chat_thread->save();
        }
        $badge = User::select('push_badge')->where('id', $user_id)->first();
        $new_badge = $badge->push_badge + 1;
        User::where('id', $user_id)->update(['push_badge' => $new_badge]);
        return $new_badge;
    }

    /**
     * soft delete user push
     * @return boolean
     */
    public static function deletePush($user_id, $push_id) {
        return PushNotification::where('id', $push_id)->where('receiver_user_id', $user_id)->delete();
    }

    /**
     * fetch user unread push count
     * @return integer
     */
    public static function fetchUserUnreadPushCount($user_id) {
        return PushNotification::where('receiver_user_id', $user_id)
                        ->where('is_read', 0)
                        ->count();
    }

    /**
     * fetch push notifications posted by admin
     */
    public static function getNotificationsByAdmin($type) {
        return PushNotification::select("push_notification.*", DB::raw("DATE_FORMAT(push_notification.created_at, '%m-%d-%Y %H:%i:%s') as created"), DB::raw("push_notification.type % 110 as recipient_id"))
                        ->whereIn('type', $type)
                        ->groupBy('title')
                        ->groupBy('message')
                        ->groupBy('type')
                        ->orderBy('created', 'DESC')
                        ->paginate(config('constants.record_per_page'))
                        ->toArray();
    }

    /**
     * User details of notification recipients
     * @return object
     */
    function userDetails() {
        return $this->hasOne('App\Models\User', 'id', 'receiver_user_id')->withTrashed();
    }

    /**
     * Get recipients of the notification
     * @param integer $id
     * @return array
     */
    public static function getNotificationsReceivers($id) {
        $notification = PushNotification::where('id', $id)->first();
        if (isset($notification)) {
            $notification = $notification->toArray();

            $title = $notification['title'];
            $message = $notification['message'];
            $type = $notification['type'];

            return PushNotification::where('title', $title)
                            ->where('message', $message)
                            ->where('type', $type)
                            ->with(['userDetails'])
                            ->paginate(config('constants.record_per_page'))
                            ->toArray();
        }
    }

}
