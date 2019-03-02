<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class ChangePasswordRequest extends Request {

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
        return [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ];
    }

}
