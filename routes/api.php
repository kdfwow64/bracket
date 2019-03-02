<?php

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */


Route::group(['prefix' => 'v1', 'middleware' => (config('environment.log.api') ? ['apiLog'] : [])], function () {

    \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
        config('environment.log.query') ? Illuminate\Support\Facades\Log::info(['Query' => $query->sql, 'Query Time' => $query->time]) : '';
    });

    Route::group(['prefix' => 'user'], function() {
        Route::post('sign-in', 'Api\v1\UserController@postSignIn');
        Route::post('update-profile', 'Api\v1\UserController@postUpdateProfile');
        Route::post('update-preference', 'Api\v1\UserController@postUpdatePreference');
        Route::post('update-push-flag', 'Api\v1\UserController@postUpdatePushStatus');
        Route::post('sign-out', 'Api\v1\UserController@postSignOut');
        Route::post('mutual-friends', 'Api\v1\UserController@postFetchMutualFriends');
        Route::post('block', 'Api\v1\UserController@postBlockUnblockUser');
        Route::get('block-users', 'Api\v1\UserController@getBlockedUsers');
        Route::post('update-country', 'Api\v1\UserController@postUpdateUserCountry');
    });

    Route::get('terms-and-conditions', function () {
        return view('app/pages/terms-and-conditions');
    });

    Route::get('privacy-policy', function () {
        return view('app/pages/privacy-policy');
    });

    Route::group(['prefix' => 'image'], function() {
        Route::post('delete-from-s3', 'Api\v1\ImageController@deleteImagesFromS3');
    });

    Route::group(['prefix' => 'bracket'], function() {
        Route::post('update', 'Api\v1\BracketController@updateBracket');
        Route::post('send-push-to-wildcards', 'Api\v1\BracketController@sendPushToWildCards');
        Route::post('share', 'Api\v1\BracketController@shareBracket');
        Route::get('server-time', 'Api\v1\BracketController@getServerTime');
    });

    Route::group(['prefix' => 'chat-thread'], function() {
        Route::post('offline-push', 'Api\v1\ChatController@sendOfflinePush');
        Route::get('clear-offline-batch/{id}', 'Api\v1\ChatController@clearOfflinePushBatch');
    });

    Route::resource('user', 'Api\v1\UserController', ['only' => ['show', 'destroy',]]);
    Route::resource('image', 'Api\v1\ImageController', ['only' => ['store']]);
    Route::resource('push', 'Api\v1\PushNotificationController', ['only' => ['index', 'destroy', 'update']]);
    Route::resource('rating', 'Api\v1\RatingController', ['only' => ['index', 'store']]);
    Route::resource('question', 'Api\v1\QuestionController', ['only' => ['index']]);
    Route::resource('bracket', 'Api\v1\BracketController', ['only' => ['store']]);
    Route::resource('chat-thread', 'Api\v1\ChatController', ['only' => ['index', 'destroy', 'update', 'show']]);
    Route::resource('in-app', 'Api\v1\InAppController', ['only' => ['store']]);
});

/*
 *  temporary routes for devlopemnt purpose
 */
Route::get('v1/notification/eleven-am', 'CronNotificationController@bracketRoundStartPushNotification');
Route::get('v1/notification/chat', 'CronNotificationController@chatThreadPushNotification');
Route::get('v1/notification/in-app-validate', 'CronNotificationController@inAppReceiptValidator');
Route::get('v1/notification/bracket-count-manage', 'CronNotificationController@bracketCountManager');
