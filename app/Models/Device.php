<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utility\CommonUtility;

class Device extends Model {

    use SoftDeletes;

    protected $table = 'device';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Register device
     * @param integer user_id
     * @param integer device_type
     * @param string device_token
     * @return object
     */
    public static function registerDevice($user_id, $device_type, $device_token) {
        Device::where('user_id', $user_id)->delete();
        $token = CommonUtility::generateToken($user_id);
        $device_data = array('user_id' => $user_id,
            'device_type' => $device_type,
            'device_token' => $device_token,
            'access_token' => $token,
        );
        Device::insert($device_data);
        return $token;
    }

    /**
     * soft delete device
     * @param string access_token
     */
    public static function unRegisterDevice($access_token) {
        Device::where('access_token', $access_token)->delete();
    }

}
