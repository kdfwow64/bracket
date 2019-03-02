<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class InAppRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'type' => 'required|in:1,2',
            'bundleId' => 'required',
            'price' => 'required',
            'receipt_data' => 'required_if:type,2'
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
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
