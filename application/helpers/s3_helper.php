<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'helpers/vendor/autoload_helper.php';
use Aws\S3\S3Client;

function s3_get_client () {
    $s3 = S3Client::factory([
        "credentials" => [
            "key" => AWS_ACCESSKEY,
            "secret" => AWS_SECRET_KEY,
        ],
        "region" => "us-east-1",
        "version" => "2006-03-01"
    ]);
    return $s3;
}

if ( ! function_exists( "upload_image_s3" ) ) {
    function upload_image_s3 ($image, $mimeType="") {
        $s3 = s3_get_client();
        
        $result = $s3->putObject(
            [
                'Bucket'       => AMAZONS3_BUCKET,
                'Key'          => $image['name'],
                'SourceFile'   => $image['tmp_name'],
                'ContentType'  => $mimeType,
                'ACL'          => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY'
            ]
        );

        return $result['ObjectURL'];
    }
}

if ( ! function_exists( "s3_image_uploader" ) ) {
    function s3_image_uploader($image, $imageName, $mimeType="") {
        $s3 = s3_get_client();
        
        $result = $s3->putObject(
            [
                'Bucket'       => AMAZONS3_BUCKET,
                'Key'          => $imageName,
                'SourceFile'   => $image['tmp_name'],
                'ContentType'  => $mimeType,
                'ACL'          => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY'
            ]
        );

        return $result['ObjectURL'];
    }
}


if ( ! function_exists( "validate_user_image" ) ) {
    function validate_user_image($image) {

        $validMimeTypes = ['image/png','image/jpg', 'image/jpeg', 'image/gif'];
        $validExtensions = ['jpg', 'png', 'jpeg', 'gif'];
        $fileMIME = mime_content_type($image['tmp_name']);
        $CI = &get_instance();
        $CI->load->language("common", "english");
        
        if ( ! in_array($fileMIME, $validMimeTypes) ) {
            return [
                "success" => false,
                "code" => NOT_AN_IMAGE,
                "message" => $CI->lang->line("not_an_image")
            ];
        }
        
        $imageSize = getimagesize($image['tmp_name']);
        
        
        if ( ! $imageSize || null === $imageSize) {
            return [
                "success" => false,
                "code" => NOT_AN_IMAGE,
                "message" => $CI->lang->line("not_an_image")
            ];
            
        } 
        
        if ( $image['size'] > MAX_IMAGE_SIZE ) {
            return [
                "success" => false,
                "code" => IMAGE_TOO_BIG,
                "message" => $CI->lang->line("image_too_big")
            ];
            
        } 
        
        $extension = pathinfo($image['name']);
        $extension = $extension["extension"];
        
        if ( ! in_array($extension, $validExtensions) ) {
            return [
                "success" => false,
                "code" => NOT_AN_IMAGE,
                "message" => $CI->lang->line("not_an_image")
            ];
        }

        return [
            "success" => true,
            "code" => SUCCESS,
            "mime_type" => $fileMIME
        ];
    }   
}

if ( ! function_exists("s3_delete_image") ) {
    function s3_delete_image( $imageKey ) {
        $s3 = s3_get_client();
        $imageKeyArray = [];
        if ( is_string($imageKey) ) {
            $imageKeyArray = [
                ['Key'  => preg_replace("/^.*s3.amazonaws.com\//", "", $imageKey)],
            ];
        } else if ( is_array($imageKey) ) {
            $imageKeyArray = array_map(function($image){
                return ['Key'  => preg_replace("/^.*s3.amazonaws.com\//", "", $image)];
            }, $imageKey);
        } else {
            return false;
        }
        
        // print_r($imageKeyArray);die;
        try{
            $deleteObjArray = [
                'Bucket' => AMAZONS3_BUCKET,
                'Delete' => [ // REQUIRED
                    'Objects' =>  $imageKeyArray,
                    'Quiet' => true || false,
                ],
              
            ];

            $result = $s3->deleteObjects($deleteObjArray);
        } catch (\Exception $error) {
            // file_put_contents("output2.txt", $error);
        }

        return $result;
        
    }
}
