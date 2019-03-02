<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class ChangeImagePositionRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'imageData.*.position' => 'required|in:0,1,2,3,4,5,6,7',
            'imageData.*.imageId' => 'exists:image,id,deleted_at,NULL',
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
