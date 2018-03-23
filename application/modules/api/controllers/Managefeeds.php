<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );
 require APPPATH.'/libraries/REST_Controller.php';

 class Managefeeds extends REST_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'feeds_model' );

     }



     /**
      * @SWG\Post(path="api/managefeed",
      *   tags={"Feeds"},
      *   summary="Post a feed",
      *   description="Post a feed",
      *   operationId="index_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="media",
      *     in="query",
      *     description="in json format like json format",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="privacy",
      *     in="query",
      *     description="1 public post", 2 private post , 3 custom
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="description",
      *     in="query",
      *     description="feed description",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="tags",
      *     in="query",
      *     description="tags of description in json format",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="location",
      *     in="query",
      *     description="location of check-in",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="latitude",
      *     in="query",
      *     description="latitude of checkin place",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="longitude",
      *     in="query",
      *     description="longitude of checkin place",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="custom_ids",
      *     in="query",
      *     description="custom User Ids in case privacy is Custom in json array",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=510, description="You can't post a empty feed"))
      *
      */
     /*
      * Media Format
      * [{"url":"s3 url of media1","media_type":1,"tags":[{"tag_coordinates":"(100,150)","user_id":230},{"tag_coordinates":"(100,150)","user_id":231}]},{"url":"s3 url of media2","media_type":2,"tags":[{"tag_coordinates":"(100,150)","user_id":10},{"tag_coordinates":"(100,150)","user_id":10}]}]
      */

     /*
      * Description tags Format
      * [232,231]
      */
     public function index_post() {
         try {
             $postDataArr = $this->post();
             $user_id     = $GLOBALS['api_user_id'];

             $default      = array (
                 "description" => "",
                 "location"    => "",
                 "latitude"    => "",
                 "longitude"   => "",
                 "privacy"     => 1,
                 "media"       => [],
                 "limit"       => CHAT_LIMIT
             );
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             #get feed details
             $description     = trim( $defaultValue['description'] );
             $location        = trim( $defaultValue['location'] );
             $checkin_lat     = trim( $defaultValue['latitude'] );
             $checkin_long    = trim( $defaultValue['longitude'] );
             $privacy         = trim( $defaultValue['privacy'] );
             $custom_user_ids = ( CUSTOM == $privacy ) ? $postDataArr['custom_ids'] : '';
             $media           = $defaultValue['media'];

             #If Post is empty send error message
             if ( empty( $description ) && empty( $location ) && empty( $media ) ) {
                 $response_array = ['code' => EMPTY_FEED, 'msg' => $this->lang->line( 'empty_feed' ), 'result' => []];
             }
             else {

                 $this->load->library( 'S3' );
                 $feedInsertArr                     = [];
                 $feedInsertArr['user_id']          = $user_id;
                 $feedInsertArr['post_description'] = $description;
                 $feedInsertArr['location']         = $location;
                 $feedInsertArr['latitude']         = $checkin_lat;
                 $feedInsertArr['longitude']        = $checkin_long;
                 $feedInsertArr['privacy']          = $privacy;
                 $feedInsertArr['update_date']      = datetime();

                 $this->db->trans_begin();
                 #add feed data in DB
                 $feedId = $this->common_model->insert_single( 'ai_post', $feedInsertArr );
                 #if error in Saving
                 if ( !$feedId ) {
                     #rolling back
                     $this->db->trans_rollback();
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }
                 # save share post mapping with
                 $mapping_id = $this->set_shared_post_mapping( $feedId, $user_id, true );

                 #Get hash tags from description
                 if ( !empty( $description ) ) {
                     $hashtags = $this->getHashtags( $description );

                     # Add hashtags in ai_post_hashtags table
                     if ( count( $hashtags ) > 0 ) {
                         $update_hashtags = $this->updateHashTags( $hashtags, $feedId );
                         #if error in Saving
                         if ( !$update_hashtags ) {

                             #rolling back
                             $this->db->trans_rollback();
                             throw new Exception( $this->lang->line( 'try_again' ) );
                         }
                     }
                 }
                 #Add Media and their tags if any
                 $all_tagged_users = [];
                 if ( !empty( $media ) ) {
                     $all_tagged_users = $this->addMedia( $media, $feedId );
                 }

                 # Add custom user IDs if privacy is Custom
                 if ( !empty( $custom_user_ids ) ) {
                     $insert_status = $this->addCustomUserIds( $custom_user_ids, $user_id, $feedId, false, $mapping_id );
                 }

                 # Get all data of posted feed
                 $hashtag = '';
                 $posts   = $this->common_model->getAllUserPosts( $user_id, $hashtag, $feedId );

                 # Group Single feed data
                 $groupFeedData = [];
                 if ( !empty( $posts ) ) {
                     $groupFeedData = $this->groupFeedData( $posts, $user_id );
                 }

                 # Check if all DB queries executed successfully
                 if ( TRUE === $this->db->trans_status() ) {
                     $this->db->trans_commit();

                     if ( count( $all_tagged_users ) > 0 ) {

                         # send push notification to all tagged users
                         $data     = ['name' => $GLOBALS['login_user']['userinfo']['name'], 'type' => TAGGED_POST, 'user_id' => $user_id];
                         $user_ids = $this->format_userids( $all_tagged_users );

                         $this->notifyTaggedUsers( $user_ids, $feedId, $data );
                     }

                     $respArr            = [];
                     $respArr['feed_id'] = $feedId;
                     $respArr['feed']    = (!empty( $groupFeedData[0] )) ? $groupFeedData[0] : [];
                     $response_array     = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'feed_posted' ), 'result' => $respArr];
                 }#if end
                 else {#IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }
             }
             #sending the response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             #sending the response
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/feeds",
      *   tags={"Feeds"},
      *   summary="get feeds list",
      *   description="get all feeds list",
      *   operationId="feeds_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid")
      * )
      */
     /*
      */
     public function feeds_get() {

         try {
             $getDataArr = $this->get();

             $user_id      = $GLOBALS['api_user_id'];
             $default      = ["hashtag" => ""];
             #Setting Default Value
             $defaultValue = defaultValue( $getDataArr, $default );

             $hashtag = $defaultValue['hashtag'];

             #Get all User posts
             $posts = $this->common_model->getAllUserPosts( $user_id, $hashtag );

             $response_array = array ('code' => NO_DATA_FOUND, 'msg' => 'NO_DATA_FOUND', 'result' => []);
             # Group feed data
             if ( !empty( $posts ) ) {
                 $groupFeedData  = $this->groupFeedData( $posts, $user_id );
                 $response_array = array ('code' => SUCCESS_CODE, 'msg' => 'success', 'result' => $groupFeedData);
             }#IF ENDS

             $this->response( $response_array );
         }#TRY ENDS
         catch ( Exception $e ) {
             $error = $e->getMessage();
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#CATCH ENDS

     }



     /**
      * @name groupFeedData
      * @description Group feed data
      *
      * @param array
      * @param string
      *
      * @return array
      */
     private function groupFeedData( $posts, $user_id ) {
         $result      = [];
         $key_counter = 0;

         foreach ( $posts as $key => $post ) {

             #this statement excutes only first time
             if ( !$key ) {
                 $post_id        = $post['post_id'];
                 #                $shared_user_id = $post['user_id'];
                 $shared_user_id = $post['shared_map_id'];
             }

             # This statement excutes when a post has one data chunk in array
             if ( ($post_id != $post['post_id'] || $shared_user_id != $post['shared_map_id'] ) || !$key ) {

                 # Set Feed Data
                 $result[$key_counter] = $post;

                 #        Set Custom User Ids data
                 $result[$key_counter]['custom_ids'] = [];
                 if ( isset( $post['privacy'] ) && $post['privacy'] == CUSTOM && !empty( $post['custom_ids'] ) ) {
                     $idsArr                             = explode( ',', $post['custom_ids'] );
                     $idsArr                             = array_diff( $idsArr, [$user_id] );
                     $result[$key_counter]['custom_ids'] = $this->fetchUserInfoAndId( $idsArr );
                 }

                 # Unset these variables a these are already Set in Media Data */
                 unset( $result[$key_counter]['url'] );
                 unset( $result[$key_counter]['media_type'] );
             }
             else {
                 # This statement excutes when a post has multiple data in array */
                 $key_counter = $key_counter - 1;
             }

             # Set tagged users Info */
             $tagged_users = [];
             if ( !empty( $post['user_ids'] ) ) {
                 $user_id_array = explode( ',', $post['user_ids'] );
                 $tagged_users  = $this->fetchUserInfoAndId( $user_id_array );
             }

             # Add media data and Media Tags Data */
             $media_result = $this->setMediaData( $post, $tagged_users );

             if ( empty( $media_result ) ) {
                 $result[$key_counter]['media'] = [];
             }
             else {
                 $result[$key_counter]['media'][] = $media_result;
             }

             # Add is_commented flag */
             $result[$key_counter]['is_commented'] = $this->setIsCommented( $post, $user_id );

             # Add is_liked flag */
             $result[$key_counter]['is_liked'] = $this->setIsLiked( $post, $user_id );

             # Add is_shared flag */
             $result[$key_counter]['is_shared'] = $this->setIsShared( $post, $user_id );

             # Set Post owner key */
             $result[$key_counter]['owner_id'] = 0;
             if ( $post['user_id'] == $user_id ) {
                 $result[$key_counter]['owner_id'] = 1;
             }

             # Set Description tagged users Info */
             $result[$key_counter]['desc_tagged_users'] = [];
             if ( !empty( $post['description'] ) ) {
                 $userTags = $this->getUserTags( $post['description'] );

                 if ( count( $userTags ) > 0 ) {
                     $result[$key_counter]['desc_tagged_users'] = $this->fetchUserInfoAndId( $userTags );
                 }
             }

             # Unset these variables a these are already Set in Media Tags Data */
             unset( $result[$key_counter]['x_axis'] );
             unset( $result[$key_counter]['y_axis'] );
             unset( $result[$key_counter]['user_ids'] );
             unset( $result[$key_counter]['comment_userids'] );
             unset( $result[$key_counter]['share_userids'] );
             unset( $result[$key_counter]['post_like_userids'] );

             $post_id = $post['post_id'];

             $shared_user_id = $post['shared_map_id'];
             $key_counter    += 1;
         }

         return $result;

     }



     /**
      * @SWG\Post(path="api/deletefeed",
      *   tags={"Feeds"},
      *   summary="Delete a feed",
      *   description="Delete a feed",
      *   operationId="delete_feed_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post/Feed ID to delete",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function delete_feed_post() {
         try {
             $postDataArr  = $this->post();
             $config       = [
                 ['field' => 'post_id', 'label' => 'Post ID', 'rules' => 'required']
             ];
             $default      = ["post_id" => ""];
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );
             $post_id      = $defaultValue['post_id'];
             $set_data     = ['post_id' => $post_id];

             # Setting Data, Rules and Error Messages for rules */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err          = $this->form_validation->error_array();
                 $arr          = array_values( $err );
                 $error        = (isset( $arr[0] ) ) ? $arr[0] : 'parameter missing';
                 $result_array = array ('code' => PARAM_REQ, 'msg' => $error, 'result' => []);
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 $this->db->trans_begin();

                 # check post status */
                 $status = $this->check_feed_status( $postDataArr['post_id'] );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => INACTIVE_POST, 'msg' => $this->lang->line( 'inactive_post' ), 'result' => []];
                 }#END IF
                 else {
                     # Update post status to 2 - soft delete */
                     $update_status = $this->update_post_status( $postDataArr['post_id'] );
                     if ( !$update_status ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Decrease Share counter of parent post if deleted post was a shared post */
                     $counter_update = $this->check_post( $post_id, BLOCKED );

                     # Delete mapping data for parent post if child posts exists */
                     if ( $counter_update ) {
                         $delete_mapping = $this->common_model->delete_data( 'ai_share_post_mapping', ["where" => ["post_id" => $post_id]] );
                         if ( !$delete_mapping ) {
                             throw new Exception( $this->lang->line( 'try_again' ) );
                         }
                     }

                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();
                         $respArr            = [];
                         $respArr['feed_id'] = $postDataArr['post_id'];
                         $response_array     = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'feed_deleted' ), 'result' => $postDataArr['post_id']];
                     }#if end
                     else {#IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }#END ELSE
             }
             #sending the response
             $this->response( $response_array );
         }
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/editfeed",
      *   tags={"Feeds"},
      *   summary="Edit a feed",
      *   description="Edit a feed",
      *   operationId="edit_post_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="media",
      *     in="query",
      *     description="in json format like json format",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="privacy",
      *     in="query",
      *     description="1 public post", 2 private post , 3 custom
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="description",
      *     in="query",
      *     description="feed description",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="tags",
      *     in="query",
      *     description="tags of description in json format",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="location",
      *     in="query",
      *     description="location of check-in",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="latitude",
      *     in="query",
      *     description="latitude of checkin place",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="longitude",
      *     in="query",
      *     description="longitude of checkin place",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="custom_ids",
      *     in="query",
      *     description="custom User Ids in case privacy is Custom in json array",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Inactive post"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=510, description="You can't post a empty feed"))
      *
      */
     /*
      * Media Format
      * [{"url":"s3 url of media1","media_type":1,"tags":[{"tag_coordinates":"(100,150)","user_id":230},{"tag_coordinates":"(100,150)","user_id":231}]},{"url":"s3 url of media2","media_type":2,"tags":[{"tag_coordinates":"(100,150)","user_id":10},{"tag_coordinates":"(100,150)","user_id":10}]}]
      */

     /*
      * Description tags Format
      * [232,231]
      */
     public function edit_feed_post() {
         try {
             $postDataArr = $this->post();

             $default      = array (
                 "post_id"     => "",
                 "share_id"    => "",
                 "description" => "",
                 "location"    => "",
                 "latitude"    => "",
                 "longitude"   => "",
                 "privacy"     => 1,
                 "media"       => [],
                 "limit"       => CHAT_LIMIT
             );
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             $postid   = $defaultValue['post_id'];
             $share_id = $defaultValue['share_id'];

             $set_data = array (
                 'post_id'  => $postid,
                 'share_id' => $share_id
             );
             $config   = [
                 ['field' => 'post_id', 'label' => 'Post ID', 'rules' => 'required'],
                 ['field' => 'share_id', 'label' => 'Share ID', 'rules' => 'required']
             ];

             #Setting Rules, Data and error Messages for rules
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = (isset( $arr[0] ) ) ? $arr[0] : $this->load->lang( 'parameter_missing' );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $error, 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {


                 # check post status
                 $status = $this->check_feed_status( $postid );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => '207', 'msg' => 'Inactive post', 'result' => []];
                 }#IF END
                 else {
                     #get feed details
                     $description  = trim( $defaultValue['description'] );
                     $location     = trim( $defaultValue['location'] );
                     $checkin_lat  = trim( $defaultValue['latitude'] );
                     $checkin_long = trim( $defaultValue['longitude'] );
                     $privacy      = trim( $defaultValue['privacy'] );

                     $custom_user_ids = ( $privacy == CUSTOM ) ? $postDataArr['custom_ids'] : '';
                     $media           = $defaultValue['media'];

                     #Post is empty send error message
                     if ( empty( $description ) && empty( $location ) && empty( $media ) ) {
                         $this->response( array (
                             'code'   => EMPTY_FEED, 'msg'    => $this->lang->line( 'empty_feed' ), 'result' => []) );
                     }
                     else {
                         $user_id = $GLOBALS['api_user_id'];

                         $feedUpdateArr                     = [];
                         $feedUpdateArr ['user_id']         = $user_id;
                         $feedUpdateArr['post_description'] = $description;
                         $feedUpdateArr['location']         = $location;
                         $feedUpdateArr['latitude']         = $checkin_lat;
                         $feedUpdateArr['longitude']        = $checkin_long;
                         $feedUpdateArr['privacy']          = $privacy;

                         $this->db->trans_begin();

                         # update feed data
                         $where  = array ('where' => array ('post_id' => $postid));
                         $status = $this->common_model->update_single( 'ai_post', $feedUpdateArr, $where );

                         #Get hash tags from description
                         #If Start
                         if ( !empty( $description ) ) {
                             $hashtags = $this->getHashtags( $description );

                             # Add hashtags in ai_post_hashtags table */
                             #If 1 start
                             if ( count( $hashtags ) > 0 ) {
                                 $update_hashtags = $this->updateHashTags( $hashtags, $postid, true );
                             }#If 1 end
                         }#If end
                         #Add Media and there tags if any
                         if ( !empty( $media ) ) {
                             $this->addMedia( $media, $postid, true );
                         }#If end
                         # Add custom user IDs if privacy is Custom */
                         if ( !empty( $custom_user_ids ) ) {
                             $insert_status = $this->addCustomUserIds( $custom_user_ids, $user_id, $postid, true, $share_id );
                         }#If end
                         else {
                             # Delete all Post related Custom User IDS in Edit Post case  */
                             $where         = array ('where' => array ('post_id' => $postid, 'share_id' => $share_id));
                             $delete_status = $this->common_model->delete_data( 'ai_post_custom', $where );
                             if ( !$delete_status ) {
                                 throw new Exception( $this->lang->line( 'try_again' ) );
                             }
                         }#Else end
                         # Get all data of posted feed */
                         $hashtag = '';
                         $posts   = $this->common_model->getAllUserPosts( $user_id, $hashtag, $postid );

                         # Group Single feed data */
                         $groupFeedData = [];
                         if ( !empty( $posts ) ) {
                             $groupFeedData = $this->groupFeedData( $posts, $user_id );
                         }#if end
                         # Check if all DB queries executed successfully */
                         if ( TRUE === $this->db->trans_status() ) {
                             $this->db->trans_commit();
                             $respArr            = [];
                             $respArr['feed_id'] = $postid;
                             $respArr['feed']    = (!empty( $groupFeedData[0] )) ? $groupFeedData[0] : [];

                             $response_array = array ('code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'feed_updated' ), 'result' => $respArr);
                         }#if end
                         else {#IF transaction failed
                             #rolling back
                             $this->db->trans_rollback();

                             #setting Response Array
                             $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                         }#ELSE END
                     }
                 }#ELSE END
             }
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#CATCH END

     }



     /**
      * @SWG\Post(path="api/reportfeed",
      *   tags={"Feeds"},
      *   summary="Report a feed",
      *   description="Report a feed",
      *   operationId="report_feed_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post/Feed ID to report",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="user_id",
      *     in="query",
      *     description="User ID of reporter",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=202, description="Feed already reported"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function report_feed_post() {
         try {
             $postDataArr = $this->post();

             $config = [
                 ['field' => 'post_id', 'label' => 'Post Id', 'rules' => 'required'],
                 ['field' => 'user_id', 'label' => 'User ID', 'rules' => 'required']
             ];

             #Setting Default Value
             $default      = [
                 "post_id" => "",
                 "user_id" => ""
             ];
             $defaultValue = defaultValue( $postDataArr, $default );

             $post_id = $defaultValue['post_id'];
             $user_id = $defaultValue['user_id'];

             $set_data = [
                 'post_id' => $post_id,
                 'user_id' => $user_id
             ];

             # Set Data , Rules and Error messages for API request parameter validation */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = ( isset( $arr[0] ) ) ? $arr[0] : 'parameter missing';
                 $response_array = array (
                     'code'   => PARAM_REQ, 'msg'    => $error, 'result' => []);
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 # check post status
                 $status = $this->check_feed_status( $post_id );
                 if ( empty( $status ) ) {
                     $response_array = array ('code'   => INACTIVE_POST, 'msg'    => $this->lang->line( 'inactive_post' ), 'result' => [
                     ]);
                 }
                 # check post status is ACTIVE
                 if ( !empty( $status ) ) {
                     # Check if post is already reported
                     $check_entry = $this->check_feed_reported( $post_id, $user_id );

                     $this->db->trans_begin();

                     # Send response when Post/Feed is already reported by the User
                     if ( count( $check_entry ) > 0 ) {
                         $response_array = array ('code' => FEED_ALREADY_REPORTED, 'msg' => $this->lang->line( 'feed_already_reported' ), 'result' => []);
                     }

                     # Add Post/Feed Report entry in DB
                     else if ( count( $check_entry ) == 0 ) {

                         # Report a post - Table - ai_report_post
                         $insert_status = $this->report_feed( $post_id, $user_id );
                         if ( !$insert_status ) {
                             throw new Exception( $this->lang->line( 'try_again' ) );
                         }
                     }

                     # Check if all DB queries executed successfully
                     if ( TRUE === $this->db->trans_status() ) {

                         $this->db->trans_commit();
                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'feed_reported' ), 'result' => $post_id];
                     }#if end
                     else {
                         #IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }
             }
             #sending the response
             $this->response( $response_array );
         }
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/commentfeed",
      *   tags={"Feeds"},
      *   summary="Comment on a feed",
      *   description="Comment on a feed",
      *   operationId="comment_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post/Feed ID to report",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="comment",
      *     in="query",
      *     description="Comment",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="user_id",
      *     in="query",
      *     description="User Id",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function comment_post() {
         try {
             $postDataArr = $this->post();
             $config      = [
                 ['field' => 'post_id', 'label' => 'Post Id', 'rules' => 'required'],
                 ['field' => 'comment', 'label' => 'Comment', 'rules' => 'required'],
                 ['field' => 'user_id', 'label' => 'User Id', 'rules' => 'required']
             ];

             $default      = array (
                 "post_id" => "",
                 "user_id" => "",
                 "comment" => ""
             );
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             # Setting Data ,  Rules and Error Messages */
             $post_id = $defaultValue['post_id'];
             $user_id = $defaultValue['user_id'];
             $comment = $defaultValue['comment'];

             $set_data = array (
                 'post_id' => $post_id, 'user_id' => $user_id, 'comment' => $comment
             );
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {


                 # check post status */
                 $status = $this->check_feed_status( $post_id );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => INACTIVE_POST, 'msg' => $this->lang->line( 'inactive_post' ), 'result' => []];
                 }
                 else {

                     # Get list of all users who commented on this post */
                     $users_list = '';

                     # Set Comment insert array */
                     $commentInsertArr = $this->setCommentInsertData( $user_id, $postDataArr );

                     $this->db->trans_begin();

                     $isRequestSuccess = $this->common_model->insert_single( 'ai_post_comment', $commentInsertArr );
                     if ( !$isRequestSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Set comment data for API Response */
                     $commentData               = [];
                     $commentData['comment']    = $commentInsertArr['comment'];
                     $commentData['comment_id'] = $isRequestSuccess;

                     # Check if all DB queries executed successfully */
                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();

                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_posted' ), 'result' => $commentData];
                     }#if end
                     else {#IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }
             }
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/editcomment",
      *   tags={"Feeds"},
      *   summary="Edit Comment",
      *   description="Edit Comment",
      *   operationId="edit_comment_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="comment_id",
      *     in="query",
      *     description="Comment Id",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="comment",
      *     in="query",
      *     description="Comment",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function edit_comment_post() {
         try {
             $postDataArr = $this->post();

             $config       = [
                 ['field' => 'comment_id', 'label' => 'Comment Id', 'rules' => 'required'],
                 ['field' => 'comment', 'label' => 'Comment', 'rules' => 'required']
             ];
             #Setting Default Value
             $default      = [
                 "comment_id" => "",
                 "comment"    => ""
             ];
             $defaultValue = defaultValue( $postDataArr, $default );

             # Setting Data ,  Rules and Error Messages
             $comment_id = $defaultValue['comment_id'];
             $comment    = $defaultValue['comment'];

             $set_data = array (
                 'comment_id' => $comment_id, 'comment'    => $comment
             );
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []);
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {


                 $this->db->trans_begin();

                 # Update comment */
                 $data             = array ('comment' => $comment, 'update_date' => datetime());
                 $where            = array ('where' => array ('id' => $comment_id));
                 $isRequestSuccess = $this->common_model->update_single( 'ai_post_comment', $data, $where );

                 if ( !$isRequestSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }

                 # Set comment data for API Response */
                 $commentData               = [];
                 $commentData['comment']    = $comment;
                 $commentData['comment_id'] = $isRequestSuccess;

                 # Check if all DB queries executed successfully */
                 if ( TRUE === $this->db->trans_status() ) {
                     $this->db->trans_commit();

                     $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_update_success' ), 'result' => $commentData];
                 }#if end
                 else {
                     #IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }
             }
             #sending Response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/deletecomment",
      *   tags={"Feeds"},
      *   summary="Delete a comment",
      *   description="Delete a comment",
      *   operationId="delete_comment_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="comment_id",
      *     in="query",
      *     description="Comment ID to delete",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=202, description="Comment already deleted"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function delete_comment_post() {
         try {
             $postDataArr = $this->post();

             $config       = [
                 ['field' => 'comment_id', 'label' => 'Comment ID', 'rules' => 'required']
             ];
             $default      = array (
                 "comment_id" => ""
             );
             #Setting Default Value
             $defaultValue = defaultValue( $getDataArr, $default );

             $comment_id = $defaultValue['comment_id'];
             $set_data   = ['comment_id' => $comment_id];

             # Set Data , Rules and Error messagefor API request parameter validation */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = ( isset( $arr[0] ) ) ? $arr[0] : $this->lang->line( 'parameter missing' );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $error, 'result' => []);
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 $this->db->trans_begin();
                 # Delete Comment */
                 $set           = ['status' => BLOCKED, 'update_date' => datetime()];
                 $where         = ['where' => ['id' => $comment_id]];
                 $delete_status = $this->common_model->update_single( 'ai_post_comment', $set, $where );

                 # If update returns false, throw exception */
                 if ( !$delete_status ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }
                 # Check if all DB queries executed successfully */
                 if ( TRUE === $this->db->trans_status() ) {
                     $this->db->trans_commit();

                     $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_delete_success' ), 'result' => $comment_id];
                 }#if end
                 else {
                     #IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }
             }
             #sending Response
             $this->response( $response_array );
         } #TRY END
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             #sending Response
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#CATCH END

     }



     /**
      * @SWG\Post(path="api/likefeed",
      *   tags={"Feeds"},
      *   summary="Like a feed",
      *   description="Like a feed",
      *   operationId="like_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post/Feed ID to report",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="user_id",
      *     in="query",
      *     description="user ID",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="status",
      *     in="query",
      *     description="Status -- 1=> LIKE , 2=> UNLIKE",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function like_post() {
         try {
             $postDataArr = $this->post();

             $config = [
                 ['field' => 'share_id', 'label' => 'Share ID', 'rules' => 'required'],
                 ['field' => 'post_id', 'label' => 'Post ID', 'rules' => 'required'],
                 ['field' => 'user_id', 'label' => 'User ID', 'rules' => 'required'],
                 ['field' => 'status', 'label' => 'Status', 'rules' => 'required']
             ];

             $default      = [
                 "share_id" => "",
                 "post_id"  => "",
                 "user_id"  => "",
                 "status"   => "",
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             $share_id = $defaultValue['share_id'];
             $post_id  = $defaultValue['post_id'];
             $user_id  = $defaultValue['user_id'];
             $status   = $defaultValue['status'];

             $set_data = [
                 'share_id' => $share_id, 'post_id'  => $post_id,
                 'user_id'  => $user_id, 'status'   => $status
             ];

             # Set Data , Rules and message for API request parameter validation */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = (isset( $arr[0] )) ? $arr[0] : $this->lang->line( 'parameter missing' );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $error, 'result' => []);
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 $this->db->trans_begin();

                 # check post status */
                 $status_check = $this->check_feed_status( $post_id );
                 if ( empty( $status_check ) ) {
                     $response_array = array
                         (
                         'code'   => '207', 'msg'    => 'Inactive post', 'result' => []);
                 }
                 else {
                     #get users who were tagged in post image
                     $get_image_tagged_users = $this->getPostImageTaggedUsers( $post_id );


                     # Add Post/Feed Like entry in DB
                     $insert_status = $this->like_feed( $share_id, $user_id, $status );

                     # If insert method returns false, throw exception */
                     if ( !$insert_status ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Update Like Counter in post table
                     $update = $this->update_counter( $share_id, 'like', $status );

                     # If update counter method returns false, throw exception */
                     if ( !$update ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Check if all DB queries executed successfully
                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();

                         #Send push only when post is liked */
                         if ( LIKE_POST == $status ) {
                             $data = ['name' => $GLOBALS['login_user']['userinfo']['name'], 'type' => LIKE_POST, 'user_id' => $user_id];

                             $post_owner_info = $this->getSharedPostOwnerDetails( $post_id );
                             if ( empty( $post_owner_info ) ) {
                                 $post_owner_info = $this->getPostOwnerDetails( $post_id );
                             }
                             #$post_owner_info = $this->getPostOwnerDetails($post_id);
                             $this->send_push( $post_id, $data, $post_owner_info );

                             # Send push to all tagged users */
                             $data['type'] = LIKE_TAGGED_POST;
                             if ( count( $get_image_tagged_users ) > 0 ) {
                                 $this->notifyTaggedUsers( $get_image_tagged_users, $post_id, $data );
                             }
                         }
                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'req_accepted' ), 'result' => $status];
                     }#if end
                     else {
                         #IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }#Else End
                 }
             }

             #sending Response
             $this->response( $response_array );
         }#try END
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             #sending Response
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#Catch End

     }



     /**
      * @SWG\Post(path="api/sharefeed",
      *   tags={"Feeds"},
      *   summary="Share a feed",
      *   description="Share a feed",
      *   operationId="share_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post/Feed ID to report",
      *     required=true,
      *     type="string"
      *   )
      *   ),
      *  @SWG\Parameter(
      *     name="user_id",
      *     in="query",
      *     description="User ID",
      *     required=true,
      *     type="string"
      *   )
      *   ),
      *  @SWG\Parameter(
      *     name="privacy",
      *     in="query",
      *     description="Privacy key , 1=> Public , 3=> custom",
      *     required=true,
      *     type="string"
      *   )
      *   ),
      *  @SWG\Parameter(
      *     name="custom_ids",
      *     in="query",
      *     description="User IDS -- CSV values",
      *     required=true,
      *     type="string"
      *   )
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function share_post() {
         try {
             $postDataArr = $this->post();

             $config       = [
                 ['field' => 'post_id', 'label' => 'Post ID', 'rules' => 'required'],
                 ['field' => 'share_id', 'label' => 'Share ID', 'rules' => 'required'],
                 ['field' => 'user_id', 'label' => 'User ID', 'rules' => 'required'],
                 ['field' => 'privacy', 'label' => 'Privacy', 'rules' => 'required']
             ];
             $default      = array (
                 "post_id"    => "",
                 "share_id"   => "",
                 "user_id"    => "",
                 "privacy"    => "",
                 "custom_ids" => ""
             );
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             $post_id    = $defaultValue['post_id'];
             $share_id   = $defaultValue['share_id'];
             $user_id    = $defaultValue['user_id'];
             $privacy    = $defaultValue['privacy'];
             $custom_ids = $defaultValue['custom_ids'];

             $set_data = [
                 'post_id'  => $post_id, 'share_id' => $share_id,
                 'user_id'  => $user_id, 'privacy'  => $privacy
             ];

             # Set Data , Rules and message for API request parameter validation
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             #if the validation fails

             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = (isset( $arr[0] )) ? $arr[0] : $this->lang->line( 'parameter_missing' );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $error, 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 # check post status
                 $status = $this->check_feed_status( $post_id );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => '207', 'msg' => 'Inactive post', 'result' => []];
                 }
                 else {
                     # Add Post/Feed Like entry in DB
                     $insert_status = $this->share_feed( $post_id, $share_id, $user_id, $privacy, $custom_ids );

                     # If insert method returns false, throw exception
                     if ( !$insert_status ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Update Share Counter in post table */
                     //$update = $this->update_counter($post_id, 'share', false);
                     # Check if all DB queries executed successfully */
                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();

                         # Send push notification to post owner */
                         $data            = ['name' => $GLOBALS['login_user']['userinfo']['name'], 'type' => SHARE_POST, 'user_id' => $user_id];
                         $post_owner_info = $this->getPostOwnerDetails( $post_id );

                         $this->send_push( $post_id, $data, $post_owner_info );

                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'req_accepted' ), 'result' => $post_id];
                     }#if end
                     else {#IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }
             }
             #sending response
             $this->response( $response_array );
         }#try eds
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             #sending Response
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }#catch ends

     }



     /**
      * @SWG\Post(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Give comment for a post",
      *   description="Give comment for a post",
      *   operationId="manage_comment_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="post_id",
      *     in="formData",
      *     description="Post Id",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment",
      *     in="formData",
      *     description="Comment",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *   @SWG\Response(response=505, description="Review already exists")
      * )
      */
     public function manage_comment_post() {
         try {
             $postDataArr  = $this->post();
             $config       = [
                 ['field' => 'post_id', 'label' => 'Post ID', 'rules' => 'required'],
                 ['field' => 'share_id', 'label' => 'Share ID', 'rules' => 'required'],
                 ['field' => 'comment', 'label' => 'Comment', 'rules' => 'required']
             ];
             $default      = array (
                 "comment"  => "",
                 "share_id" => "",
                 "post_id"  => ""
             );
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             $post_id  = $defaultValue['post_id'];
             $share_id = $defaultValue['share_id'];
             $comment  = $defaultValue['comment'];

             $set_data = [
                 'post_id'  => $post_id, 'share_id' => $share_id, 'comment'  => $comment
             ];

             # Setting Data , Rules and Error Messages for rules */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 # check post status */
                 $status = $this->check_feed_status( $post_id );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => '207', 'msg' => 'Inactive post', 'result' => []];
                 }
                 else {
                     $user_id                = $GLOBALS['api_user_id'];
                     # Get all media tagged users of this post */
                     $get_image_tagged_users = $this->getPostImageTaggedUsers( $post_id );

                     # Request Array
                     $commentInsertArr                = [];
                     $commentInsertArr['user_id']     = $user_id;
                     $commentInsertArr['share_id']    = $share_id;
                     $commentInsertArr['post_id']     = $post_id;
                     $commentInsertArr['comment']     = trim( $comment );
                     $commentInsertArr['create_date'] = datetime();
                     $commentInsertArr['update_date'] = datetime();

                     $this->db->trans_begin();
                     #add post comment in ai_post_comment table
                     $isRequestSuccess = $this->common_model->insert_single( 'ai_post_comment', $commentInsertArr );

                     if ( !$isRequestSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }
                     $commentData               = [];
                     $commentData['comment']    = $comment;
                     $commentData['comment_id'] = $isRequestSuccess;

                     # update comment count */
                     $update = $this->update_counter( $share_id, 'comment', false );
                     if ( !$update ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     # Check if all DB queries executed successfully
                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();

                         $data = ['name' => $GLOBALS['login_user']['userinfo']['name'], 'type' => COMMENT_POST, 'user_id' => $user_id];

                         # Get post owner's details */
                         //                    $user_details = $this->getPostOwnerDetails($post_id);

                         $user_details = $this->getSharedPostOwnerDetails( $post_id );
                         if ( empty( $user_details ) ) {
                             $user_details = $this->getPostOwnerDetails( $post_id );
                         }

                         $data['post_owner_userid'] = $user_details['user_id'];

                         # Get all users who commented on this post including post owner and Send push to all of them */
                         $user_ids  = $this->getUsersCommented( $post_id );
                         $send_push = $this->getUsersInCommentList( $user_ids, $post_id, $data );

                         # Send push to all tagged users */
                         if ( count( $get_image_tagged_users ) > 0 ) {
                             $data = ['name' => $GLOBALS['login_user']['userinfo']['name'], 'type' => COMMENT_TAGGED_POST, 'user_id' => $user_info['user_id']];
                             $this->notifyTaggedUsers( $get_image_tagged_users, $post_id, $data );
                         }
                         # Send push only when post is liked */
                         //$this->send_push($post_id,$data,$user_details);

                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_posted' ), 'result' => $commentData];
                     }#if end
                     else {
                         #IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }
             }
             #   send response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             #send response
             $this->response( array ('code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []) );
         }

     }



     /**
      * @SWG\Get(path="/viewcomments",
      *   tags={"Comments"},
      *   summary="View the comments of a post",
      *   description="View the comments of a post",
      *   operationId="view_comments_get",
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="post_id",
      *     in="query",
      *     description="Post Id",
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="page",
      *     in="query",
      *     description="page no.",
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Please try again"),
      *   @SWG\Response(response=202, description="No data found"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function manage_comment_get() {
         try {
             $getDataArr     = $this->input->get();
             $user_id        = $GLOBALS['api_user_id'];
             $response_array = ['code' => NO_DATA_FOUND, 'msg' => $this->lang->line( 'no_comments_found' ), 'result' => []];
             $config         = [
                 ['field' => 'share_id', 'label' => 'Share Id', 'rules' => 'required']
             ];

             $default      = [
                 "share_id" => ""
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $getDataArr, $default );

             $set_data = ['share_id' => $defaultValue['share_id']];
             #Setting Error Messages for rules
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             #if the validation fails
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {
                 # check post status */
                 $status = $this->check_feed_status( $getDataArr['post_id'] );
                 if ( empty( $status ) ) {
                     $response_array = ['code' => '207', 'msg' => $this->lang->line( 'inactive_post' ), 'result' => []];
                 }
                 #if feed status is ACTIVE
                 else if ( !empty( $status ) ) {
                     $page               = isset( $getDataArr['page'] ) ? $getDataArr['page'] : 1;
                     $params             = [];
                     $params['share_id'] = $getDataArr['share_id'];
                     $params['user_id']  = $user_id;
                     $limit              = 20;
                     $offset             = ($page - 1) * $limit;
                     $params['limit']    = $limit;
                     $params['offset']   = $offset;
                     $commentsList       = $this->common_model->getComments( $params );

                     #setting page number

                     if ( ($commentsList[
                         'count'] > ($page * $limit) ) ) {
                         $page++;
                     }
                     else {
                         $page = 0;
                     }
                     if ( !empty( $commentsList['result'] ) ) {
                         $response_array = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comments_list_fetched' ), 'next_page' => $page, 'total_rows' => $commentsList['count'], 'result' => $commentsList['result']];
                     }
                 }
             }
             #sending response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $error = $e->getMessage();
             list($msg, $code) = explode( " || ", $error );
             $this->response( array ('code' => $code, 'msg' => $msg, 'result' => []) );
         }

     }



     /**
      * @SWG\Put(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Update existing comment",
      *   description="Update existing comment",
      *   operationId="index_put",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment_id",
      *     in="formData",
      *     description="Review Id of review which we want to edit",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment",
      *     in="formData",
      *     description="New Comment",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function manage_comment_put() {
         try {
             $putDataArr = $this->put();

             $config = array (
                 array (
                     'field' => 'comment_id', 'label' => 'Comment Id', 'rules' => 'required'
                 ),
                 array (
                     'field' => 'comment', 'label' => 'Comment', 'rules' => 'required'
                 ),
             );

             $default      = array (
                 "comment_id" => "",
                 "comment"    => ""
             );
             #Setting Default Value
             $defaultValue = defaultValue( $putDataArr, $default );

             $comment_id = $defaultValue['comment_id'];
             $comment    = $defaultValue['comment'];
             $set_data   = array (
                 'comment_id' => $comment_id,
                 'comment'    => $comment
             );
             #Setting Date, Rules and Error Messages for rules
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             # Set Data , Rules and Error messages for API request parameter validation */
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = ['code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []];
             }
             #if the form data validation runs successfully returning no errors
             else if ( $this->form_validation->run() ) {

                 $commentUpdateArr                = [];
                 $commentUpdateArr['user_id']     = $GLOBALS['api_user_id'];
                 $commentUpdateArr['comment']     = $comment;
                 $commentUpdateArr['update_date'] = datetime();
                 $whereArr                        = [];
                 $whereArr['where']               = ['id' => $comment_id];

                 $this->db->trans_begin();
                 #update comment in ai_post_comment table
                 $isRequestSuccess = $this->common_model->update_single( 'ai_post_comment', $commentUpdateArr, $whereArr );
                 if ( !$isRequestSuccess ) {
                     throw new Exception( $this->lang->line( 'try_again' ) );
                 }
                 # Check if all DB queries executed successfully
                 if ( $this->db->trans_status() === TRUE ) {
                     $this->db->trans_commit();
                     $commentData            = [];
                     $commentData['comment'] = $commentUpdateArr['comment'];
                     $response_array         = ['code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_update_success' ), 'result' => $commentData];
                 }#if end
                 else {
                     #IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                 }
             }
             #sending the response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }#CATCH END

     }



     /**
      * @SWG\Delete(path="/managecomments",
      *   tags={"Comments"},
      *   summary="Delete comment",
      *   description="Delete comment",
      *   operationId="index_delete",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *     @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Parameter(
      *     name="comment_id",
      *     in="formData",
      *     description="Comment Id of Comment which we want to delete",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      * )
      */
     public function manage_comment_delete() {
         try {

             $deleteDataArr = $this->delete();

             $config       = [
                 ['field' => 'comment_id', 'label' => 'Comment Id', 'rules' => 'required'],
                 ['field' => 'post_id', 'label' => 'Post Id', 'rules' => 'required'],
                 ['field' => 'share_id', 'label' => 'Share Id', 'rules' => 'required']
             ];
             $default      = [
                 "comment_id" => "", "post_id"    => "", "share_id"   => ""
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $deleteDataArr, $default );

             $comment_id = $defaultValue['comment_id'];
             $post_id    = $defaultValue['post_id'];
             $share_id   = $defaultValue['share_id'];

             $set_data = array (
                 'comment_id' => $comment_id,
                 'post_id'    => $post_id,
                 'share_id'   => $share_id
             );


             #Setting Data ,Rules and Error Messages for rules
             $this->form_validation->set_message( 'required', 'Please enter the %s' );
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );

             #if validation runs and returns error(s)
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []);
             }
             #if validation runs successfully without returning any errors
             else if ( $this->form_validation->run() ) {

                 /* check post status */
                 $status = $this->check_feed_status( $deleteDataArr['post_id'] );
                 if ( empty( $status ) ) {
                     $response_array = array ('code' => '207', 'msg' => 'Inactive post', 'result' => []);
                 }
                 else {
                     /* Delete comment */
                     $whereArr          = [];
                     $whereArr['where'] = ['id' => $comment_id];
                     $isDeleteSuccess   = $this->common_model->delete_data( 'ai_post_comment', $whereArr );
                     if ( !$isDeleteSuccess ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }

                     /* update comment count */
                     $update = $this->update_counter( $share_id, 'comment', UNLIKE );
                     if ( !$update ) {
                         throw new Exception( $this->lang->line( 'try_again' ) );
                     }
                     /* Check if all DB queries executed successfully */
                     if ( TRUE === $this->db->trans_status() ) {
                         $this->db->trans_commit();
                         $response_array = array ('code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'comment_delete_success' ), 'result' => []);
                     }#if end
                     else {#IF transaction failed
                         #rolling back
                         $this->db->trans_rollback();

                         #setting Response Array
                         $response_array = ['code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line( 'try_again' ), 'result' => []];
                     }
                 }
             }
             #sending the response
             $this->response( $response_array );
         }
         catch ( Exception $e ) {
             $this->db->trans_rollback();
             $error = $e->getMessage();
             #sending the response
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }

     }



     /**
      * @SWG\Post(path="api/hashtags",
      *   tags={"Feeds"},
      *   summary="get hashtags list",
      *   description="get all hashtags list",
      *   operationId="hashtags_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="accesstoken",
      *     in="header",
      *     description="Access token received during signup or login",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid")
      * )
      */
     /*
      */
     public function hashtags_get() {
         try {
             $getDataArr   = $this->get();
             $default      = [
                 "page"   =>
                 1,
                 "search" => ""
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $getDataArr, $default );

             $config   = [
                 ['field' => 'page', 'label' => 'page', 'rules' => 'required']
             ];
             $page     = $defaultValue['page'];
             $search   = $defaultValue['search'];
             $set_data = ['page' => $page];


             #Set Data , Rules and Error messages for API request parameter validation
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if validation runs and returns error(s)
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $err_msg        = (isset( $arr[0] ) ) ? $arr[0] : $this->lang->line( 'missing_parameter' );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $err_msg, 'result' => []);
             }
             #if validation runs successfully without returning any errors
             else if ( $this->form_validation->run() ) {

                 $limit                                    = 5;
                 $offset                                   = ($page - 1) * $limit;
                 $params[
                     'limit'] = $limit;
                 $params['offset']                         = $offset;

                 # Get all Hashtags
                 $hashtags = $this->fetchHashtags( $params, $search );


                 if ( ($hashtags['count'] > ($page * $limit) ) ) {
                     $page = 1;
                 }
                 else {
                     $page = 0;
                 }

                 /* Group feed data */
                 $response_array = array ('code' => NO_DATA_FOUND, 'msg' => $this->lang->line( 'no_data_found' ), 'result' => [], 'page' => '');
                 if ( !empty( $hashtags['data'] ) ) {

                     $response_array = array ('code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'success' ), 'result' => $hashtags['data'], 'page' => $page);
                 }
             }
             #sending Response
             $this->response( $response_array );
         }
         catch ( Exception $exc ) {
             $this->db->trans_rollback();
             $error = $exc->getMessage();
             #send response
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }#CATCH END

     }



     /**
      * @SWG\Post(path="api/savelocation",
      *   tags={"Feeds"},
      *   summary="Save user location",
      *   description="Save user location",
      *   operationId="save_location_post",
      *   consumes ={"multipart/form-data"},
      *   produces={"application/json"},
      *   @SWG\Parameter(
      *     name="Authorization",
      *     in="header",
      *     description="",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="device_id",
      *     in="query",
      *     description="Device Id of user",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="platform",
      *     in="query",
      *     description="User device platform -- Android or iOS",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="latitude",
      *     in="query",
      *     description="Latitude",
      *     required=true,
      *     type="string"
      *   ),
      *  @SWG\Parameter(
      *     name="longitude",
      *     in="query",
      *     description="Longitude",
      *     required=true,
      *     type="string"
      *   ),
      *   @SWG\Response(response=200, description="Success"),
      *   @SWG\Response(response=201, description="Try Again"),
      *   @SWG\Response(response=206, description="Unauthorized request"),
      *   @SWG\Response(response=207, description="Header is missing"),
      *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
      *
      */
     public function save_location_post() {
         try {
             $postDataArr = $this->post();


             $config       = [
                 ['field' => 'device_id', 'label' => 'Device Id', 'rules' => 'required'],
                 ['field' => 'platform', 'label' => 'Platform', 'rules' => 'required'],
                 ['field' => 'latitude', 'label' => 'Latitude', 'rules' => 'required'],
                 ['field' => 'longitude',
                     'label' => 'Longitude', 'rules' => 'required']
             ];
             $default      = [
                 "device_id" => "", "platform"  => "",
                 "latitude"  => "", "longitude" => ""
             ];
             #Setting Default Value
             $defaultValue = defaultValue( $postDataArr, $default );

             $device_id = $defaultValue['device_id'];
             $platform  = $defaultValue['platform'];
             $latitude  = $defaultValue['latitude'];
             $longitude = $defaultValue['longitude'];

             $set_data = [
                 'device_id' => $device_id,
                 'platform'  => $platform,
                 'latitude'  => $latitude,
                 'longitude' => $longitude
             ];

             /* Set Data , Rules and Error messages for API request parameter validation */
             $this->form_validation->set_data( $set_data );
             $this->form_validation->set_rules( $config );
             $this->form_validation->set_message( 'required', 'Please enter the %s' );

             #if validation runs and returns error(s)
             if ( !$this->form_validation->run() ) {
                 $err            = $this->form_validation->error_array();
                 $arr            = array_values( $err );
                 $error          = (isset( $arr[0] )) ? $arr[0] : $this->lang->line( 'missing_parameter' );
                 $response_array = array ('code' => PARAM_REQ, 'msg' => $error);
             }
             #if validation runs successfully without returning any errors
             else if ( $this->form_validation->run() ) {

                 $this->db->trans_begin();

                 /* Save user location - Table - ai_user_location */
                 $insert_status = $this->save_user_location( $set_data );


                 /* Check if all DB queries executed successfully */
                 if ( TRUE === $this->db->trans_status() ) {
                     $this->db->trans_commit();

                     $response_array = array ('code' => SUCCESS_CODE, 'msg' => $this->lang->line( 'location_saved' ));
                 }#if end
                 else {#IF transaction failed
                     #rolling back
                     $this->db->trans_rollback();

                     #setting Response Array
                     $response_array = ['code'   => TRY_AGAIN_CODE,
                         'msg'    => $this->lang->line( 'try_again' ),
                         'result' => []
                     ];
                 }
             }#form validation if end
             #sending Response
             $this->response( $response_array );
         }#TRY END
         catch ( Exception $exc ) {

             $this->db->trans_rollback();
             $error = $exc->getMessage();
             #send response
             $this->response( ['code' => TRY_AGAIN_CODE, 'msg' => $error, 'result' => []] );
         }#CATCH END

     }



     /**
      * @name setCommentInsertData
      * @description Set Comment insert array
      *
      * @param string
      * @param array
      * @return array
      */
     function setCommentInsertData( $user_id, $postDataArr ) {

         $commentInsertArr = [];

         $commentInsertArr['user_id']     = $user_id;
         $commentInsertArr['post_id']     = $postDataArr['post_id'];
         $commentInsertArr['comment']     = $postDataArr['comment'];
         $commentInsertArr['create_date'] = datetime();
         $commentInsertArr['update_date'] = datetime();
         return $commentInsertArr;

     }



     /**
      * @name setMediaData
      * @description Return Media Data
      *
      * @param string
      * @param array
      * @return array
      */
     function setMediaData( $post, $tagged_users ) {

         $media_arr = [];
         /* Check if media data exists */
         if ( empty( $post['url'] ) && empty( $post['url'] ) && empty( $post['x_axis'] ) && empty( $post['y_axis'] ) ) {
             return $null = "";
         }
         $media_arr['url']             = isset( $post['url'] ) ? $post['url'] : '';
         $media_arr['video_thumbnail'] = isset( $post['video_thumbnail'] ) ? $post['video_thumbnail'] :
             '';
         $media_arr['media_type']      = isset( $post['media_type'] ) ? $post['media_type'] : '';
         $media_arr['tags']            = (!empty( $post['x_axis'] ) && !empty( $post['y_axis'] )) ? $this->setTags( $post, $tagged_users ) : [];
         return $media_arr;

     }



     /**
      * @name setUserName
      * @description Return User name
      *
      * @param array
      * @param string
      * @return string
      */
     function setUserName( $tagged_users, $user_id ) {
         $name = '';
         if ( count( $tagged_users ) > 0 ) {
             foreach ( $tagged_users as $key => $userInfo ) {
                 if ( $userInfo[
                     'user_id'] == $user_id ) {
                     $name = $userInfo['name'];
                     break;
                 }
             }
         }
         return $name;

     }



     /**
      * @name setTags
      * @description Return media Tags Data
      *
      * @param string
      * @param array
      * @return array
      */
     function setTags( $post, $tagged_users ) {

         $setTags = [];

         $xAxisArr   = explode( ',', $post['x_axis'] );
         $yAxisArr   = explode( ',', $post[
             'y_axis'] );
         $userIdsArr = explode( ',', $post['user_ids'] );

         foreach ( $xAxisArr as $key => $axis ) {
             $tags            = [];
             $tags['x_axis']  = ( string ) trim( $axis );
             $tags['y_axis']  = isset( $yAxisArr[$key] ) ? ( string ) trim( $yAxisArr[$key] ) : "";
             $tags['user_id'] = isset( $userIdsArr[$key] ) ? ( string ) trim( $userIdsArr[$key] ) : "";

             /* Check and Set User's Name */
             $tags['name'] = $this->setUserName( $tagged_users, $tags['user_id'] );

             $setTags[] = $tags;
         }
         return $setTags;

     }



     /**
      * @name update_post_status
      * @description Delete Post/Feed -- update Status = 2 in table ai_post
      *
      * @param string
      * @return array
      */
     function update_post_status( $post_id ) {

         $table  = 'ai_post';
         $where  = array ('where' => array
                 ('post_id' => $post_id));
         $set    = array ('status' => BLOCKED, 'update_date' => datetime());
         return $status = $this->common_model->update_single( $table, $set, $where );

     }



     /**
      * @name update_counter
      * @description Update Post/Feed Like, comment and Share count -  table ai_post
      *
      * @param string
      * @param string
      * @param string
      * @return array
      */
     function update_counter( $share_id, $column, $status ) {

         return $status = $this->common_model->update_post_counter( $share_id, $column, $status );

     }



     /**
      * @name report_feed
      * @description Report Post/Feed -- update Status = 2 in table ai_report_post
      *
      * @param string
      * @param string
      * @return array
      */
     function report_feed( $post_id, $user_id ) {

         $table  = 'ai_report_post';
         $data   = array ('user_id' => $user_id, 'post_id' => $post_id);
         return $status = $this->common_model->insert_single( $table, $data );

     }



     /**
      * @name like_feed
      * @description  Like Post/Feed -- Add entry in table ai_like_post
      *
      * @param string
      * @param string
      * @param string
      * @return string
      */
     function like_feed( $share_id, $user_id, $status ) {

         $table = 'ai_like_post';

         /* in case of UNLIKE */
         if ( $status == UNLIKE ) {
             $where  = ['where' => ['user_id' => $user_id, 'share_id' => $share_id]];
             return $status = $this->common_model->delete_data( $table, $where );
         }
         /* In case of Like */
         $table  = 'ai_like_post';
         $data   = array ('user_id' => $user_id, 'share_id' => $share_id);
         return $status = $this->common_model->insert_single( $table, $data );

     }



     /**
      * @name check_feed_reported
      * @description Check if a Post/Feed is already reported by the user in table ai_report_post
      *
      * @ param st ring
      * @param string
      * @return array
      */
     function check_feed_reported( $post_id, $user_id ) {

         $table  = 'ai_report_post';
         $where  = array ('where' => array ('post_id' => $post_id, 'user_id' => $user_id));
         return $result = $this->common_model->fetch_data( $table, 'id', $where );

     }



     /**
      * @name getFeedMedia
      * @description Get Feed Media Files while get feeds api
      *
      * @param array
      * @return array
      */
     function getFeedMedia( $feed ) {
         $mediaUrls  = explode( ',', $feed['url'] );
         $mediaTypes = explode( ',', $feed['media_type'] );
         $media      = [];
         foreach ( $mediaUrls as $mediaIndex => $mediaUrl ) {
             $media[$mediaIndex]['url']        = $mediaUrl;
             $media[$mediaIndex]['media_type'] = $mediaTypes[$mediaIndex];
         }
         return $media;

     }



     /**
      * @name getHashtags
      * @description get hash tags from a string
      *
      * @param string
      * @return array
      */
     function getHashtags( $enc_string ) {
         $string   = base64_decode( $enc_string );
         $hashtags = FALSE;
         preg_match_all( "/(#\w+)/u", $string, $matches );
         if ( $matches ) {
             $hashtagsArray = array_count_values( $matches[0] );
             $hashtags      = array_keys( $hashtagsArray );
         }
         return $hashtags;

     }



     /**
      * @name getUserTags
      * @description get all User Ids from Description
      *
      * @param string
      * @return array
      */
     function getUserTags( $enc_string ) {
         $string   = base64_decode( $enc_string );
         $userTags = FALSE;
         preg_match_all( "/(%%%\d+%%%)/u", $string, $matches );
         if ( $matches ) {
             $userTagsArray = array_count_values( str_replace( '%%%', '', $matches[0] ) );
             $userTags      = array_keys( $userTagsArray );
         }
         return $userTags;

     }



     /**
      * @name addMedia
      * @description Add Media in ai_media Table
      *
      * @param array
      * @param string
      * @param bool
      * @return array
      */
     private function addMedia( $mediaArr, $postId, $update_feed_case = false ) {

         $all_tagged_users = [];
         $all_tagged       = [];
         /* Delete all Post related hash tags in Edit Post case  */
         if ( $update_feed_case ) {
             $where = array ('where' => array ('post_id' => $postId));
             $this->common_model->delete_data( 'ai_media', $where );
         }
         foreach ( $mediaArr as $media ) {

             /* Get image width and height */
             $url_append = $ext        = '';

             $mediaInsertArr               = [];
             $mediaInsertArr['post_id']    = $postId;
             $mediaInsertArr['url']        = $media['url'];
             $mediaInsertArr['media_type'] = $media['media_type'];
#Add Media in ai_media Table
             $mediaId                      = $this->common_model->insert_single( 'ai_media', $mediaInsertArr );

             if ( !$mediaId ) {
                 throw new Exception( $this->lang->line( 'try_again' ) );
             }
             if ( !empty( $media['tags'] ) ) {

                 /* Set Tags on media in ai_image_usertags table  */
                 $isSuccess = $this->setMediaDetails( $media['tags'], $mediaId, $postId, $all_tagged_users );
             }
             /*  Create and save Video thumbnail if Media is Video */
             if ( $media['media_type'] == 2 ) {
                 $create_thumb = $this->createThumbFromVideo( $media['url'] );

                 /* Update thumbnail url in ai_post Table */
                 $this->common_model->update_single( 'ai_media', ['video_thumb' => $create_thumb], ['where' => ['id' => $mediaId]] );
             }

             $all_tagged[] = $all_tagged_users;
         }
         return $all_tagged;

     }



     /**
      * @name createThumbFromVideo
      * @description Create and save Video thumbnail if Media is Video
      *
      * @param string
      * @return string
      */
     function createThumbFromVideo( $url ) {

         $thumbnail_url        = 'https://appinventiv-development.s3.amazonaws.com/android/1516861760349/443&271.jpg';
         $unique_id            = uniqid();
         $thumbnail_image_name = $unique_id.".jpeg";
         $thumbnail            = getcwd()."/public/images/";
         $thumb_path           = $thumbnail.$thumbnail_image_name;
         $cmd                  = "ffmpeg -i ".$url." -ss 00:00:00.1 35 -vfra mes 1 ".$thumb_path."";
         exec( $cmd );

         if ( file_exists( $thumb_path ) ) {
             list($width, $height, $type, $attr) = getimagesize( $thumb_path );
             $width_height = '/'.$width.'&'.$height;

             $thumbnail_image_name = uniqid().".jpeg";

             /* Upload video thumbnail on S3 server */
             $s3 = new S3( AWS_ACCESSKEY, AWS_SECRET_KEY );

             $uri           = 'android/'.$unique_id.$width_height.".jpeg";
             $bucket        = BUCKET;
             $result        = $s3->putObjectFile( $thumb_path, $bucket, $uri, S3::ACL_PUBLIC_READ );
             $thumbnail_url = PATH_S3.$uri;
             unlink( $thumb_path );
         }

         return $thumbnail_url;

     }



     /**
      * @name setMediaDetails
      * @description Set Tags on media in ai_image_usertags table
      *
      * @param array
      * @param string
      * @param string
      * @param string
      * @return bool
      */
     private function setMediaDetails( $tagsArr, $mediaId, $postId, &$all_tagged_users ) {
         $tagArrBatch = [];

         $all_tagged_users = [];

         foreach ( $tagsArr as $key => $tag ) {
             $tagArrBatch[$key]['media_id']                                          = $mediaId;
             $tagArrBatch[$key]['x_axis']                                            = isset( $tag['x_axis'] ) ? trim( $tag['x_axis'] ) : "";
             $tagArrBatch[$key]['y_axis']                                            = isset( $tag['y_axis'] ) ? trim( $tag['y_axis'] ) : "";
             $tagArrBatch[
                 $key
                 ]['user_id'] = isset( $tag['user_id'] ) ? trim( $tag[
                     'user_id'] ) : "";

             /*  save tagged users' IDS for sending notification */
             $all_tagged_users[] = isset( $tag['user_id'] ) ? trim( $tag['user_id'] ) : "";
         }
#Set Tags on media in ai_image_usertags table
         $lastIds = $this->common_model->insert_multiple( 'ai_image_usertags', $tagArrBatch );
         if ( !$lastIds ) {
             throw new Exception( $this->lang->line( 'try_again' ) );
         }
         else {
             return true;
         }

     }



     /**
      * @name updateHashTags
      * @description Save Hash Tags in ai_post_hashtags table
      *
      * @param string
      * @param string
      * @param boolean
      * @param string
      * @return string
      */
     private function updateHashTags( $hashtags, $post_id, $update_feed_case = false ) {
         $tagsBatch = []

         ;
         foreach ( $hashtags as $key => $tag ) {
             $tagsBatch[$key]['hash_tag'] = $tag;
             $tagsBatch[$key]['post_id']  = $post_id;
         }
         /* Delete all Post related hash tags in Edit Post case  */
         if ( $update_feed_case ) {
             $where = array ('where' => array
                     ('post_id' => $post_id));
             $this->common_model->
                 delete_data( 'ai_post_hashtags', $where );
         }
#Save Hash Tags in ai_post_hashtags table
         $lastIds = $this->common_model->insert_multiple( 'ai_post_hashtags', $tagsBatch );
         if ( !$lastIds ) {
             throw new Exception( $this->lang->line( 'try_again' ) );
         }
         else {
             return true;
         }

     }



     /**
      * @name addCustomUserIds
      * @description Add custom USer IDs in ai_post_custom
      *
      * @param string
      * @param string
      * @param string
      *  @param boolean
      * @param string
      * @return string
      */
     private function addCustomUserIds( $user_ids, $user_ID, $post_id, $update_feed_case = false, $share_id = 0 ) {

         $add_custom_ids = [];
         $user_ids_arr   = [];

         /* Also adding current user in custom user list */
         $user_ids_arr[] = $user_ID;

         /* Fetch user Ids from Request */ foreach ( $user_ids as $key => $user_id ) {
             $user_ids_arr[] = $user_id['user_id'];
         }

         foreach ( $user_ids_arr as $key => $user_id ) {
             $add_custom_ids[$key]['post_id']   = $post_id;
             $add_custom_ids[$key]['share_id '] = $share_id;
             $add_custom_ids[$key]['user_id']   = $user_id;
         }

# Delete all Post related User IDS in Edit Post case
         if ( $update_feed_case ) {
             $where = array ('where' => array ('post_id' => $post_id, 'share_id' => $share_id));
             $this->common_model->delete_data( 'ai_post_custom', $where );
         }
#Add custom USer IDs in ai_post_custom table
         $insert_id = $this->common_model->insert_multiple( 'ai_post_custom', $add_custom_ids );
         if ( !$insert_id ) {
             throw new Exception( $this->lang->line( 'try_again' ) );
         }
         else {
             return true;
         }

     }



     /**
      * @name setIsShared
      * @description check if user Shared a post or not
      *
      * @param string
      * @param st ring
      * @return string
      */
     function setIsShared( $post, $user_id ) {
         $status   = 0;
         $user_ids = explode( ',', $post['share_userids'] );

         if ( !empty( $user_ids ) && in_array( $user_id, $user_ids ) && $post['shared'] ) {
             $status = 1;
         }
         return $status;

     }



     /**
      * @name setIsCommented
      * @description check if user commented on a post or not
      *
      * @param string
      * @param string
      * @return string
      */
     function setIsCommented( $post, $user_id ) {
         $status   = 0;
         $user_ids = explode( ',', $post['comment_userids'] );
         if ( !empty( $user_ids ) && in_array( $user_id, $user_ids ) ) {
             $status = 1;
         }
         return $status;

     }



     /**
      * @name setIsLiked
      * @description check if user Liked on a post or not
      *
      * @param string
      * @param string
      * @return string
      */
     function setIsLiked( $post, $user_id ) {
         $status   = 0;
         $user_ids = explode( ',', $post['post_like_userids'] );

         if ( !empty( $user_ids ) && in_array( $user_id, $user_ids ) ) {
             $status = 1;
         }
         return $status;

     }



     /**
      * @name fetchUserInfoAndId
      * @description Fetch user Info of Tagged users
      *
      * @param array
      * @return array
      */
     private function fetchUserInfoAndId( $userTags ) {

//where condition
         $where  = array ('where_in' => array ('user_id' => $userTags));
         $result = $this->common_model->fetch_data( 'ai_user', 'user_id,CONCAT(first_name," ",last_name) as name', $where );
         return $result;

     }



     /**
      * @name fetchUserInfo
      * @description Fetch user Info of Tagged users
      *
      * @param array
      * @return array
      */
     private function fetchUserInfo( $userTags ) {

#where condition
         $where  = array ('where_in' => array ('user_id' => $userTags));
         $result = $this->common_model->fetch_data( 'ai_user', 'CONCAT(first_name," ",last_name) as name', $where );
         return $result;

     }



     /**
      * @name getPostImageTaggedUsers
      * @descr iption Fetch Tagged users In post media
      *
      * @param string
      * @return array
      */
     private function getPostImageTaggedUsers( $post_id ) {

         $result = $this->common_model->getMediaTaggedUsers( $post_id );

#put all user_ids in an array
         $userIdsArr = [];
         if ( count( $result ) > 0 ) {

             foreach ( $result as $key => $value ) {
                 $userIdsArr[] = $value['user_id'];
             }
         }

         return $userIdsArr;

     }



     /**
      * @name share_feed
      * @description Share feed method -- it contains all metho ds to co py the post related data
      *
      * @param string
      * @param string
      * @param string
      * @param array
      * @return string
      */
     function share_feed( $post_id, $share_id, $user_id, $privacy, $custom_ids ) {

         /*  save share post mapping data for all posts */
         $mapping_id = $this->set_shared_post_mapping( $post_id, $user_id, false, $share_id );

         $parent_share_id = $this->get_parent_share_id( $post_id );

         /* Check if given shared id is of parent post, if not , over write it wth Parent share id */
         if ( $parent_share_id != $share_id ) {
             $share_id = $parent_share_id;
         }

         /* Set Custom User IDS if Privacy is 3 */
         if ( $privacy == CUSTOM && !empty( $custom_ids ) ) {
             $custom_ids_arr = explode( ' ', $custom_ids );
             $setCustomIds   = $this->add_custom_ids( $custom_ids_arr, $user_id, $mapping_id );
         }

         return $mapping_id;

     }



     /**
      * @name set_shared_post_mapping
      * @description save share post mapping for all posts
      *
      * @param string
      * @param string
      * @param bool
      * @param string
      * @return string
      */
     function set_shared_post_mapping( $post_id, $user_id, $add_post = false, $share_id = 0 ) {

         $status = ACTIVE;
         /*   Add shared post mapping data in Table -- ai_share_post_mapp ing */
         $data   = ['post_id'       => $post_id,
             'user_id'       => $user_id, 'like_count'    => 0, 'comment_count' => 0, 'share_count'   => 0, 'update_date'   => datetime()];

         /* Set shared flag to 1 in case of shared post */
         if ( !$add_post ) {
             $data['shared'] = ACTIVE;
         }
         $insert_id = $this->common_model->insert_single( 'ai_share_post_mapping', $data );
         if ( !$insert_id ) {
             throw new Exception( $this->lang->line( 'try_again' ) );
         }
         else if ( !$add_post ) { // Update counter only if it is a shared feed case

             /* Update share_count of parent post */
             $update = $this->update_counter( $share_id, 'share', $status );
         }
         return $insert_id;

     }



     /**
      * @name add_custom_ids
      * @description Add Custom Ids
      *
      * @param array
      * @param string
      * @param string
      * @return bool
      */
     function add_custom_ids( $user_ids_arr, $user_ID, $post_id ) {
         $add_custom_ids = [];

         /* Also adding current user in custom user list */
         $add_custom_ids[] = $user_ID;

         foreach ( $user_ids_arr as $key => $user_id ) {
             $add_custom_ids[$key]['share_id'] = $post_id;
             $add_custom_ids[$key]['user_id']  = $user_id;
         }

         /*  Add custom IdS in Table -- ai_post_custom */
         $insert_id = $this->common_model->insert_multiple( 'ai_post_custom', $add_custom_ids );

         if ( !$insert_id ) {
             throw new Exception( $this->lang->line( 'try_again' ) );
         }
         else {
             return true;
         }

     }



     /**
      * @name check_post
      * @description Decrease Share count er of pa rent post if deleted post was a shared post
      *
      * @p aram str ing
      * @param string
      * @return string
      */
     private function check_post( $post_id, $status ) {
         $id     = 0;
#where condition
         $where  = array ('where' => array ('post_id' => $post_id));
         $result = $this->common_model->fetch_data( 'ai_share_post_mapping', 'id,post_id', $where );
         if ( !empty( $result ) && count( $result ) > 0 ) {

             /* Update share_count of parent post */
             $update = $this->update_counter( $post_id, 'share', $status );
         }
         return $id;

     }



     /**
      * @name get_parent_share_id
      * @description Get parent share id of the Shared post to check if it is child post or parent post
      *
      * @param string
      * @return string
      */
     private function get_parent_share_id( $post_id ) {
         $id         = 0;
         $return_row = true;
#where condition
         $where      = array ('where' => array ('post_id' => $post_id, 'shared' => 0));
         $result     = $this->common_model->fetch_data( 'ai_share_post_mapping', 'id', $where, $return_row );
         if ( !empty( $result ) && count( $result ) > 0 ) {
             $id = (!empty( $result['id'] ) ) ? $result['id'] : 0;
         }
         return $id;

     }



     /**
      * @name check_feed_status
      * @description Check feed status
      *
      * @param string
      * @return array
      */
     private function check_feed_status( $post_id ) {

         $table  = 'ai_post';
         $where  = array ('where' => array ('post_id' => $post_id, 'status' => ACTIVE));
         return $result = $this->common_model->fetch_data( $table, 'post_description,post_id', $where, true );

     }



     /**
      * @name get_notify_types
      * @description get notification types from table -- ai_notification_type
      *
      * @param string
      * @return array
      */
     private function get_notify_types( $type ) {

         $table          = 'ai_notification_type';
         $where['where'] = ["type_id" => $type
         ];
         return $result         = $this->common_model->fetch_data( $table, 'type_id,message', $where );

     }



     /**
      * @name getUsersCommented
      * @description get all users' ids who commented on a post
      *
      * @param string
      * @return array
      */
     private function getUsersCommented( $post_id ) {

         $table          = 'ai_post_comment';
         $where['where'] = [
             "post_id" => $post_id];
         return $result         = $this->common_model->fetch_data( $table, 'DISTINCT(user_id)', $where );

     }



     /**
      * @name getPostOwnerDetails
      * @description Get post owner's details
      *
      * @param string
      * @return array
      */
     private function getPostOwnerDetails( $post_id ) {
         $where           = ['p.post_id' => $post_id, 'p.status' => ACTIVE];
         return $post_owner_info = $this->common_model->getPostownerInfo( 'device_token,p.user_id,a.platform', $where );

     }



     /**
      * @name getSharedPostOwnerDetails
      * @description Get post owner's details
      *
      * @param string
      * @return array
      */
     private function getSharedPostOwnerDetails( $post_id ) {
         $where           = ['p.post_id' => $post_id, 'p.shared' => ACTIVE];
         return $post_owner_info = $this->common_model->getSharedPostownerInfo( 'device_token,p.user_id,a.platform', $where );

     }



     /**
      * @name fetchHashtags
      * @description Fetch list of all hashtags
      *
      * @param array
      * @param string
      * @return array
      */
     private function fetchHashtags( $params, $search ) {

         $result = $this->common_model->getHashtags( $params, $search );
         return $result;

     }



     /**
      * @name getUsersInCommentList
      * @description Send push to all users who earlier commented on a post
      *
      * @param string
      * @param string
      * @param array
      */
     private function getUsersInCommentList( $user_ids, $post_id, $data ) {

         /* put all user_ids in an array */
         $userIdsArr = [];

         if ( count( $user_ids ) == 0 ) {
             return false;
         }

         foreach ( $user_ids as $key => $value ) {
             $userIdsArr[] = $value['user_id'];
         }
         $userIdsArr[] = $data['post_owner_userid'];

         /* Set device token for all users who commented on the post */
         $field          = 'device_token,u.user_id,a.platform';
         $whereArr       = [];
         $get_users_info = $this->common_model->getCurrentUserInfo( $field, $whereArr, $userIdsArr );
         if ( count( $get_users_info ) == 0 ) {
             return false;
         }
         /* Send notification to each user who commented on the post */
         $user_ids_notified = [];
         $data['type']      = IN_COMMENT_POST;
         foreach ( $get_users_info as $key => $user_Data ) {

             if ( in_array( $user_Data['user_id'], $user_ids_notified ) || empty( $user_Data['device_token'] ) ) {
                 continue;
             }
             if ( $data['post_owner_userid'] == $user_Data['user_id'] ) {
                 $data['type'] = COMMENT_POST;
             }
             $user_ids_notified[] = $user_Data['user_id'];
             $this->send_push( $post_id, $data, $user_Data );
         }

     }



     /**
      * @name notifyLikeTaggedUsers
      * @description send push to tagged users
      *
      * @param string
      * @param string
      * @param array
      */
     private function notifyLikeTaggedUsers( $user_ids, $post_id, $data ) {

         /* Set device token for all users who commented on the post */
         $field          = 'device_token,u.user_id,a.platform';
         $whereArr       = [];
         $get_users_info = $this->common_model->getCurrentUserInfo( $field, $whereArr, $user_ids );
         if ( count( $get_users_info ) == 0 ) {
             return false;
         }
         /* Send notification to each user who is tagged in the post description */
         $user_ids_notified = [];
         foreach ( $get_users_info as $key => $user_Data ) {

             if ( in_array( $user_Data['user_id'], $user_ids_notified ) || empty( $user_Data['device_token'] ) ) {
                 continue;
             }
             $user_ids_notified[] = $user_Data['user_id'];
             $this->send_push( $post_id, $data, $user_Data );
         }

     }



     /**
      * @name notifyTaggedUsers
      * @description send push to tagged users
      *
      * @param string
      * @param array
      * @param array
      */
     private function notifyTaggedUsers( $user_ids, $post_id, $data ) {

         if ( count( $user_ids ) == 0 ) {
             return false;
         }

#get tagged us ers' dev ice details from DB
         $field          = 'device_token,u.user_id,a.platform';
         $whereArr       = [];
         $get_users_info = $this->common_model->getCurrentUserInfo( $field, $whereArr, $user_ids );
         if ( count( $get_users_info ) == 0 ) {
             return false;
         }

         /* Send notification to each user who are tagged in media in the post */
         $user_ids_notified = [];
         foreach ( $get_users_info as $key => $user_Data ) {

             if ( in_array( $user_Data['user_id'], $user_ids_notified ) || empty( $user_Data['dev ice_toke n'] ) ) {
                 continue;
             }
             $user_idsnotified[] = $user_Data['user_id'];
             $this->send_push( $post_id, $data, $user_Data );
         }

     }



     /**
      * @name send_push
      * @description Send push notification
      *
      * @param string
      * @param array
      * @param array
      *
      * @return string
      */
     private function send_push( $post_id, $data, $user_info ) {
         $status = false;

         /*  if device token is empty */
         if ( empty( $user_info['device_token'] ) ) {
             return $status;
         }
         /* If owner himself performed action on  his pos t */
         if ( $user_info['user_id'] == $data['user_id'] ) {
             return $status;
         }


         /* Get notification types from ai_notification_type Table */
         $notify_type   = $this->get_notify_types( $data['type'] );
         $notify_result = $notify_type[0];

         $msg                         = str_replace( '$$username$$', $data['name'], $notify_result['message'] );
#send notification to android/iOS
         $pushDataArr['deviceTokens'] = $user_info['device_token'];
         if ( $user_info['platform'] == ANDROID ) {

             $status = $this->set_android_push( $msg, $user_info, $data, $pushDataArr );
         }
         else
         if ( $user_info['platform'] == IPHONE ) {

             $status = $this->set_ios_push( $msg, $user_info, $data, $pushDataArr );
         }

         /*  Add notification data to notifications table */
         $data_notify['receiver_id']       = $user_info['user_id'];
         $data_notify['object_id']         = $post_id;
         $data_notify['notification_type'] = $data['type'];

         $this->common_model->insert_single( 'ai_notifications', $data_notify );
         return $status;

     }



     /**
      * @name set_android_push
      * @description Set Android push payload data and Send Push
      *
      * @param string
      * @param array
      * @param array
      * @param array
      *
      * @return string
      */
     private function set_android_push( $msg, $post_owner_info, $data, $pushDataArr ) {

         $status = false;

         $pay_load['message'] = $msg;
         $pay_load['user_id'] = $post_owner_info['user_id'];
         $pay_load['type']    = $data['type'];
         $pay_load['time']    = time();

         $pushDataArr['payload'] = $pay_load;

         $status = sendAndroidPush( $pushDataArr );
         return $status;

     }



     /**
      * @name set_ios_push
      * @description Set iOS push payload data and Send Push
      *
      * @param string
      * @param array
      * @param array
      * @param array
      *
      * @return string
      */
     private function set_ios_push( $msg, $post_owner_info, $data, $pushDataArr ) {

         $status = false;

         $iosPayload['alert']    = $msg;
         $iosPayload['badge']    = 0;
         $iosPayload['type']     = $data['type'];
         $iosPayload['sound']    = 'beep.mp3';
         $pushDataArr['payload'] = $iosPayload;
         $status                 = sendIosPush( $pushDataArr );

         return $status;

     }



     /**
      * @name format_userids
      * @description format user ids array -- parse through each key to get all values under a array key
      *
      * @param array
      * @return array
      */
     private function format_userids( $user_ids ) {

         $user_ids_arr = [];

         if ( count( $user_ids ) ) {
             foreach ( $user_ids as $key => $val ) {
                 foreach ( $val as $k => $value ) {
                     $user_ids_arr[] = $value;
                 }
             }
         }
         return $user_ids_arr;

     }



     /**
      * @name save_user_location
      * @description Save user location in table ai_user_location
      *
      * @param array
      * @return string
      */
     function save_user_location( $data ) {

         $table  = 'ai_user_location';
         $data   = [
             'device_id' => $data['device_id'],
             'platform'  => ( $data['platform'] == 'android' ) ? ANDROID : IPHONE,
             'latitude'  => $data['latitude'],
             'longitude' => $data['longitude']
         ];
#Save user location in  table  ai_user_location
         return $status = $this->common_model->insert_single( $table, $data );

     }



 }
