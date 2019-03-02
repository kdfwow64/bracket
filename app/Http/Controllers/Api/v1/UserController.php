<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\UserServiceProvider;
use App\Http\Requests\Api\v1\SignInRequest;
use App\Http\Requests\Api\v1\UpdateProfileRequest;
use App\Http\Requests\Api\v1\UpdatePreferenceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\UpdatePushStatusRequest;
use App\Http\Requests\Api\v1\ViewProfileRequest;
use App\Http\Requests\Api\v1\MutualFriendsRequest;
use App\Http\Requests\Api\v1\BlockUserRequest;
use App\Http\Requests\Api\v1\DeleteUserRequest;
use App\Http\Requests\Api\v1\UpdateCountryRequest;

/**
 * UserController class contains methods for user management
 */
class UserController extends Controller {

    public function __construct() {

        $this->middleware('validateJson', ['except' => ['postSignOut', 'show', 'destroy', 'getBlockedUsers']]);
        $this->middleware('apiAuth', ['except' => ['postSignIn', 'postSignOut']]);
        parent:: __construct();
    }

    /**
     * SignIn user
     * @param SignInRequest $request
     * @return type JSON
     */
    public function postSignIn(SignInRequest $request) {
        try {
            DB::beginTransaction();
            $request_data = json_decode($request->getContent(), true);
            $response = UserServiceProvider::postSignIn($request_data);
            if ($response['success'] === 1) {
                DB::commit();
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * Update user profile data
     * @param UpdateProfileRequest $request
     * @return type JSON
     */
    public function postUpdateProfile(UpdateProfileRequest $request) {
        try {
            $request_data = json_decode($request->getContent(), true);
            $request_data['user_id'] = $request->user_id;
            $response = UserServiceProvider::updateUserProfile($request_data);
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

    /**
     * Update user preference data
     * @param UpdatePreferenceRequest $request
     * @return type JSON
     */
    public function postUpdatePreference(UpdatePreferenceRequest $request) {
        try {
            $request_data = json_decode($request->getContent(), true);
            $request_data['user_id'] = $request->user_id;
            $response = UserServiceProvider::updateUserPreference($request_data);
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

    /**
     * sign out user
     * @param Request $request
     * @return type
     */
    public function postSignOut(Request $request) {
        try {
            $response = UserServiceProvider::userSignOut($request->header('accessToken'));
            $response = $this->responseSuccess($response['message'], $response['result']);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * update push on/off status
     * @param UpdatePushStatusRequest $request
     * @return type JSON
     */
    public function postUpdatePushStatus(UpdatePushStatusRequest $request) {
        try {
            $request_data = json_decode($request->getContent(), true);
            $request_data['user_id'] = $request->user_id;
            $response = UserServiceProvider::updatePushStatus($request_data);
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

    /**
     * soft delete user
     * @param Request $request
     * @return type JSON
     */
    public function destroy(DeleteUserRequest $request, $delete_user_id) {
        try {
            DB::beginTransaction();
            $response = UserServiceProvider::deleteUser($request->user_id, $delete_user_id);
            if ($response['success'] === 1) {
                DB::commit();
                $response = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $response = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * to view any user profile
     * @param ViewProfileRequest $request
     * @return json
     */
    public function show(ViewProfileRequest $request) {
        try {
            $response = UserServiceProvider::viewProfile($request->view_user_id);
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

    /**
     * to fetch mutual friends data
     * @param MutualFriendsRequest $request
     * @return json
     */
    public function postFetchMutualFriends(MutualFriendsRequest $request) {
        try {
            $response = UserServiceProvider::fetchMutualFriends($request->facebookIds, $request->user_id);
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

    /**
     * to block any user
     * @param MutualFriendsRequest $request
     * @return json
     */
    public function postBlockUnblockUser(BlockUserRequest $request) {
        try {
            $request_data = json_decode($request->getContent(), true);
            $response = UserServiceProvider::blockUnblockUser($request->user_id, $request_data['blockedUserId'], $request_data['reason']);
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

    /**
     * to fetch blocked users
     * @param Request $request
     * @return json
     */
    public function getBlockedUsers(Request $request) {
        try {
            $response = UserServiceProvider::fetchBlockedUsers($request->user_id);
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

    /**
     * to update user country
     * @param Request $request
     * @return json
     */
    public function postUpdateUserCountry(UpdateCountryRequest $request) {
        try {
            $response = UserServiceProvider::updateUserCountry($request->user_id, $request->all());
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
