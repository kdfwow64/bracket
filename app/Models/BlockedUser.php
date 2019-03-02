<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BlockedUser extends Model {

    use SoftDeletes;

    protected $table = 'blocked_user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * blocked/unblocked by dater
     * @return array
     */
    function blockedBy() {
        return $this->hasOne('App\Models\User', 'id', 'blocked_by_user_id');
    }

    /**
     * blocked/unblocked to dater
     * @return array
     */
    function blockedTo() {
        return $this->hasOne('App\Models\User', 'id', 'blocked_user_id');
    }

    /**
     * Get list of blocked/unblocked users 
     * @return array
     */
    public static function getBlockedUser($flag = NULL) {
        $blocked_user = BlockedUser::with(['blockedTo'])
                ->join('user', 'user.id', '=', 'blocked_user.blocked_user_id')
                ->whereNull('user.deleted_at')
                ->select('*', DB::raw('count(*) as total'))
                ->orderBy('blocked_user.created_at','DESC')
                ->groupBy('blocked_user_id');
        if ($flag == 1) {
            $blocked_user = $blocked_user->onlyTrashed();
        }
        return $blocked_user->paginate(config('constants.record_per_page'))->toArray();
    }

    /**
     * Get list of users who blocked/unblocked particular user
     * @param integer $userid
     * @return array
     */
    public static function getBlockedUserDetail($userid, $flag = NULL) {
        $blocked_user = BlockedUser::
                select("blocked_user.*", DB::raw("DATE_FORMAT(blocked_user.created_at, '%m-%d-%Y %H:%i:%s') as created"), DB::raw("DATE_FORMAT(blocked_user.deleted_at, '%m-%d-%Y %H:%i:%s') as deleted"))
                ->where('blocked_user_id', $userid)
                ->join('user', 'user.id', '=', 'blocked_user.blocked_by_user_id')
                ->whereNull('user.deleted_at')
                ->orderBy('blocked_user.created_at','DESC')
                ->with(['blockedBy']);
        if ($flag == 1) {
            $blocked_user = $blocked_user->onlyTrashed();
        }
        return $blocked_user->paginate(config('constants.record_per_page'))->toArray();
    }

    /**
     * Get blocked/unblocked user by date filtered 
     * @param integer $userid
     * @param date $from_date
     * @param date $to_date
     * @return array
     */
    public static function getDateFilterBlockedUser($userid, $from_date, $to_date, $flag = NULL) {
        $filter_arr = BlockedUser::select("blocked_user.*", DB::raw("DATE_FORMAT(blocked_user.created_at, '%m-%d-%Y %H:%i:%s') as created"), DB::raw("DATE_FORMAT(blocked_user.deleted_at, '%m-%d-%Y %H:%i:%s') as deleted")
                )
                ->where('blocked_user_id', $userid);
        /**
         * For unblocked users we are fetching deleted at records and created at for blocked users 
         */
        if ($flag == 1) {
            $filter_arr = $filter_arr->onlyTrashed()
                    ->whereBetween('blocked_user.deleted_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        } else {
            $filter_arr = $filter_arr->whereBetween('blocked_user.created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        return $filter_arr->join('user', 'user.id', '=', 'blocked_user.blocked_by_user_id')
                        ->whereNull('user.deleted_at')
                        ->orderBy('blocked_user.created_at','DESC')
                        ->with(['blockedBy'])->paginate(config('constants.record_per_page'));
    }

    /**
     * to remove two way blocked users from rating
     * @param type $user_id
     * @return type object
     */
    public static function removeBlockedUserFromRating($user_id) {
        $first = BlockedUser::select(\DB::raw('blocked_user_id as user_id'))
                ->where('blocked_user_id', $user_id)
                ->orWhere('blocked_by_user_id', $user_id);

        return BlockedUser::select(\DB::raw('blocked_by_user_id as user_id'))
                        ->where('blocked_user_id', $user_id)
                        ->orWhere('blocked_by_user_id', $user_id)
                        ->union($first)
                        ->pluck('user_id')->toArray();
    }

}
