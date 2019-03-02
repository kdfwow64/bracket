<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Support\Facades\Config;

class SignInRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => 'email|max:50',
            'facebookId' => 'required|numeric',
            'age' => 'required|integer|min:18',
            'occupation' => 'string|min:0|max:250',
            'school' => 'string|min:0|max:250',
            'firstName' => 'required|max:25',
            'lastName' => 'required|max:25',
            'tzDiffSec' => 'required|max:8',
            'deviceInfo.deviceType' => 'required|in:' . Config::get('constants.device_type.ios'),
            'deviceInfo.deviceToken' => 'required|max:100',
            'gender' => 'boolean',
            'fb_image_url_1' => 'sometimes|url',
            'fb_image_url_2' => 'sometimes|url',
            'fb_image_url_3' => 'sometimes|url',
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'deviceInfo.deviceType.required' => 'Device type is required.',
            'deviceInfo.deviceType.in' => 'Device type must be integer and value = 1.',
            'deviceInfo.deviceToken.required' => 'Device token is required.',
            'deviceInfo.deviceToken.max' => 'Device token must be of maximum 100 characters.',
            'gender.boolean' => 'Gender must be either male or female.'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

}
