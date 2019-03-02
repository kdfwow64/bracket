<?php

namespace App\Repository;

use Storage;

/**
 * FileRepository contains method for file management with S3 server 
 */
class FileRepositoryS3 {

    protected $awsObj;

    public function __construct() {
        $this->awsObj = Storage::disk('s3');
    }

    /**
     * method to put file on s3
     * @param string $image
     * @param string $filename
     * @return int/string
     */
    public function uploadFileToAWS($image, $filename) {
        try {
            $res = $this->awsObj->put($filename, $image, 'public');
            if ($res != 1) {
                $res = 0;
            }
            return $res;
        } catch (\Exception $e) {
            //echo $e->getMessage();
        }
    }

    /**
     * method to delete file on s3
     * @param  string $filename
     */
    public function deleteFileFromAWS($filename) {
        if ($this->awsObj->exists($filename)) {
            return $this->awsObj->delete($filename);
        }
    }

}
