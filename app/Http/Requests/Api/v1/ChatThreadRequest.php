<?php

namespace App\Http\Requests\Api\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;

class ChatThreadRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'chat_thread_id' => 'required|exists:chat_window,id,deleted_at,NULL',
        ];
    }

    /**
     * Custom error messages of the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            'chat_thread_id.exists' => 'Sorry, This Chat Thread has been deleted.',
            'chat_thread_id.required' => 'Chat thread id is required.'
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
     * Adding named route parameters into JSON data
     *
     * @return array
     */
    public function all() {
        $data = parent::all();
        $data['chat_thread_id'] = $this->segment(4);
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
