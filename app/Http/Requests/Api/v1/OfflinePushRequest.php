<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class OfflinePushRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'senderId' => 'required|exists:user,id,deleted_at,NULL',
            'receiverId' => 'required|exists:user,id,deleted_at,NULL',
            'threadId' => 'required|exists:chat_window,id',
            'message' => 'required|max:250',
            'accessToken' => 'required'
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
