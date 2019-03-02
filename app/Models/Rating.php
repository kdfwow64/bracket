<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Rating extends Model {

    protected $table = 'rating';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * rating given by dater
     * @return array
     */
    function ratingBy() {
        return $this->hasOne('App\Models\User', 'id', 'rating_by_user_id');
    }

    /**
     * rating given to dater
     * @return array
     */
    function ratingTo() {
        return $this->hasOne('App\Models\User', 'id', 'rating_to_user_id');
    }

    /**
     * Get dater ratings received 
     * @param integer $userid
     * @return object
     */
    public static function getRatingForUserId($userid) {
        return Rating::select("rating.*", DB::raw("DATE_FORMAT(rating.created_at, '%m-%d-%Y %H:%i:%s') as created"))
                        ->where('rating_to_user_id', $userid)
                        ->orderBy('created_at', 'DESC')
                        ->with(['ratingBy', 'ratingTo'])
                        ->paginate(config('constants.record_per_page'));
    }

    /**
     * Get dater ratings by date filtered 
     * @param integer $userid
     * @param date $from_date
     * @param date $to_date
     * @return array
     */
    public static function getDateFilterRating($userid, $from_date, $to_date) {
        return Rating::select("rating.*", DB::raw("DATE_FORMAT(rating.created_at, '%m-%d-%Y %H:%i:%s') as created"))
                        ->where('rating_to_user_id', $userid)->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])->orderBy('created_at', 'DESC')->with(['ratingBy', 'ratingTo'])->paginate(config('constants.record_per_page'));
    }

    /**
     * return conditional select query for finding a user for rating
     * @param type $login_user
     * @return type
     */
    public static function getQuery($login_user) {
        if ($login_user->latitude != '' || $login_user->longitude != '') {
            $query = User::select('user.*', 'user.profile_picture as thumb_picture', 'user.id as user_id', DB::raw("  3956 * 2 * "
                                    . "ASIN(SQRT(POWER(SIN(($login_user->latitude - abs(latitude)) *"
                                    . " pi()/180 / 2), 2) +  COS($login_user->latitude * pi()/180 ) * "
                                    . "COS(abs(latitude) * pi()/180) *  "
                                    . "POWER(SIN(($login_user->longitude - longitude) * "
                                    . "pi()/180 / 2), 2) )) as  distance"))
                    ->havingRaw("distance <= $login_user->end_radius");
        } else {
            $query = User::select('id');
        }
        return $query;
    }

}
