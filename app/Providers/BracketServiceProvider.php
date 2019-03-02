<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Bracket;
use App\Models\BracketMember;
use DB;
use App\Models\PushNotification;
use Carbon;
use App\Models\ChatWindow;

/**
 * BracketServiceProvider class contains methods for bracket management
 */
class BracketServiceProvider extends BaseServiceProvider {

    /**
     * create bracket for the user
     * @param type array
     * @return array
     */
    public static function createBracket($input, $user_id) {
        $bracket_constants = config('constants.bracket');
        User::where('id', $user_id)->update(['latitude' => $input['latitude'], 'longitude' => $input['longitude']]);
        $login_user = User::find($user_id);
        $results = DB::select(DB::raw('SELECT NOW() AS time'));
        $current_time = $results[0]->time;
        $existing_bracket = Bracket::where('user_id', $user_id)->where('is_completed', $bracket_constants['is_complete_false'])->first();

        if (is_object($existing_bracket)) {
            $users = User::select('user.*', 'user.profile_picture as thumb_picture', 'user.id as user_id', DB::raw("  3956 * 2 * "
                                    . "ASIN(SQRT(POWER(SIN(($login_user->latitude - abs(latitude)) *"
                                    . " pi()/180 / 2), 2) +  COS($login_user->latitude * pi()/180 ) * "
                                    . "COS(abs(latitude) * pi()/180) *  "
                                    . "POWER(SIN(($login_user->longitude - longitude) * "
                                    . "pi()/180 / 2), 2) )) as  distance"), 'user.id as user_id', 'bracket_member.type', 'bracket_member.round_number as available_round')
                    ->join('bracket_member', 'bracket_member.user_id', '=', 'user.id')
                    ->where('bracket_member.bracket_id', $existing_bracket->id)
                    ->withTrashed()
                    ->get();

            static::$response['result'] = array(
                'created_at' => Carbon\Carbon::parse($existing_bracket->created_at)->format('Y-m-d H:i:s'),
                'bracket_id' => $existing_bracket->id,
                'last_played_round' => $existing_bracket->status,
                'next_played_round' => $existing_bracket->status + 1,
                'api_status' => 2,
                'current_time' => $current_time,
                'is_time_bounded' => $existing_bracket->is_time_bounded,
                'is_paid' => $login_user->is_paid,
                'is_carry_forward' => $existing_bracket->type,
                'user' => $users
            );
            static::$response['success'] = config('constants.status.success');
            static::$response['message'] = trans('messages.fail.bracket_already_running');
        } else {
            $status = 0;
            if ($login_user->free_bracket_count != 0) {
                $status = 1;
            } else if ($login_user->earn_bracket_count != 0) {
                $status = 2;
            } else if ($login_user->paid_bracket_count != 0) {
                $status = 3;
            } else {
                static::$response['success'] = config('constants.status.success');
                static::$response['message'] = trans('messages.fail.daily_bracket_limit_over');
                static::$response['result'] = array(
                    'api_status' => 4,
                    'current_time' => $current_time
                );
                return static::$response;
            }

            $users = Bracket::createBracket($login_user, $bracket_constants, $user_id);
            $users_count = count($users);

            if ($users_count < $bracket_constants['members_count']) {
                static::$response['success'] = config('constants.status.success');
                static::$response['message'] = trans('messages.fail.members_not_found_for_bracket');
                static::$response['result'] = array(
                    'api_status' => 3,
                    'current_time' => $current_time,
                    'available_daters_count' => $users_count
                );
            } else {
                // type(c/f)
                if ($login_user->is_paid == 0) {
                    $is_paid = 0;
                    $is_time_bounded = 1;
                    if ($status == 1) {
                        // free user + free daily bracket
                        $type = 0;
                    } else if ($status == 2) {
                        // free user + earn bracket
                        $type = 1;
                    } else if ($status == 3) {
                        // free user + paid bracket
                        $type = 1;
                        $is_time_bounded = 0;
                    }
                } else {
                    $is_paid = 1;
                    $is_time_bounded = 0;
                    if ($status == 1) {
                        // paid user + free daily bracket
                        $type = 0;
                    } else if ($status == 2) {
                        // paid user + earn bracket
                        $type = 1;
                    } else if ($status == 3) {
                        // paid user + paid bracket
                        $type = 1;
                    }
                }

                $bracket_data = array(
                    'user_id' => $user_id,
                    'bracket_date' => DB::raw('now()'),
                    'type' => $type,
                    'is_time_bounded' => $is_time_bounded,
                    'is_paid_bracket' => $is_paid
                );
                $bracket_id = Bracket::insertGetId($bracket_data);
                $bracket_member_data = array();
                foreach ($users as $key => $value) {
                    $bracket_member_data[$key]['bracket_id'] = $bracket_id;
                    $bracket_member_data[$key]['user_id'] = $value['id'];
                    $bracket_member_data[$key]['round_number'] = $bracket_constants['first_round'];
                    $bracket_member_data[$key]['type'] = $bracket_constants['non_wild_card_type'];
                    $users[$key]['available_round'] = $bracket_constants['first_round'];
                    $users[$key]['type'] = $bracket_constants['non_wild_card_type'];
                    $users[$key]['distance'] = $value['distance'];
                    if ($key == 0) {
                        $bracket_member_data[$key]['round_number'] = $bracket_constants['round_four'];
                        $bracket_member_data[$key]['type'] = $bracket_constants['round_four_wild_card_type'];
                        $users[$key]['type'] = $bracket_constants['round_four_wild_card_type'];
                        $users[$key]['available_round'] = $bracket_constants['round_four'];
                    } elseif ($key == 1 || $key == 2) {
                        $bracket_member_data[$key]['round_number'] = $bracket_constants['round_three'];
                        $bracket_member_data[$key]['type'] = $bracket_constants['round_three_wild_card_type'];
                        $users[$key]['type'] = $bracket_constants['round_three_wild_card_type'];
                        $users[$key]['available_round'] = $bracket_constants['round_three'];
                    }
                }
                $bracket = Bracket::find($bracket_id)->toArray();
                BracketMember::insert($bracket_member_data);

                if ($status == 1) {
                    $login_user->free_bracket_count = $login_user->free_bracket_count - 1;
                } else if ($status == 2) {
                    $login_user->earn_bracket_count = $login_user->earn_bracket_count - 1;
                } else if ($status == 3) {
                    $login_user->paid_bracket_count = $login_user->paid_bracket_count - 1;
                }
                $login_user->save();

                static::$response['success'] = config('constants.status.success');
                static::$response['result'] = array(
                    'created_at' => $bracket['created_at'],
                    'bracket_id' => $bracket_id,
                    'last_played_round' => $bracket_constants['first_round'] - 1,
                    'next_played_round' => $bracket_constants['first_round'],
                    'api_status' => 1,
                    'current_time' => $current_time,
                    'is_time_bounded' => $bracket['is_time_bounded'],
                    'is_paid' => $login_user->is_paid,
                    'is_carry_forward' => $bracket['type'],
                    'user' => $users,
                );
            }
        }
        return static::$response;
    }

