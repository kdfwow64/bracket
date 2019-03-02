<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;

class DeleteImageRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'image_id' => 'required|exists:image,id,deleted_at,NULL',
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
     * Adding named route parameters into JSON data
     *
     * @return array
     */
    public function all() {
        $data = parent::all();
        $data['image_id'] = $this->segment(4);
        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
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
