<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;

class DeleteUserRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'delete_user_id' => 'required|exists:user,id,deleted_at,NULL',
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
        $data['delete_user_id'] = $this->segment(4);
        return $data;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $data = parent::all();
        $data['delete_user_id'] = $this->segment(4);
        if ($data['delete_user_id'] == $data['user_id']) {
            return true;
        }
        return false;
    }

    /*
     * method to overide response
     */

    public function failedAuthorization() {
        $error_mesg = trans('messages.fail.unauthorized');
        $obj = new Controller();
        return $obj->responseBadRequest($error_mesg, array());
    }

}
