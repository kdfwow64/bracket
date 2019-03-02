<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlockedUser;
use App\Models\PushNotification;
use App\Models\Receipt;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\ChatWindow;
use DB;
use App\Providers\ImageServiceProvider;

class User extends Authenticatable {

    use SoftDeletes;

    use Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * method to create user
     * @param type array
     * @return type integer
     */
    public static function createUser($data) {
        if (empty($data['email'])) {
            $data['email'] = '';
        }
        if (empty($data['occupation'])) {
            $data['occupation'] = '';
        }
        if (empty($data['school'])) {
            $data['school'] = '';
        }
        $user_default_values = config('constants.user_default_value');
        if (!isset($data['gender'])) {
            $data['gender'] = $user_default_values['gender'];
            $data['prefer_gender'] = $user_default_values['prefer_gender'];
        } else {
            $data['prefer_gender'] = 1;
            if ($data['gender'] == 1) {
                $data['prefer_gender'] = 0;
            }
        }

        $user_data = array(
            'age' => $data['age'],
            'occupation' => $data['occupation'],
            'school' => $data['school'],
            'email' => $data['email'],
            'facebook_id' => $data['facebookId'],
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'ejabberd_username' => '',
            'ejabberd_password' => '',
            'role' => config('constants.user_type.app_user'),
            'tz_diff_sec' => $data['tzDiffSec'],
            'gender' => $data['gender'],
            'start_radius' => $user_default_values['start_radius'],
            'end_radius' => $user_default_values['end_radius'],
            'start_age' => $user_default_values['start_age'],
            'end_age' => $user_default_values['end_age'],
            'prefer_gender' => $data['prefer_gender'],
            'first_sign_in' => config('constants.status.success'),
            'is_profile_completed' => config('constants.status.success'),
        );
        $user_id = User::insertGetId($user_data);
        $user_default_push = config('constants.push_notification.default');
        foreach ($user_default_push as $key => $value) {
            $user_default_push[$key]['receiver_user_id'] = $user_id;
        }
        $jabber_constants = config('environment.ejjaber');
        $jabber_username = $jabber_constants['server'] . '_' . str_replace(" ", "_", $data['firstName']) . '_' . time();
        $jabber_password = HASH::make($jabber_username);
        $host = $jabber_constants['host'];
        $command = 'register';
        $param_auth = array("user" => "admin", "server" => "localhost", "password" => "@ppst3r@123");
        $param_comm = array('user' => $jabber_username, 'host' => $host, 'password' => $jabber_password);
        $params = array($param_auth, $param_comm);
        $request = xmlrpc_encode_request($command, $params, (array('encoding' => 'utf-8')));
        $context = stream_context_create(array('http' => array(
                'method' => "POST",
                'header' => "User-Agent: XMLRPC::Client mod_xmlrpc\r\n" .
                "Content-Type: text/xml\r\n" .
                "Content-Length: " . strlen($request),
                'content' => $request
        )));
        $file = file_get_contents($jabber_constants['rpc_server'], false, $context);
        $response = xmlrpc_decode($file);
        if (!xmlrpc_is_fault($response)) {
            User::where('id', $user_id)->update(['ejabberd_username' => $jabber_username, 'ejabberd_password' => $jabber_password]);
        }
        PushNotification::insert($user_default_push);
        if (!empty($data['fb_image_url_1'])) {
            ImageServiceProvider::uploadFaceBookImage($data['fb_image_url_1'], $user_id, 1, 0);
        }
        if (!empty($data['fb_image_url_2'])) {
            ImageServiceProvider::uploadFaceBookImage($data['fb_image_url_2'], $user_id, 0, 1);
        }
        if (!empty($data['fb_image_url_3'])) {
            ImageServiceProvider::uploadFaceBookImage($data['fb_image_url_3'], $user_id, 0, 2);
        }
        return User::getUserProfile($user_id, config('constants.search.user_id'), 3);
    }

