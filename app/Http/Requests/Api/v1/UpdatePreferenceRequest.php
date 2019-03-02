<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class UpdatePreferenceRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'startRadius' => 'required|integer|min:0|max:1000',
            'endRadius' => 'required|integer|min:0|max:1000',
            'preferGender' => 'required|boolean',
            'startAge' => 'required|required|integer|min:18|max:99',
            'endAge' => 'required|required|integer|min:18|max:99',
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'preferGender.boolean' => 'Gender must be either male or female.'
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
