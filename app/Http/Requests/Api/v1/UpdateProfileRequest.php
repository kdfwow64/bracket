<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class UpdateProfileRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'imageData.*.position' => 'required|in:0,1,2,3,4,5,6,7',
            'imageData.*.imageId' => 'exists:image,id,deleted_at,NULL',
            'deletedImageIds.*' => 'required|exists:image,id,deleted_at,NULL',
            'latitude' => 'required|string|max:50',
            'longitude' => 'required|string|max:50',
            'school' => 'required|string|max:250',
            'occupation' => 'required|string|max:250',
            'height' => 'integer|max:150',
            'age' => 'required|integer|min:18',
            'gender' => 'required|boolean',
            'aboutMe' => 'required|string|max:255',
            'country' => 'required|string|max:250',
            'questionOneId' => 'exists:question,id',
            'questionOneAnswer' => 'string|max:255|nullable',
            'questionTwoId' => 'exists:question,id',
            'questionTwoAnswer' => 'string|max:255|nullable',
            'questionThreeId' => 'exists:question,id',
            'questionThreeAnswer' => 'string|max:255|nullable'
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
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
