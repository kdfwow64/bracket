<?php

namespace App\Utility;

use Illuminate\Support\Facades\Log;
use App\Repository\FileRepositoryS3;
use DateTime;
use Image;

/**
 * Class which contains utility methods
 */
class CommonUtility {

    protected $response;
    protected $statusCode;

    public function __construct() {
        $this->statusCode = config('constants.apiStatus');
        $this->response = array(
            'status' => 1,
            'responseCode' => $this->statusCode['ok'],
            'data' => (object) array(),
            'message' => '',
        );
    }

    /**
     * Render JSON
     * @param integer $status
     * @param integer $responseCode
     * @param array $data
     * @param string $message
     * @return json
     */
    public function renderJson($status, $responseCode, $data, $message = '') {
        $this->response['status'] = $status;
        $this->response['responseCode'] = $responseCode;
        $this->response['data'] = $data;
        $this->response['message'] = $message;
        return Response()->json($this->response, $this->response['responseCode'])->header('Content-Type', 'application/json');
    }

    /**
     * generate random string
     * @param integer $length
     * @return string
     */
    public static function randomString($length) {
        $random = "";
        srand((double) microtime() * 1000000);
        $data = "ADCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }
        return $random;
    }

    /**
     * Log exception
     * @param string $method
     * @param object $e
     */
    public static function logException($method, $e) {

        Log::info(['method' => $method, 'error' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'message' => $e->getMessage()], 'created_at' => date("Y-m-d H:i:s")]);
    }

    /**
     * Generate random token
     * @param integer user_id
     * @return string
     */
    public static function generateToken($user_id) {
        $encoded_user_id = str_replace('=', '', base64_encode((string) $user_id));
        $encoded_user_id_length = strlen($encoded_user_id);
        $hash_string = md5(date('dmYHis') . rand(10000, 99999));
        $check_bit = strlen($encoded_user_id_length);
        $random_str_length = 64 - ($encoded_user_id_length + 32 + $check_bit + 1);
        $random_str = md5(rand(10000, 99999) . date('dmYHis'));
        $token = $encoded_user_id . $hash_string . substr($random_str, 0, $random_str_length) . $encoded_user_id_length . $check_bit;
        return $token;
    }

    /**
     * upload image to S3
     * @param file $file
     * @return boolean|string
     */
    public static function uploadImageToS3($file, $is_facebook_image = 0) {
        try {
            if (!$is_facebook_image) {
                $height = Image::make($file)->orientate()->height();
                $width = Image::make($file)->orientate()->width();
                if ($height > 600 || $width > 600) {
                    if ($height > 600) {
                        $img = Image::make($file)->orientate()->heighten(600);
                        $img->save($file);
                    }
                    if ($width > 600) {
                        $img = Image::make($file)->orientate()->widen(600);
                        $img->save($file);
                    }
                }
            }

            $time = microtime(true);
            $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
            $date = new DateTime(date('Y-m-d H:i:s.' . $micro, $time));
            $file_name = md5($date->format("YmdHisu")) . '.' . 'jpeg';
            $obj = new FileRepositoryS3();
            if ($is_facebook_image) {
                $res = $obj->uploadFileToAWS($file, '600X600/' . $file_name);
            } else {
                $res = $obj->uploadFileToAWS(file_get_contents($file), '600X600/' . $file_name);
            }
            if ($res) {
                return $file_name;
            }
            return false;
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
    }

    /**
     * upload base64 image to S3
     * @param string base_64_image
     * @return boolean|string
     */
    public static function uploadBase64ImageToS3($base_64_image) {
        try {
            $time = microtime(true);
            $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
            $date = new DateTime(date('Y-m-d H:i:s.' . $micro, $time));
            $file_name = md5($date->format("YmdHisu")) . '.png';
            $base_64_image = trim($base_64_image);
            $base_64_image = str_replace('data:image/png;base64,', '', $base_64_image);
            $base_64_image = str_replace('data:image/jpg;base64,', '', $base_64_image);
            $base_64_image = str_replace('data:image/jpeg;base64,', '', $base_64_image);
            $base_64_image = str_replace('data:image/gif;base64,', '', $base_64_image);
            $base_64_image = str_replace(' ', '+', $base_64_image);
            $image_data = base64_decode($base_64_image);
            $obj = new FileRepositoryS3();
            $res = $obj->uploadFileToAWS($image_data, $file_name);
            if ($res) {
                return $file_name;
            }
            return false;
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
    }

    /**
     * delete image from S3
     * @param string image
     * @return boolean/string
     */
    public static function deleteImageFromS3($image) {
        try {
            $obj = new FileRepositoryS3();
            $res = $obj->deleteFileFromAWS($image);
            if ($res) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
    }

    public static function ratingBucket($rating) {
        $low_rating = $rating - 1;
        $high_rating = $rating + 1;
        if ($rating == 1) {
            $low_rating = 1;
        }
        if ($rating == 5) {
            $high_rating = 5;
        }
        return array($low_rating, $rating, $high_rating);
    }

    /* execute background script to send push notification by admin
     * @param array $data
     */

    public static function sendPushNotification($data) {
        $data['notification_message'] = str_replace("'", "", $data['notification_message']);
        $data['notification_title'] = str_replace("'", "", $data['notification_title']);
        $url = url('/admin/send-push-notification-by-admin');
        exec('curl -H "Accept: application/json"  -H "Content-type: application/json" -X POST -d ' . "'" . json_encode($data) . "'" . ' ' . $url . '  > /dev/null &');
    }

    /* execute background script to send mail to admin for user excel
     * @param array $data
     */

    public static function sendUserExcelMail($data) {
        $url = url('/admin/send-user-excel-mail');
        exec('curl -H "Accept: application/json"  -H "Content-type: application/json" -X POST -d ' . "'" . json_encode($data) . "'" . ' ' . $url . '  > /dev/null &');
    }

    /* execute background script to delete images from s3 server
     * @param array $data
     */

    public static function iniateImageDeleteRequest($data) {
        $url = url('/api/v1/image/delete-from-s3');
        exec('curl -H "Accept: application/json"  -H "Content-type: application/json" -X POST -d ' . "'" . json_encode($data) . "'" . ' ' . $url . '  > /dev/null &');
        return true;
    }

    public static function validateInAppReceipt($environment, $receipt) {
        // determine which endpoint to use for verifying the receipt
        if ($environment == 'sandbox') {
            $endpoint = config('receipt_verification.apple.sandbox');
        } else {
            $endpoint = config('receipt_verification.apple.production');
        }

        // build the post data
        $post_data = json_encode(array('receipt-data' => $receipt, 'password' => $endpoint['password']));

        // create the cURL request
        $ch = curl_init($endpoint['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // execute the cURL request and fetch response data
        $response = curl_exec($ch);
        curl_close($ch);

        // parse the response data
        $data = json_decode($response);

        $result = array(
            'receipt_data' => $data,
            'status' => 1
        );
        if (is_object($data) && $data->status == 0) {
            return $result;
        } else {
            $result['status'] = 0;
            return $result;
        }
    }

    public static function generateDate($milliseconds) {
        //date is in Etc/GMT time zone.
        $timestamp = $milliseconds / 1000;
        return date("Y-m-d H:i:s", $timestamp);
    }

}
