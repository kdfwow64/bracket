<?php

namespace App\Providers;

use App\Models\Rating;
use App\Models\User;
use App\Models\BlockedUser;
use App\Models\PushNotification;
use App\Utility\CommonUtility;
use App\Models\Bracket;
use App\Models\BracketMember;

/**
 * RatingServiceProvider class contains methods for user rating management
 */
class RatingServiceProvider extends BaseServiceProvider {

    /**
     * fetch user for rating
     * @return array
     */
    public static function fetchUserForRating($login_user_id) {
        $login_user = User::select('prefer_gender', 'latitude', 'longitude', 'end_radius')->where('id', $login_user_id)->first();
        $rating_buckets = CommonUtility::ratingBucket($login_user->average_rating);
        $rated_users_id = Rating::where('rating_by_user_id', $login_user_id)->pluck('rating_to_user_id')->toArray();
        $blocked_users_id = BlockedUser::removeBlockedUserFromRating($login_user_id);
        $user_bracket_ids = Bracket::where('user_id', $login_user_id)->pluck('id')->toArray();
        $already_existing_bracket_users_id = BracketMember::whereIn('bracket_id', $user_bracket_ids)
                        ->pluck('user_id')->toArray();
        $remove_users_id = array_merge($rated_users_id, $blocked_users_id, $already_existing_bracket_users_id);

        $query = Rating::getQuery($login_user);

        // all conditions
        $users = $query
                        ->where(function ($query) use ($login_user_id, $login_user) {
                            $query->where('id', '!=', $login_user_id)
                            ->where('gender', $login_user->prefer_gender)
                            ->where('is_profile_completed', 1)
                            ->where('role', 2);
                        })
                        ->where(function ($query) use ($rating_buckets) {
                            $query->whereIn('average_rating', $rating_buckets);
                        })
                        ->where(function ($query) use ($remove_users_id) {
                            $query->whereNotIn('id', $remove_users_id);
                        })
                        ->pluck('id')->toArray();

        if (empty($users)) {
            // removed buckets condition
            unset($query);
            $query = Rating::getQuery($login_user);
            $users = $query
                            ->where(function ($query) use ($login_user_id, $login_user) {
                                $query->where('id', '!=', $login_user_id)
                                ->where('gender', $login_user->prefer_gender)
                                ->where('is_profile_completed', 1)
                                ->where('role', 2);
                            })
                            ->where(function ($query) use ($remove_users_id) {
                                $query->whereNotIn('id', $remove_users_id);
                            })
                            ->pluck('id')->toArray();
        }

        if (empty($users)) {
            // removed buckets condition and allowing previous user to rate
            unset($query);
            $query = Rating::getQuery($login_user);
            $remove_users_id = array_merge($blocked_users_id, $already_existing_bracket_users_id);
            $users = $query
                            ->where(function ($query) use ($login_user_id, $login_user) {
                                $query->where('id', '!=', $login_user_id)
                                ->where('gender', $login_user->prefer_gender)
                                ->where('is_profile_completed', 1)
                                ->where('role', 2);
                            })
                            ->where(function ($query) use ($remove_users_id) {
                                $query->whereNotIn('id', $remove_users_id);
                            })
                            ->pluck('id')->toArray();
        }

        $user = '';
        if (!empty($users)) {
            $rating_user_id_index = array_rand($users, 1);
            $rating_user_id = $users[$rating_user_id_index];
            $user = User::getUserProfile($rating_user_id, config('constants.search.user_id'), 3);
        }
        if (is_object($user)) {
            static::$response['result'] = array('rating_user' => $user);
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.rating_user_not_found');
        }
        return static::$response;
    }

    /**
     * rate user for rating
     * @return array
     */
    public static function rateUser($data, $user_id) {
        $array = array(
            'rating_to_user_id' => $data['ratingToUserId'],
            'rating_by_user_id' => $user_id,
            'rating_number' => $data['ratingNumber']
        );
        $ratingToUserId = User::where('id', $data['ratingToUserId'])->withTrashed()->first();
        if ($ratingToUserId->deleted_at != NULL) {
            $user_rating_count = Rating::where('rating_by_user_id', $user_id)->count();
            User::where('id', $user_id)->update(['rating_done' => $user_rating_count]);
            static::$response['success'] = config('constants.status.success');
            static::$response['message'] = trans('messages.success.rate_done');
        } else {
            $check_rating = Rating::where('rating_to_user_id', $data['ratingToUserId'])
                            ->where('rating_by_user_id', $user_id)->first();
            $old_avg_rating = (int) round(Rating::where('rating_to_user_id', $data['ratingToUserId'])->avg('rating_number'));
            Rating::insert($array);
            $avg_rating = (int) round(Rating::where('rating_to_user_id', $data['ratingToUserId'])->avg('rating_number'));
            $avg_rating = ($avg_rating > 5 ? 5 : $avg_rating);
            $user_rating_count = Rating::where('rating_by_user_id', $user_id)->count();
            User::where('id', $data['ratingToUserId'])->update(['average_rating' => $avg_rating]);
            User::where('id', $user_id)->update(['rating_done' => $user_rating_count]);
            $push_data = config('constants.push_notification');
            if ($avg_rating > $old_avg_rating) {
                $push_data['five']['sender_user_id'] = $user_id;
                $push_data['five']['receiver_user_id'] = $data['ratingToUserId'];
                PushNotification::sendPushNotification($user_id, $data['ratingToUserId'], $push_data['five']);
            }
            $push_data['silent']['four']['sender_user_id'] = $user_id;
            $push_data['silent']['four']['receiver_user_id'] = $data['ratingToUserId'];
            $push_data['silent']['four']['silent_flag'] = 1;
            PushNotification::sendPushNotification($user_id, $data['ratingToUserId'], $push_data['silent']['four']);
            static::$response['success'] = config('constants.status.success');
            static::$response['message'] = trans('messages.success.rate_done');
        }
        return static::$response;
    }

}
