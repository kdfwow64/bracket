<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\ChatServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\v1\ThreadRequest;
use App\Http\Requests\Api\v1\ChatThreadRequest;
use App\Http\Requests\Api\v1\OfflinePushRequest;

/**
 * ChatController class contains methods for chat thread
 */
class ChatController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => ['index', 'destroy', 'update', 'show',
                'sendOfflinePush', 'clearOfflinePushBatch']]);
        $this->middleware('apiAuth', ['except' => ['sendOfflinePush']]);
        parent:: __construct();
    }

    /**
     * index method to fetch user chat thread
     * @param Request $request
     * @return type JSON
     */
    public function index(Request $request) {
        try {
            $response = ChatServiceProvider::fetchChatThreadList($request->user_id);
            $response = $this->responseSuccess($response['message'], $response['result']);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * destroy method to delete user chat thread
     * @param Request $request
     * @return type JSON
     */
    public function destroy(ChatThreadRequest $request) {
        try {
            $response = ChatServiceProvider::deleteChatThread($request->user_id, $request->chat_thread_id);
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
     * method to update chat thread
     * @param Request $request
     * @return type JSON
     */
    public function update(ChatThreadRequest $request) {
        try {
            $response = ChatServiceProvider::updateChatThread($request->user_id, $request->chat_thread_id);
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
     * show method to fetch user particular chat thread
     * @param Request $request
     * @return type JSON
     */
    public function show(ChatThreadRequest $request) {
        try {
            $response = ChatServiceProvider::fetchChatThreadList($request->user_id, $request->chat_thread_id);
            $response = $this->responseSuccess($response['message'], $response['result']);
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * send push for each offline message send
     * @param OfflinePushRequest $request
     * @return type
     */
    public function sendOfflinePush() {
        try {
            $post = file_get_contents('php://input');
            $input = $data = array();
            parse_str($post, $input);
            $data['from_ejabberd_id'] = $input['From'];
            $data['to_ejabberd_id'] = $input['To'];
            $data['message'] = $input['Body'];
            $response = '';
            if (!empty($data['message'])) {
                $response = ChatServiceProvider::sendOfflinePushMessage($data);
                if ($response['success'] === 1) {
                    $response = $this->responseSuccess($response['message'], $response['result']);
                } else {
                    $response = $this->responseNotFound($response['message']);
                }
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $response = $this->responseServerError(trans('messages.error.exception'));
        }
        return $response;
    }

    /**
     * clear offline message batch
     * @param ThreadRequest $request
     * @return type
     */
    public function clearOfflinePushBatch(ThreadRequest $request) {
        try {
            $response = ChatServiceProvider::clearOflineMesgBatch($request->user_id, $request->chat_thread_id);
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
