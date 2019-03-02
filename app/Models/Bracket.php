<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlockedUser;
use App\Utility\CommonUtility;
use DB;
use App\Models\User;

class Bracket extends Model {

    use SoftDeletes;

    protected $table = 'bracket';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public static function checkAvailbleCount($round_number, $winner_ids, $looser_ids, $user_is_paid) {
        $winner_ids_count = count($winner_ids);
        $looser_ids_count = count($looser_ids);
        if (
                ($round_number == 1 && $winner_ids_count == 8 && $looser_ids_count == 8) ||
                ($round_number == 2 && $winner_ids_count == 4 && $looser_ids_count == 4) ||
                ($round_number == 3 && $winner_ids_count == 3 && $looser_ids_count == 3) ||
                ($round_number == 4 && $winner_ids_count == 2 && $looser_ids_count == 2) ||
                ($round_number == 5 && $user_is_paid == 0 && $winner_ids_count == 1 && $looser_ids_count == 1) ||
                ($round_number == 5 && $user_is_paid == 1 && $winner_ids_count = 2 && $looser_ids_count == 0) ||
                ($round_number == 5 && $user_is_paid == 1 && $winner_ids_count = 1 && $looser_ids_count == 1)
        ) {
            $return = 1;
        } else {
            $return = 0;
        }

        return $return;
    }

    /**
     * Get bracket analytics
     * @return array
     */
    public static function bracketAnalytics($request = NULL) {

        $bracket_analytics['Brackets'] = Bracket::where('bracket.is_completed', config('constants.bracket.is_complete_true'));
        if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $bracket_analytics['Brackets'] = $bracket_analytics['Brackets']->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $bracket_analytics['Brackets'] = $bracket_analytics['Brackets']->count();

        $free_counts = Bracket::
                where('bracket.is_completed', config('constants.bracket.is_complete_true'))
                ->where('bracket.is_paid_bracket', config('constants.bracket.is_free_bracket'));

        if (isset($request) && !empty($request) && !empty($request['from_date'])) {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $free_counts = $free_counts->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $free_counts = $free_counts->count();

        $paid_counts = Bracket::where('bracket.is_completed', config('constants.bracket.is_complete_true'))->where('bracket.is_paid_bracket', config('constants.bracket.is_paid_bracket'));
        if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $paid_counts = $paid_counts->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $paid_counts = $paid_counts->count() * 2;
        $bracket_analytics['Winners'] = $paid_counts + $free_counts;
        return $bracket_analytics;
    }

    /**
     * function to create bracket for user
     * @param type $login_user
     * @param type $bracket_constants
     * @param type $user_id
     * @return type
     */
    public static function createBracket($login_user, $bracket_constants, $user_id) {
        $rating_buckets = CommonUtility::ratingBucket($login_user->average_rating);
        $current_chat_users = ChatWindow::removeCurrentThreadUsers($user_id);
        $blocked_users_id = BlockedUser::removeBlockedUserFromRating($user_id);
        $already_existing_users_id = self::fetchBracketPreviousUsers($user_id);
        $removed_users_id = array_unique(array_merge($current_chat_users, $blocked_users_id, $already_existing_users_id));
        $users = self::bracket($login_user, $bracket_constants, $user_id, $rating_buckets, $removed_users_id);
        $users_count = count($users);
        if ($users_count < $bracket_constants['members_count']) {
            $available_users_id = array_column($users->toArray(), 'id');
            $old_available_users_array = array_unique(array_diff($already_existing_users_id, $blocked_users_id, $current_chat_users, $available_users_id));
            $required_users_count = $bracket_constants['members_count'] - $users_count;
            if (!empty($old_available_users_array) && count($old_available_users_array) >= $required_users_count) {
                $bracket_members_id = array_merge($available_users_id, array_rand(array_flip($old_available_users_array), $required_users_count));
                $users = User::
                        select('user.*', 'user.profile_picture as thumb_picture', 'user.id as user_id', DB::raw("  3956 * 2 * "
                                        . "ASIN(SQRT(POWER(SIN(($login_user->latitude - abs(latitude)) *"
                                        . " pi()/180 / 2), 2) +  COS($login_user->latitude * pi()/180 ) * "
                                        . "COS(abs(latitude) * pi()/180) *  "
                                        . "POWER(SIN(($login_user->longitude - longitude) * "
                                        . "pi()/180 / 2), 2) )) as  distance"))
                        ->whereIn('id', $bracket_members_id)
                        ->get();
            }
        }
        return $users->shuffle();
    }

    /**
     * function to fetch user for bracket creation
     * @param type $login_user
     * @param type $bracket_constants
     * @param type $user_id
     * @param type $rating_buckets
     * @param type $removed_users_id
     * @return type
     */
    public static function bracket($login_user, $bracket_constants, $user_id, $rating_buckets, $removed_users_id) {
        return User::
                        select('user.*', 'user.profile_picture as thumb_picture', 'user.id as user_id', DB::raw("  3956 * 2 * "
                                        . "ASIN(SQRT(POWER(SIN(($login_user->latitude - abs(latitude)) *"
                                        . " pi()/180 / 2), 2) +  COS($login_user->latitude * pi()/180 ) * "
                                        . "COS(abs(latitude) * pi()/180) *  "
                                        . "POWER(SIN(($login_user->longitude - longitude) * "
                                        . "pi()/180 / 2), 2) )) as  distance"))
                        ->where(function ($query) use ($bracket_constants, $login_user, $user_id) {
                            $query
                            ->where('is_profile_completed', $bracket_constants['is_complete_true'])
                            ->where('role', config('constants.user_type.app_user'))
                            ->where('gender', $login_user->prefer_gender)
                            ->where('prefer_gender', $login_user->gender)
                            ->where('id', '!=', $user_id);
                        })
                        ->where(function ($query) use ($rating_buckets, $login_user, $removed_users_id) {
                            $query
                            ->whereIn('average_rating', $rating_buckets)
                            ->whereBetween('age', [$login_user->start_age, $login_user->end_age])
                            ->whereNotIn('id', $removed_users_id);
                        })
                        ->havingRaw("start_age >= $login_user->start_age AND end_age <= $login_user->end_age")
                        ->havingRaw("distance <= $login_user->end_radius")
                        ->orderBy('rating_done', 'desc')
                        ->limit($bracket_constants['members_count'])
                        ->get();
    }

    /**
     * function to fetch previously played bracket members
     * @param type $user_id
     * @return type
     */
    public static function fetchBracketPreviousUsers($user_id) {
        $user_bracket = Bracket::select('id')->where('user_id', $user_id)->get()->toArray();
        $user_bracket_ids = array_column($user_bracket, 'id');
        $already_existing_users = BracketMember::select('user_id')->whereIn('bracket_id', $user_bracket_ids)->get()->toArray();
        return array_column($already_existing_users, 'user_id');
    }

}
