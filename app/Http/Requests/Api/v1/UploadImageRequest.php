<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;

class UploadImageRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            'isProfileImage' => 'required|in:0,1',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:8000',
            'imagePosition' => 'required|in:0,1,2,3,4,5,6,7',
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'image.size' => 'Maxium file can be upload of 4MB.',
            'isProfileImage.in' => 'isProfileImage must be either 0 or 1.'
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

    /**
     * Adding header data into JSON data
     * @return array
     */
    public function all() {
        $data = parent::all();
        $data['isProfileImage'] = $this->header('isProfileImage');
        $data['imagePosition'] = $this->header('imagePosition');
        return $data;
    }

    /*
     * method to overide response
     */

    public function response(array $errors) {
        $first_error = '';
        foreach ($errors as $error) {
            $first_error = $error[0];
            break;
        }
        $obj = new Controller();
        return $obj->responseBadRequest($first_error, array());
    }

}
