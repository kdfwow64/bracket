<?php

namespace App\Http\Controllers\Api\v1;

use App\Utility\CommonUtility;
use App\Providers\ImageServiceProvider;
use App\Http\Requests\Api\v1\UploadImageRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * ImageController class contains methods for image management
 */
class ImageController extends Controller {

    public function __construct() {
        $this->middleware('validateJson', ['except' => ['store']]);
        $this->middleware('apiAuth', ['except' => ['deleteImagesFromS3']]);
        parent:: __construct();
    }

    /**
     * Upload user image 
     * @param UploadImageRequest $request
     * @return type JSON
     */
    public function store(UploadImageRequest $request) {
        try {
            $response = ImageServiceProvider::uploadImage($request->file('image'), $request->isProfileImage, $request->imagePosition, $request->user_id);
            if ($response['status'] === 1) {
                $return = $this->responseSuccess(trans('messages.success.image_upload'), $response['result']);
            } else {
                $return = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $return = $this->responseServerError(trans('messages.error.exception'));
        }
        return $return;
    }

    /**
     * delete images form s3 server
     * @param Request $request
     * @return type
     */
    public function deleteImagesFromS3(Request $request) {
        try {
            $images = json_decode($request->getContent(), true);
            $response = ImageServiceProvider::deleteImagesFromS3($images);
            if ($response['status'] === 1) {
                $return = $this->responseSuccess($response['message'], $response['result']);
            } else {
                $return = $this->responseNotFound($response['message']);
            }
        } catch (\Exception $e) {
            CommonUtility::logException(__METHOD__, $e);
            $return = $this->responseServerError(trans('messages.error.exception'));
        }
        return $return;
    }

}