    /**
     * update bracket for the user
     * @param type array
     * @return array
     */
    public static function updateBracket($input, $user_id) {
        $bracket_id = $input['bracketId'];
        $round_result = $input['roundResult'];
        $winner_ids = $input['winnersIds'];
        $looser_ids = $input['looserIds'];
        $next_round = $round_result + 1;
        $login_user = User::find($user_id);
        $bracket_constants = config('constants.bracket');
        $bracket = Bracket::select('bracket.*', DB::raw('NOW() AS time'))->where('id', $bracket_id)->first();
        $current_time = $bracket->time;
        if ($bracket->status == $round_result) {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.bracket_round_played');
        } else {
            $bracket_data_status = Bracket::checkAvailbleCount($round_result, $winner_ids, $looser_ids, $login_user->is_paid, $current_time);
            $is_completed = $bracket_constants['is_complete_false'];
            $winner_user_id = NULL;
            $runner_up_user_id = NULL;
            if ($bracket_data_status) {
                if ($round_result == $bracket_constants['last_round']) {
                    $push_data = config('constants.push_notification.ten');
                    $push_data['push_message'] = $push_data['push_message_1'] . $login_user->first_name . $push_data['push_message_2'];
                    $push_data['message'] = $push_data['message_1'] . $login_user->first_name . $push_data['message_2'];
                    $is_completed = $bracket_constants['is_complete_true'];
                    $next_round--;
                    $winner_ids_count = count($winner_ids);
                    $tomorrow = Carbon\Carbon::parse($current_time)->adddays(1)->format('Y-m-d H:i:s');
                    $day_after_tomorrow = Carbon\Carbon::parse($current_time)->adddays(3)->format('Y-m-d H:i:s');
                    if ($login_user->is_paid && $winner_ids_count == 2) {
                        $winner_user_id = $winner_ids[0];
                        $runner_up_user_id = $winner_ids[1];
                        foreach ($winner_ids as $winner_id) {
                            $push_data['sender_user_id'] = $user_id;
                            $push_data['receiver_user_id'] = $winner_id;
                            $push_data['thread_id'] = ChatWindow::createChatWindow($user_id, $winner_id, $tomorrow, $day_after_tomorrow);
                            PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
                        }
                    } else {
                        $winner_user_id = $winner_ids[0];
                        $runner_up_user_id = $looser_ids[0];
                        $push_data['sender_user_id'] = $user_id;
                        $push_data['receiver_user_id'] = $winner_ids[0];
                        $push_data['thread_id'] = ChatWindow::createChatWindow($user_id, $winner_user_id, $tomorrow, $day_after_tomorrow);
                        PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
                    }
                }

                BracketMember::where('bracket_id', $bracket_id)
                        ->withTrashed()
                        ->whereIn('user_id', $winner_ids)
                        ->update(['round_number' => $next_round]);
                Bracket::where('id', $bracket_id)
                        ->withTrashed()
                        ->where('user_id', $user_id)
                        ->update([
                            'status' => $round_result,
                            'is_completed' => $is_completed,
                            'winner_user_id' => $winner_user_id,
                            'runner_up_user_id' => $runner_up_user_id,
                ]);
                static::$response['success'] = config('constants.status.success');
                static::$response['message'] = trans('messages.success.bracket_round_data_saved');
            } else {
                static::$response['success'] = config('constants.status.fail');
                static::$response['message'] = trans('messages.fail.bracket_users_data_not_complete');
            }
        }
        return static::$response;
    }

