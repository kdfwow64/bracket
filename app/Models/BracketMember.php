<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class BracketMember extends Model {

    use SoftDeletes;

    protected $table = 'bracket_member';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * wildcard dater
     * @return array
     */
    function wildcardDater() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * Get list of wildcard users 
     * @return array
     */
    public static function getWildcardUser() {
        return BracketMember::select("bracket_member.*", DB::raw("max(DATE_FORMAT(bracket_member.created_at, '%m-%d-%Y %H:%i:%s')) as created"), DB::raw('count(*) as total'))
                        ->with(['wildcardDater'])
                        ->where('bracket_member.type', config('constants.bracket.round_three_wild_card_type'))
                        ->orWhere('bracket_member.type', config('constants.bracket.round_four_wild_card_type'))
                        ->groupBy('bracket_member.user_id')
                        ->orderBy('bracket_member.created_at','DESC')
                        ->paginate(config('constants.record_per_page'))->toArray();
    }

    /**
     * Get wildcard user by date filtered 
     * @param date $from_date
     * @param date $to_date
     * @return array
     */
    public static function getDateFilterWildcardUser($from_date, $to_date) {
        return BracketMember::select("bracket_member.*", DB::raw("max(DATE_FORMAT(bracket_member.created_at, '%m-%d-%Y %H:%i:%s')) as created"), DB::raw('count(*) as total'))
                        ->with(['wildcardDater'])
                        ->orWhere(function ($where_query) {
                            $where_query->orWhere('bracket_member.type', config('constants.bracket.round_three_wild_card_type'))
                            ->orWhere('bracket_member.type', config('constants.bracket.round_four_wild_card_type'));
                        })
                        ->whereBetween('bracket_member.created_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))])
                        ->groupBy('bracket_member.user_id')
                        ->orderBy('bracket_member.created_at','DESC')
                        ->paginate(config('constants.record_per_page'))->toArray();
    }

}