    /**
     * method to update user profile data
     * @param type array
     * @return type array
     */
    public static function updateUserProfile($data) {
        if (!empty($data['imageData'])) {
            foreach ($data['imageData'] as $image) {
                Image::where('id', $image['imageId'])
                        ->update(['image_position' => $image['position']]);
                if ($image['position'] == 0) {
                    $image = Image::select('image_name')->where('id', $image['imageId'])->first();
                    User::where('id', $data['user_id'])->update(['profile_picture' => $image->getOriginal('image_name')]);
                }
            }
        }

        if (!empty($data['deletedImageIds'])) {
            Image::deleteImages($data['deletedImageIds'], $data['user_id']);
        }
        if ($data['age'] > 99) {
            $data['age'] = 99;
        }
        $user_profile_data = array(
            'age' => $data['age'],
            'gender' => $data['gender'],
            'height' => $data['height'],
            'occupation' => $data['occupation'],
            'school' => $data['school'],
            'about_me' => $data['aboutMe'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'country' => $data['country'],
            'is_profile_completed' => config('constants.status.success'),
        );


        if (!empty($data['questionOneId']) && !empty($data['questionOneAnswer'])) {
            $user_profile_data['question_1_id'] = $data['questionOneId'];
            $user_profile_data['question_1_answer'] = $data['questionOneAnswer'];
        } else {
            $user_profile_data['question_1_id'] = NULL;
            $user_profile_data['question_1_answer'] = '';
        }

        if (!empty($data['questionTwoId']) && !empty($data['questionTwoAnswer'])) {
            $user_profile_data['question_2_id'] = $data['questionTwoId'];
            $user_profile_data['question_2_answer'] = $data['questionTwoAnswer'];
        } else {
            $user_profile_data['question_2_id'] = NULL;
            $user_profile_data['question_2_answer'] = '';
        }

        if (!empty($data['questionThreeId']) && !empty($data['questionThreeAnswer'])) {
            $user_profile_data['question_3_id'] = $data['questionThreeId'];
            $user_profile_data['question_3_answer'] = $data['questionThreeAnswer'];
        } else {
            $user_profile_data['question_3_id'] = NULL;
            $user_profile_data['question_3_answer'] = '';
        }

        $user_profie_complete = User::where('id', $data['user_id'])->select('is_profile_completed', 'is_registered_country_set')->first();
        if ($user_profie_complete->is_profile_completed == 0) {
            $user_profile_data['prefer_gender'] = 1;
            if ($data['gender'] == 1) {
                $user_profile_data['prefer_gender'] = 0;
            }
        }

        if ($user_profie_complete->is_registered_country_set == 0) {
            $user_profile_data['registered_country'] = $data['country'];
            $user_profile_data['is_registered_country_set'] = 1;
        }

        User::where('id', $data['user_id'])->update($user_profile_data);
        PushNotification::where('type', 3)
                ->where('receiver_user_id', $data['user_id'])->delete();
        return User::getUserProfile($data['user_id'], config('constants.search.user_id'), 3);
    }

    /**
     * method to update user preference data
     * @param type array
     * @return type array
     */
    public static function updateUserPreference($data) {
        $user_preference_data = array(
            'start_radius' => $data['startRadius'],
            'end_radius' => $data['endRadius'],
            'prefer_gender' => $data['preferGender'],
            'start_age' => $data['startAge'],
            'end_age' => $data['endAge'],
        );
        User::where('id', $data['user_id'])->update($user_preference_data);
        return User::getUserProfile($data['user_id'], config('constants.search.user_id'), 3);
    }

    /**
     * fetch user profile based on id and flag
     * @param type integer
     * @return type array
     */
    public static function getUserProfile($id, $flag, $select_flag, $deleted_user = NULL) {
        $select = User::generateUserProfileSelectStatement($select_flag);
        $user = User::select($select);
        if ($flag === config('constants.search.user_id')) {
            $user = $user->where('user.id', $id);
        } else if ($flag === config('constants.search.facebook_id')) {
            $user = $user->where('facebook_id', $id);
        }
        if ($deleted_user) {
            $user = $user->withTrashed()->first();
        } else {
            $user = $user->first();
        }
        if (is_object($user) && $select_flag != 1) {
            $user['gallery'] = Image::select('id as imageId', 'image_name', 'image_position', 'image_name as thumb_name')->where('user_id', $user->user_id)->get();
        }
        if (is_object($user)) {
            $bracket = Bracket::select('id')->where('user_id', $user->user_id)->first();
            $user['first_bracket_played'] = 0;
            $user['is_running_bracket'] = 0;
            if (is_object($bracket)) {
                $user['first_bracket_played'] = 1;
            }
            $latest_bracket = Bracket::select('is_completed')
                    ->where('user_id', $user->user_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
            if ((is_object($latest_bracket)) && ($latest_bracket->is_completed == 0)) {
                $user['is_running_bracket'] = 1;
            }
        }
        return $user;
    }

    /**
     * update user push on off flag
     * @param type integer
     * @return type boolean
     */
    public static function updatePushStatus($user_id, $flag) {
        return User::where('id', $user_id)->update(['is_push_notification' => $flag]);
    }

    /**
     * fetch select statement for user profile
     * @param type $flag
     * @return type array
     */
    public static function generateUserProfileSelectStatement($flag) {
        $select = array('user.id as user_id', 'is_profile_completed', 'facebook_id',
            'profile_picture',
            'profile_picture as thumb_picture',
            'ejabberd_username', 'ejabberd_password', 'first_name',
            'last_name', 'email', 'first_sign_in', 'role', 'is_push_notification', 'average_rating', 'is_paid',
            'free_bracket_count', 'paid_bracket_count', 'earn_bracket_count', 'chat_badge');
        if ($flag == 1) {
            //
        } else if ($flag == 2) {
            $select = array_merge($select, array('age', 'gender', 'height', 'occupation', 'school',
                'about_me', 'country', 'latitude', 'longitude',
                'question_1_id as questionOneId',
                'question_1_answer as questionOneAnswer', 'question_2_id as questionTwoId',
                'question_2_answer as questionTwoAnswer', 'question_3_id as questionThreeId',
                'question_3_answer as questionThreeAnswer'));
        } else if ($flag == 3) {
            $select = array_merge($select, array('age', 'gender', 'height', 'occupation', 'school',
                'about_me', 'country', 'latitude', 'longitude', 'start_radius',
                'end_radius', 'prefer_gender', 'start_age',
                'question_1_id as questionOneId', 'end_age',
                'question_1_answer as questionOneAnswer', 'question_2_id as questionTwoId',
                'question_2_answer as questionTwoAnswer', 'question_3_id as questionThreeId',
                'question_3_answer as questionThreeAnswer'));
        } else if ($flag == 4) {
            $select = array('user.id as user_id', 'facebook_id', 'profile_picture', 'profile_picture as thumb_picture', 'first_name', 'last_name');
        } else if ($flag == 5) {
            $select = array('user.id as user_id', 'profile_picture', 'profile_picture as thumb_picture', 'first_name', 'last_name',
                'age', 'country', 'occupation', 'prefer_gender', 'gender');
        }
        return $select;
    }

    /**
     * Get profile image path attribute
     * @return string
     */
    public function getProfilePictureAttribute() {
        $profile_image_path = '';
        if ($this->attributes['profile_picture']) {
            $image_name = explode('.', $this->attributes['profile_picture']);
            $profile_image_path = config('environment.s3_url') . '600X600/' . $image_name[0] . '.jpeg';
        }
        return $profile_image_path;
    }

    /**
     * soft delete user
     * @return boolean
     */
    public static function deleteUser($user_id) {
        Rating::where('rating_to_user_id', $user_id)
                ->orWhere('rating_by_user_id', $user_id)->delete();
        BlockedUser::where('blocked_user_id', $user_id)
                ->orWhere('blocked_by_user_id', $user_id)->delete();
        $user_brackets_id = Bracket::where('user_id', $user_id)->pluck('id')->toArray();
        Bracket::whereIn('id', $user_brackets_id)->delete();
        BracketMember::whereIn('bracket_id', $user_brackets_id)->delete();
        BracketMember::where('user_id', $user_id)->delete();
        $user_subscription_id = Subscription::where('user_id', $user_id)->pluck('id')->toArray();
        Transaction::whereIn('subscription_id', $user_subscription_id)->delete();
        Receipt::whereIn('subscription_id', $user_subscription_id)->delete();
        Subscription::whereIn('id', $user_subscription_id)->delete();
        ChatWindow::where('user_id', $user_id)
                ->orWhere('winner_user_id', $user_id)->delete();
        return User::where('id', $user_id)->delete();
    }

    /**
     * to fetch blocked users
     * @param type $user_id
     * @return type object
     */
    public static function fetchBlockedUser($user_id) {
        $select = User::generateUserProfileSelectStatement(3);
        $users_id = BlockedUser::where('blocked_by_user_id', $user_id)
                        ->pluck('blocked_user_id')->toArray();
        $users = User::select($select)
                ->whereIn('id', $users_id)
                ->get();
        if (is_object($users)) {
            foreach ($users as $user) {
                $user['is_blocked'] = 1;
            }
            return $users;
        }
    }

    /**
     * Get user id on the basis of email
     * @param string $email
     * @return array
     */
    public static function getUserByEmail($email) {
        $user_arr = User::where('user.email', $email)->where('role', config('constants.user_type.admin'))
                ->first();
        return json_decode($user_arr);
    }

    /**
     * Update password
     * @param integer $user_id
     * @param string  $new_password
     * @param integer $is_reset_password
     */
    public static function updatePassword($user_id, $new_password, $is_reset_password = 0) {
        User::where('id', $user_id)->update([
            'password' => Hash::make($new_password),
            'is_reset_password' => $is_reset_password
        ]);
    }

    /**
     * Get user list
     * @return array
     */
    public static function getUser($pagination = NULL) {

        if ($pagination != 'on') {
            $user_arr = User::where('user.role', '!=', config('constants.user_type.admin'))->paginate(config('constants.record_per_page'))->toArray();
            $user_arr['items'] = $user_arr['data'];
        } else {
            DB::statement(DB::raw('set @rownum:=0'));
            $user_arr = User::select(DB::raw('(@rownum := @rownum + 1) AS rowNumber'),'first_name', 'last_name', DB::raw("(CASE when email = '' then 'Not Shared' else email end ) as email"), DB::raw("(CASE when age = '' then 'Not Shared' else age end ) as age"), DB::raw("(CASE when gender = 1 then 'Male' else 'Female' end ) as gender"), DB::raw("(CASE when registered_country = '' then 'Not Shared' else registered_country end ) as registered_country"), DB::raw("(CASE when school = '' then 'Not Shared' else school end ) as school"), DB::raw("(CASE when height = '' then 'Not Shared' else height end ) as height"), DB::raw("(CASE when about_me = '' then 'Not Shared' else about_me end ) as bio"), DB::raw("(CASE when user.created_at = '' then 'Not Shared' else DATE_FORMAT(CONVERT_TZ(user.created_at,'+00:00',tz_diff_sec),'%m-%d-%Y %H:%i') end ) as registered_date"), DB::raw("(CASE when q1.question = '' then 'Not Shared' else q1.question end ) as question1"), DB::raw("(CASE when question_1_answer = '' then 'Not Shared' else question_1_answer end ) as answer1"), DB::raw("(CASE when q2.question = '' then 'Not Shared' else q2.question end ) as question2"), DB::raw("(CASE when question_2_answer = '' then 'Not Shared' else question_2_answer end ) as answer2"), DB::raw("(CASE when q3.question = '' then 'Not Shared' else q3.question end ) as question3"), DB::raw("(CASE when question_3_answer = '' then 'Not Shared' else question_3_answer end ) as answer3"))
                    ->leftjoin('question as q1', 'q1.id', '=', 'user.question_1_id')
                    ->leftjoin('question as q2', 'q2.id', '=', 'user.question_2_id')
                    ->leftjoin('question as q3', 'q3.id', '=', 'user.question_3_id')
                    ->where('user.role', '!=', config('constants.user_type.admin'))
                    ->whereNull('user.deleted_at')
                    ->get()
                    ->toArray();
        }

        return $user_arr;
    }

    /**
     * Get searched users
     * @param array $request
     * @return array
     */
    public static function getSearchUser($request) {
        
        if (isset($request['query'])) {
            $query = $request['query'];
        }
        $user_arr = User::where('user.role', '!=', config('constants.user_type.admin'));
        if (isset($request['push_notification']) && $request['push_notification'] == "user_search") {
            $user_arr = $user_arr->where('user.email', $query)
                    ->orWhere(function ($where_query) use ($query) {
                $where_query->orWhere('user.first_name', 'like', '%' . $query . '%')
                ->orWhere('user.last_name', 'like', '%' . $query . '%');
            });
        } else if (isset($request['push_notification']) && $request['push_notification'] == "location_search") {
            if (!isset($request['query'])) {
                $user_arr = $user_arr->groupBy('user.country');
            } else {
                $user_arr = $user_arr->where('user.country', 'like', '%' . $query . '%')
                        ->where('user.country', '!=', '')
                        ->groupBy('user.country');
            }
        } else {
            $user_arr = $user_arr->where('user.email', $query)
                    ->orWhere(function ($where_query) use ($query) {
                $where_query->orWhere('user.first_name', 'like', '%' . $query . '%')
                ->orWhere('user.last_name', 'like', '%' . $query . '%')
                ->orWhere('user.country', 'like', '%' . $query . '%');
            });
        }
        $user_arr = $user_arr
                ->where('user.role', '!=', config('constants.user_type.admin'))
                ->paginate(config('constants.record_per_page'))->toArray();
        $user_arr['items'] = $user_arr['data'];
        return $user_arr;
    }

    /**
     * Question 1 posted by date
     * @return array
     */
    function userQuestion1() {
        return $this->hasOne('App\Models\Question', 'id', 'question_1_id');
    }

    /**
     * Question 2 posted by dater
     * @return array
     */
    function userQuestion2() {
        return $this->hasOne('App\Models\Question', 'id', 'question_2_id');
    }

    /**
     * Question 3 posted by dater
     * @return array
     */
    function userQuestion3() {
        return $this->hasOne('App\Models\Question', 'id', 'question_3_id');
    }

    /**
     * Get user by user_id
     * @param integer user_id
     * @return object
     */
    public static function getUserById($user_id) {
        $query = User::where('id', $user_id)->with(['userQuestion1', 'userQuestion2', 'userQuestion3']);
        return $query->first();
    }

    /**
     * Get gender analytics
     * @return array
     */
    public static function genderAnalytics($request = NULL) {
        $gender_const = config('constants.gender');
        $gender_analytics['male'] = User::where('user.gender', $gender_const['male'])->where('user.role', '!=', config('constants.user_type.admin'));
        if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $gender_analytics['male'] = $gender_analytics['male']->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $gender_analytics['male'] = $gender_analytics['male']->count();

        $gender_analytics['female'] = User::where('user.gender', $gender_const['female'])->where('user.role', '!=', config('constants.user_type.admin'));
        if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $gender_analytics['female'] = $gender_analytics['female']->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $gender_analytics['female'] = $gender_analytics['female']->count();
        return $gender_analytics;
    }

    /**
     * Get age analytics
     * @return array
     */
    public static function ageAnalytics($request = NULL) {
// Calculate maximum age available in our records
        $max_age = User::where('user.role', '!=', config('constants.user_type.admin'))->max('age');
// Array of age groups
        $age_arr[] = '18 - 25';
        if ($max_age > 99) {
            $max_age = 99;
        }
        for ($i = 26; $i <= $max_age; $i = $i + 5) {
            if ($i + 4 > 99) {
                $upper_limit = 99;
                $lower_limit = 96;
            } else {
                $upper_limit = $i + 4;
                $lower_limit = $i;
            }
            $age_arr[] = $lower_limit . ' - ' . $upper_limit;
        }
// Array of number of users in each age group
        foreach ($age_arr as $value) {
            $age_arr = explode(' - ', $value);
            $age_analytics[$value] = User::where('user.role', '!=', config('constants.user_type.admin'))->whereBetween('age', [$age_arr[0], $age_arr[1]]);
            if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
                $from_date = $request['from_date'];
                $to_date = $request['to_date'];
                $age_analytics[$value] = $age_analytics[$value]->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
            }
            $age_analytics[$value] = $age_analytics[$value]->count();
        }
        return $age_analytics;
    }

    /**
     * Get occupation analytics
     * @return array
     */
    public static function occupationAnalytics($request = NULL) {
        $occupation_available = User::where('user.role', '!=', config('constants.user_type.admin'));
        if (isset($request) && !empty($request) && $request['from_date'] != "NaN/NaN/NaN NaN:NaN:NaN") {
            $from_date = $request['from_date'];
            $to_date = $request['to_date'];
            $occupation_available = $occupation_available->whereBetween('updated_at', [date('Y-m-d H:i:s', strtotime($from_date)), date('Y-m-d H:i:s', strtotime($to_date))]);
        }
        $occupation_available = $occupation_available->pluck('occupation')->toArray();
        $occupation_array = Array();
        foreach ($occupation_available as $occupation_name) {
            if ($occupation_name != "") {
                $occupation_array[] = ucwords($occupation_name);
            }
        }
        return array_count_values($occupation_array);
    }

    /**
     *
     * @param string/array $user
     * @return array
     */
    public static function fetchUserForNotification($user) {
        $user_arr = User::where('user.role', '!=', config('constants.user_type.admin'));
        if ($user == 'male') {
            $user_arr = $user_arr->where('user.gender', config('constants.gender.male'));
        }
        if ($user == 'female') {
            $user_arr = $user_arr->where('user.gender', config('constants.gender.female'));
        }
        if (is_array($user)) {
            $user_arr = $user_arr->whereIn('user.country', $user);
        }
        return $user_arr->pluck('id')->toArray();
    }

    /**
     *
     * @param integer $userid
     * @return string
     */
    public static function getLocationNameByUser($userid) {
        return User::where('user.id', $userid)->pluck('country')->first();
    }

    /**
     * send complete your profile push
     * @param type $user_id
     * @return type
     */
    public static function sendPushCompleteProfile($user_id) {
        $push_data = config('constants.push_notification.three');
        $push_data['sender_user_id'] = NULL;
        $push_data['receiver_user_id'] = $user_id;
        return PushNotification::sendPushNotification($push_data['sender_user_id'], $push_data['receiver_user_id'], $push_data);
    }

    /**
     * Get profile image path attribute
     * @return string
     */
    public function getThumbPictureAttribute() {
        $profile_image_path = '';
        if ($this->attributes['profile_picture']) {
            $image_name = explode('.', $this->attributes['profile_picture']);
            $profile_image_path = config('environment.s3_url') . '600X600/' . $image_name[0] . '.jpeg';
        }
        return $profile_image_path;
    }

}
