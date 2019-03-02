<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utility\CommonUtility;

class Image extends Model {

    use SoftDeletes;

    protected $table = 'image';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Get profile image path attribute
     * @return string
     */
    public function getImageNameAttribute() {
        $profile_image_path = '';
        if ($this->attributes['image_name']) {
            $image_name = explode('.', $this->attributes['image_name']);
            $profile_image_path = config('environment.s3_url') . '600X600/' . $image_name[0] . '.jpeg';
        }
        return $profile_image_path;
    }

    /**
     * Get profile image thumb path attribute
     * @return string
     */
    public function getThumbNameAttribute() {
        $profile_image_path = '';
        if ($this->attributes['image_name']) {
            $image_name = explode('.', $this->attributes['image_name']);
            $profile_image_path = config('environment.s3_url') . '600X600/' . $image_name[0] . '.jpeg';
        }
        return $profile_image_path;
    }

    public static function checkProfileImage($user_id) {
        $image = Image::where('user_id', $user_id)
                        ->where('image_position', 0)->first();
        if (!is_object($image)) {
            $gallery = Image::where('user_id', $user_id)
                    ->orderBy('image_position', 'asc')
                    ->first();
            if (is_object($gallery)) {
                User::where('id', $user_id)->update(['profile_picture' => $gallery->getOriginal('image_name')]);
            } else {
                User::where('id', $user_id)->update(['profile_picture' => '']);
            }
        }
        return true;
    }

    public static function deleteImages($image_ids, $user_id) {
        $image_names = \DB::table('image')
                        ->select('image_name')
                        ->whereIn('id', $image_ids)
                        ->where('user_id', $user_id)
                        ->get()->toArray();
        Image::whereIn('id', $image_ids)
                ->where('user_id', $user_id)
                ->delete();
        Image::checkProfileImage($user_id);
        CommonUtility::iniateImageDeleteRequest(array_column($image_names, 'image_name'));
        return true;
    }

}
