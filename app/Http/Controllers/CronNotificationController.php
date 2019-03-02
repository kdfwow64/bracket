<?php

namespace App\Http\Controllers;

use App\Providers\NotificationServiceProvider;
use App\Http\Controllers\Controller;
use App\Utility\CommonUtility;

/**
 * CronNotificationController class contains methods for user management
 */
class CronNotificationController extends Controller {

    public function __construct() {
        parent:: __construct();
    }

    /**
     * fetch users to send bracket 11AM, 2PM and 4PM push
     * @return json
     */
    public function bracketRoundStartPushNotification() {
        try {
            $response = NotificationServiceProvider::bracketRoundStartPush();
            if ($response['success'] === 1) {
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    public function chatThreadPushNotification() {
        try {
            $response = NotificationServiceProvider::chatThreadUltimatumPush();
            if ($response['success'] === 1) {
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    public function inAppReceiptValidator() {
        try {
            $response = NotificationServiceProvider::inAppReceiptValidator();
            if ($response['success'] === 1) {
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    public function bracketCountManager() {
        try {
            $response = NotificationServiceProvider::bracketCountManager();
            if ($response['success'] === 1) {
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

}
