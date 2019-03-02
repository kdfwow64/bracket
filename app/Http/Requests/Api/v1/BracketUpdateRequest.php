<?php

namespace App\Http\Requests\Api\v1;

use App\Http\Requests\BaseApiRequest;

class BracketUpdateRequest extends BaseApiRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'winnersIds.*' => 'required|exists:user,id,role,2',
            'looserIds.*' => 'required|exists:user,id,role,2',
            'roundResult' => 'required|in:1,2,3,4,5',
            'bracketId' => 'required|exists:bracket,id,deleted_at,NULL,is_completed,0'
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