    /**
     * send push to wildcard members
     * @param type array
     * @return array
     */
    public static function sendPushToWildCards($inputs, $user_id) {
        $push_data = config('constants.push_notification.nine');
        $results = DB::select(DB::raw('SELECT NOW() AS time'));
        $current_day = Carbon\Carbon::parse($results[0]->time)->format('Y-m-d');
        $bracket_id = $inputs['bracketId'];
        $bracket = Bracket::find($bracket_id);
        $bracket_member = BracketMember::where('bracket_id', $bracket_id)
                ->where('round_number', $bracket->status + 1)
                ->where('type', $bracket->status)
                ->select('bracket_member.*')
                ->get();

        if (is_object($bracket_member)) {
            foreach ($bracket_member as $member) {
                $today_record = PushNotification::
                        where('receiver_user_id', $member->user_id)
                        ->where('sender_user_id', $user_id)
                        ->where('type', $push_data['type'])
                        ->whereRaw("DATE('created_at') = $current_day")
                        ->first();
                if (!is_object($today_record)) {
                    $push_data['sender_user_id'] = $user_id;
                    $push_data['receiver_user_id'] = $member->user_id;
                    PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
                }
            }
        }
        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = array('bracket_member' => $bracket_member);
        return static::$response;
    }

    /**
     * share bracket on face book
     * @param type array
     * @return array
     */
    public static function shareBracket($user_id) {
        $user = User::find($user_id);
        $user->earn_bracket_count = $user->earn_bracket_count + 1;
        $user->save();
        static::$response['success'] = config('constants.status.success');
        return static::$response;
    }

    /**
     * return server current time
     * @return type
     */
    public static function getServerCurrentTime() {
        static::$response['success'] = config('constants.status.success');
        static::$response['result'] = Bracket::select(DB::raw('NOW() AS time'))->first()->toArray();
        return static::$response;
    }

}
