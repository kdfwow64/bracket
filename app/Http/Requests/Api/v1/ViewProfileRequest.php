<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;

class ViewProfileRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'view_user_id' => 'required|exists:user,id',
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'view_user_id.exists' => 'View profile user does not exists.',
            'view_user_id.required' => 'View profile user id is required.'
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
     * Adding named route parameters into json data
     *
     * @return array
     */
    public function all() {
        $data = parent::all();
        $data['view_user_id'] = $this->segment(4);
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
