<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class PushNotificationRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules() {
        if (Request::get('send_to_radios') == 'selected_users') {
            $user_validation = 'required';
        } else {
            $user_validation = '';
        }

        if (Request::get('send_to_radios') == 'selected_location') {
            $location_validation = 'required';
        } else {
            $location_validation = '';
        }
        return [
            'notification_title' => 'required|max:100',
            'notification_message' => 'required|max:250',
            'send_to_radios' => 'required',
            'js_users' => $user_validation,
            'js_location' => $location_validation
        ];
    }

}
