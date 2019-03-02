<?php

namespace App\Providers;

use App\Models\User;
use App\Utility\CommonUtility;
use App\Models\Image;

/**
 * ImageServiceProvider class contains methods for image management
 */
class ImageServiceProvider extends BaseServiceProvider {

    /**
     * Upload image and delete old image
     * @param form data
     * @return array
     */
    public static function uploadImage($image_file, $is_profile_image, $image_position, $user_id, $is_facebook_image = 0) {
        $old_image = Image::where('user_id', $user_id)->where('image_position', $image_position)->first();
        if (is_object($old_image) && !empty($old_image->image_name)) {
            Image::where('user_id', $user_id)->where('image_position', $image_position)->delete();
            CommonUtility::iniateImageDeleteRequest($old_image->getOriginal('image_name'));
        }
        $image = CommonUtility::uploadImageToS3($image_file, $is_facebook_image);
        if ($image != false) {
            $image_data = array(
                'user_id' => $user_id,
                'image_name' => $image,
                'image_position' => $image_position
            );
            $image_id = Image::insertGetId($image_data);
            if ($is_profile_image == 1) {
                User::where('id', $user_id)->update(['profile_picture' => $image]);
            } else {
                Image::checkProfileImage($user_id);
            }
            $gallery = Image::select('id as imageId', 'image_name', 'image_position', 'image_name as thumb_name')
                    ->where('user_id', $user_id)
                    ->get();
            static::$response['result'] = array(
                'image_name' => config('environment.s3_url') . '600X600/' . $image,
                'thumb_name' => config('environment.s3_url') . '600X600/' . $image,
                'image_position' => $image_position,
                'imageId' => $image_id,
                'gallery' => $gallery
            );
            static::$response['status'] = 1;
        }
        return static::$response;
    }

    /**
     * delete images from s3 server
     * @param type $images
     * @return type
     */
    public static function deleteImagesFromS3($images) {
        if (is_array($images)) {
            foreach ($images as $image) {
                CommonUtility::deleteImageFromS3($image);
            }
        } else {
            CommonUtility::deleteImageFromS3($images);
        }
        static::$response['status'] = 1;
        return static::$response;
    }

    /**
     * method to upload user image from url
     * @param type $image_url
     * @param type $user_id
     * @return type
     */
    public static function uploadFaceBookImage($image_url, $user_id, $is_profile_image, $image_position) {
        try {
            $file_name = 'test.jpg';
            $is_facebook_image = 1;
            $destinationPath = public_path('upload') . '/' . $file_name;
            copy($image_url, $destinationPath);
            $file_name = 'upload/' . $file_name;
            $data = file_get_contents($file_name);
            ImageServiceProvider::uploadImage($data, $is_profile_image, $image_position, $user_id, $is_facebook_image);
            @unlink($destinationPath);
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
    }

}
