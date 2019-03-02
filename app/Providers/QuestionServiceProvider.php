<?php

namespace App\Providers;

use App\Models\Question;

/**
 * QuestionServiceProvider class contains methods for user management
 */
class QuestionServiceProvider extends BaseServiceProvider {

    /**
     * fetch questions list
     * @return array
     */
    public static function fetchQestionsList() {
        $questions = Question::select('id as question_id', 'question')->get();
        if (is_object($questions)) {
            static::$response['result'] = array('questions' => $questions);
            static::$response['success'] = config('constants.status.success');
        } else {
            static::$response['success'] = config('constants.status.fail');
            static::$response['message'] = trans('messages.fail.question_not_found');
        }
        return static::$response;
    }

}
