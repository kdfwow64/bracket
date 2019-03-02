<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Route::get('/', function () {
    return view('auth.login');
});


Auth::routes();

Route::get('/home', 'Admin\HomeController@index');

Route::post('/analytics-data-search-list', 'Admin\HomeController@filterAnalyticsData');

Route::post('/in-app-analytics-data', 'Admin\HomeController@inAppAnalyticsData');

Route::get('/downloadExcel', 'Admin\HomeController@downloadExcel');

Route::get('/downloadDatersExcel', 'Admin\UserController@downloadDatersCSV');

Route::get('/inAppDownloadExcel', 'Admin\HomeController@inAppDownloadExcel');

Route::post('/reset', 'Admin\UserController@resetPassword');

Route::get('/change-password', 'Admin\UserController@changePassword');

Route::post('/change-password', 'Admin\UserController@changePasswordSubmit');

Route::resource('/user', 'Admin\UserController');

Route::get('/search-user', 'Admin\UserController@searchUser');

Route::get('/user-list-ajax', 'Admin\UserController@userListByAjax');

Route::post('/user-rating-list', 'Admin\UserController@userRatingList');

Route::post('/user-rating-search-list', 'Admin\UserController@searchUserRatingList');

Route::resource('/blocked-user', 'Admin\BlockedController');

Route::get('/blocked-user-list-ajax', 'Admin\BlockedController@blockedUserListByAjax');

Route::get('/blockedby-user-list-ajax', 'Admin\BlockedController@blockedByUserListByAjax');

Route::post('/blocked-user-search-list', 'Admin\BlockedController@searchBlockedByUserList');

Route::resource('/unblocked-user', 'Admin\UnBlockedController');

Route::get('/unblocked-user-list-ajax', 'Admin\UnBlockedController@unBlockedUserListByAjax');

Route::get('/unblockedby-user-list-ajax', 'Admin\UnBlockedController@unBlockedByUserListByAjax');

Route::post('/unblocked-user-search-list', 'Admin\UnBlockedController@searchUnBlockedByUserList');

Route::resource('/wildcard-user', 'Admin\WildcardController');

Route::get('/wildcard-user-list-ajax', 'Admin\WildcardController@wildcardUserListByAjax');

Route::post('/wildcard-user-search-list', 'Admin\WildcardController@searchWildcardUserList');

Route::resource('/push-notification', 'Admin\PushNotificationController');

Route::get('/push-notification-list-ajax', 'Admin\PushNotificationController@pushNotificationListByAjax');

Route::post('/notification-recipient-list-ajax', 'Admin\PushNotificationController@notificationRecipientListByAjax');

Route::group(['middleware' => (env('ENABLE_LOG', 1) ? ['apiLog'] : [])], function () {
    Route::post('/send-push-notification-by-admin', 'Admin\PushNotificationController@sendPushNotificationByAdmin');
    Route::post('/send-user-excel-mail', 'Admin\UserController@sendUserExcelMail');
});

Route::resource('/in-app-purchase', 'Admin\InAppController');

Route::get('/in-app-user-list-ajax', 'Admin\InAppController@inAppUserListByAjax');
