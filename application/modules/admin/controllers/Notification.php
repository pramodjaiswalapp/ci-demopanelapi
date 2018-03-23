<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Notification extends MY_Controller {

     /**
      * private variable to hold logged in admin Information
      * @var type
      */
     private $admininfo = array ();

     function __construct() {
         parent::__construct();
         $this->lang->load( 'common', "english" );
         $this->admininfo = $this->session->userdata( 'admininfo' );
         date_default_timezone_set( "Asia/Kolkata" );

     }



     /**
      * @function index
      * @description To load list of all notification
      */
     public function index() {

         //TRY START
         try {

             $default = array (
                 "searchlike" => "",
                 "limit"      => 10,
                 "page"       => 1,
                 "platform"   => "",
                 "startDate"  => "",
                 "endDate"    => ""
             );

             $defaultValue              = defaultValue( $this->input->get(), $default );
             $defaultValue['admininfo'] = $this->admininfo;
             $this->load->library( 'commonfn' );

             #$getDataArr = $this->input->get();


             $offset = ($defaultValue['page'] - 1) * $defaultValue['limit'];

             $defaultValue['offset'] = $offset;
             $notiDetail             = $this->Common_model->getNotifications( $defaultValue );

             #IF user is on other than First page, having only one element
             #IF last row is deleted by user
             #than page will redirected to previous page
             if ( !$notiDetail['totalRecords'] && $defaultValue['page'] > 1 ) {
                 $defaultValue['page'] = ( string ) ($defaultValue['page'] - 1);
                 redirect( base_url()."admin/notification?data=".queryStringBuilder( $defaultValue ) );
             }

             $pageurl                   = 'admin/notification';
             $defaultValue['notiList']  = $defaultValue['totalrows'] = $notiDetail['totalRows'];
             $defaultValue['links']     = $this->commonfn->pagination( $pageurl, $defaultValue['totalrows'], $defaultValue['limit'] );
             $defaultValue['notiList']  = $notiDetail['totalRecords'];
             if ( !$GLOBALS['permission'] ) {
                 setDefaultPermission();
             }
             $defaultValue['permission'] = $GLOBALS['permission'];

             load_views( "notification/index", $defaultValue );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function add
      * @description To add new notification in DB
      */
     public function add() {

         try {//TRY END
             // If logged user is sub admin check for his permission
             $data['admininfo'] = $this->admininfo;
             $postDataArr       = $this->input->post();


             if ( !empty( $_FILES ) && '' != $_FILES['notificationImage']['name'] ) {
                 $path = $this->uploadImage( $_FILES );
                 if ( !$path ) {
                     load_views( "notification/add", $data );
                     return;
                 }
                 $postDataArr['image'] = $path;
             }



             if ( !empty( $postDataArr ) ) {//IF 2 START
                 $this->sendNotification( $postDataArr );
             }//IF 2 END
             load_views( "notification/add", $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function edit
      * @description Function to edit/update Notification to send
      */
     public function edit() {
         try {//TRY START
             $data              = array ();
             $data['admininfo'] = $this->admininfo;


             $getDataArr = $this->input->get();

             $postDataArr = $this->input->post();

             $notiId = (isset( $getDataArr['id'] ) && !empty( $getDataArr['id'] )) ? $getDataArr['id'] : '';

             if ( empty( $notiId ) ) {//IF START
                 show404();
             }//IF END

             if ( isset( $postDataArr['old_img'] ) && '' != $postDataArr['old_img'] ) {
                 $postDataArr['image'] = $postDataArr['old_img'];
             }
             if ( !empty( $_FILES ) && '' != $_FILES['notificationImage']['name'] ) {
                 $path = $this->uploadImage( $_FILES );
                 if ( !$path ) {
                     load_views( "notification/add", $data );
                     return;
                 }
                 $postDataArr['image'] = $path;
             }


             if ( !empty( $postDataArr ) ) {//IF START
                 $this->sendNotification( $postDataArr );
             }//IF END
             else if ( !empty( $notiId ) ) { //ELSE IF START
                 $whereArr          = [];
                 $whereArr['where'] = array ('id' => $notiId);
                 $notiDetail        = $this->Common_model->fetch_data( 'admin_notification', array (), $whereArr, true );

                 if ( empty( $notiDetail ) ) {//NESTED IF START
                     show404();
                 }//NESTED IF END

                 $data['detail'] = $notiDetail;
                 load_views( "notification/edit", $data );
             }//ELSE IF END
             else {//ELSE START
                 show404();
             }//ELSE END
         }//TRY END
         catch ( Exception $exception ) {//CATCH START
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function resendNotification
      * @description to Re-send notification
      */
     public function resendNotification() {
         try {
             $notiId               = $this->input->get( 'notiToken' );
             $notiId               = json_decode( encryptDecrypt( $notiId, 'decrypt' ), true );
             $whereArr             = [];
             $whereArr['where']    = array ('id' => $notiId['id']);
             $notiDetail           = $this->Common_model->fetch_data( 'admin_notification', array (), $whereArr, true );
             $notiDetail['notiId'] = $notiId['id'];
             if ( empty( $notiDetail ) ) {
                 show404();
             }
             $this->sendNotification( $notiDetail, true );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * Function used to send notification to device
      *
      * @param array $dataArr all required data to send notification
      * @param bollean $isResend to check is it resend or first time send
      * @return '' NA
      */
     private function sendNotification( $dataArr, $isResend = false ) {
         try {

             $default      = array (
                 "platform" => "",
                 "gender"   => "",
                 "regDate"  => "",
                 "title"    => "",
                 "message"  => "",
                 "link"     => "",
                 "image"    => ""
             );
             $defaultValue = defaultValue( $dataArr, $default );

             $notificationsList = $this->Common_model->sendNotification( $defaultValue );

             $totalCounts = count( $notificationsList );
             $androidArr  = [];
             $iosArr      = [];

             //Make two array of android and iOS
             if ( !empty( $notificationsList ) ) {
                 foreach ( $notificationsList as $list ) {
                     if ( 1 === $list['platform'] ) {
                         $androidArr[] = $list;
                     }
                     else {
                         $iosArr[] = $list;
                     }
                 }
             }

             $payloadData            = [];
             $payloadData['title']   = $defaultValue['title'];
             $payloadData['link']    = $defaultValue['link'];
             $payloadData['message'] = $defaultValue['message'];

             if ( !empty( $androidArr ) ) {
                 $newandroidArr = array_chunk( $androidArr, 10 );
                 foreach ( $newandroidArr as $arr ) {
                     $notiInsertArr = [
                         "data"         => json_encode( $arr ),
                         "payload_data" => json_encode( $payloadData ),
                         "chunk_type"   => "android",
                         "created_time" => date( 'Y-m-d H:i:s' )
                     ];
                     $chunkId       = $this->Common_model->insert_single( 'ai_noti_chunk', $notiInsertArr );
                     $this->sendNotiViaCurl( $chunkId );
                 }
             }

             if ( !empty( $iosArr ) ) {
                 $newiosArr = array_chunk( $iosArr, 10 );

                 foreach ( $newiosArr as $arr ) {
                     $notiInsertArr['data']         = json_encode( $arr );
                     $notiInsertArr['payload_data'] = json_encode( $payloadData );
                     $notiInsertArr['chunk_type']   = 'ios';
                     $notiInsertArr['created_time'] = date( 'Y-m-d H:i:s' );
                     $chunkId                       = $this->Common_model->insert_single( 'ai_noti_chunk', $notiInsertArr );
                     $this->sendNotiViaCurl( $chunkId );
                 }
             }

             $pushInfoArr = [
                 "platform"    => $defaultValue['platform'],
                 "gender"      => $defaultValue['gender'],
                 "date_range"  => $defaultValue['regDate'],
                 "title"       => $defaultValue['title'],
                 "message"     => $defaultValue['message'],
                 "link"        => $defaultValue['link'],
                 "image"       => $defaultValue['image'],
                 "total_sents" => $totalCounts,
                 "created_at"  => date( 'Y-m-d H:i:s' )
             ];


             if ( isset( $dataArr['notiId'] ) && !empty( $dataArr['notiId'] ) ) {
                 $whereArr          = [];
                 $whereArr['where'] = array ('id' => $dataArr['notiId']);
                 $isSuccess         = $this->saveNotificationData( $pushInfoArr, $dataArr['notiId'] );
             }
             else {
                 $isSuccess = $this->saveNotificationData( $pushInfoArr, FALSE );
             }
             $alertMsg = [];
             if ( $isSuccess ) {
                 $alertMsg['text'] = $this->lang->line( 'notification_added' );
                 $alertMsg['type'] = $this->lang->line( 'success' );
                 $this->session->set_flashdata( 'alertMsg', $alertMsg );
             }
             else {
                 $alertMsg['text'] = $this->lang->line( 'try_again' );
                 $alertMsg['type'] = $this->lang->line( 'error' );
                 $this->session->set_flashdata( 'alertMsg', $alertMsg );
             }

             if ( $isResend ) {
                 echo json_encode( array ('code' => 200, 'msg' => 'Success') );
                 die;
             }
             else {
                 redirect( '/admin/notification' );
             }
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function sendNotiViaCurl
      * @description used to call CURL, to send/resend notification using CURL service
      *
      * @param type $chunkId
      * @return type
      */
     private function sendNotiViaCurl( $chunkId ) {

         $url     = base_url().'admin/notify?chunkId='.$chunkId;
         $ch      = curl_init();
         $timeout = 1;

         curl_setopt( $ch, CURLOPT_URL, $url );
         curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
         curl_setopt( $ch, CURLOPT_HEADER, false );
         curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
         curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );

         $data = curl_exec( $ch );
         curl_close( $ch );
         return $data;

     }



     /**
      * @function saveNotificationData
      * @description to save/update version information in DB
      *
      * @param type $data
      * @param type $updateId
      * @return boolean
      */
     private function saveNotificationData( $data, $updateId = false ) {

         try {
             $this->db->trans_start();
             if ( $updateId ) {
                 $this->Common_model->update_single( 'admin_notification', $data, ['where' => ['id' => $updateId]] );
             }
             else {
                 $updateId = $this->Common_model->insert_single( 'admin_notification', $data );
             }

             if ( TRUE === $this->db->trans_status() ) {
                 $this->db->trans_commit();
                 return true;
             }
             else {
                 $this->db->trans_rollback();
                 return false;
             }
         }
         catch ( Exception $exception ) {
             $this->db->trans_rollback();
             showException( $exception->getMessage() );
             exit;
         }

     }



     private function uploadImage() {
         $config = getConfig( UPLOAD_IMAGE_PATH, 'jpeg|jpg|png', 6000, 2048, 2048 );
         $this->load->library( 'upload', $config );

         if ( $this->upload->do_upload( 'notificationImage' ) ) {//IF 4
             $upload_data   = $this->upload->data();
             $imageName     = $upload_data['file_name'];
             $thumbFileName = $upload_data['file_name'];
             $fileSource    = UPLOAD_IMAGE_PATH.$thumbFileName;
             $targetPath    = UPLOAD_THUMB_IMAGE_PATH;
             $isSuccess     = 1; #$this->commonfn->thumb_create( $thumbFileName, $fileSource, $targetPath );
             if ( $isSuccess ) {//IF 5
                 $thumbName = $imageName;
             }//IF 5 END

             return base_url().UPLOAD_PATH.$imageName;
         }
         else {//ELSE 4
             $this->session->set_flashdata( 'message', $this->lang->line( 'error_prefix' ).strip_tags( $this->upload->display_errors() ).$this->lang->line( 'error_suffix' ) );
             return FALSE;
         }

     }



 }
