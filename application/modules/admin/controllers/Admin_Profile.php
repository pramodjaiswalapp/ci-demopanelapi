<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Admin_Profile extends MY_Controller {

     function __construct() {
         parent::__construct();
         $this->load->model( 'Common_model' );
         $this->lang->load( 'common', "english" );
         $this->data      = [];
         $this->admininfo = [];
         $this->admininfo = $this->session->userdata( 'admininfo' );

         $this->data['admininfo'] = $this->admininfo;

     }



     /**
      * @name admin_profile
      * @description Admin pofile view
      *
      * @access public.
      */
     public function admin_profile() {
         $data['admininfo'] = $this->admininfo;
         $data["csrfName"]  = $this->security->get_csrf_token_name();
         $data["csrfToken"] = $this->security->get_csrf_hash();
         $data['editdata']  = $this->admininfo;
         load_views( "profile/my-profile", $data );

     }



     /**
      * @function admin_change_password
      * @description This method is used to change admin password.
      */
     public function admin_change_password() {
         try {
             $postdata          = $this->input->post();
             $data['admininfo'] = $this->admininfo;

             //IF post array is set and not null
             if ( isset( $postdata ) && !empty( $postdata ) ) {

                 //setting form rules
                 $this->form_validation->set_rules( 'oldpassword', $this->lang->line( 'old_pass' ), 'trim|required' );
                 $this->form_validation->set_rules( 'password', $this->lang->line( 'password' ), 'trim|required' );
                 $this->form_validation->set_rules( 'confirm_password', $this->lang->line( 'con_pass' ), 'trim|required' );

                 //Checking Form validation
                 if ( $this->form_validation->run() ) {
                     if ( $postdata["password"] !== $postdata["confirm_password"] ) {
                         //Checking old password is correct or not
                         $isExists = $this->Common_model->fetch_data( 'admin', 'admin_password',
                                                                      ['where' => ['admin_password' => hash( "sha256", $postdata["oldpassword"] ), 'admin_id' => $this->admininfo['admin_id']]], true );

                         //if password is correct
                         if ( isset( $isExists ) && !empty( $isExists ) ) {

                             //seting user new password
                             $userdata['admin_password'] = hash( "sha256", $postdata["password"] );
                             $where                      = array ("where" => array ('admin_id' => $this->admininfo['admin_id']));

                             //if updated successfully
                             if ( $this->Common_model->update_single( "admin", $userdata, $where ) ) {

                                 $this->session->set_flashdata( 'password_updated', $this->lang->line( 'success_prefix' ).$this->lang->line( 'password_updated' ).$this->lang->line( 'success_suffix' ) );
                                 redirect( base_url()."admin/profile" );
                             }//if END
                             else {
                                 //If faild to update
                                 $this->session->set_flashdata( 'message', $this->lang->line( 'error_prefix' ).$this->lang->line( 'something_went_wrong' ).$this->lang->line( 'error_suffix' ) );
                             }
                         }
                         else {//ELSE end
                             //if Old password mismatched
                             $data['error_message'] = $this->lang->line( 'old_password_mismatch' );
                         }//ELSE 3 END
                     }
                     else {
                         $data['error_message'] = $this->lang->line( 'no_password_mastch' );
                     }//IF  ENDS
                 }//ELSE 2
             }//IF end
             load_views( "profile/change-password", $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @name Admin Profile
      * @description This method is used to edit admin profile.
      *
      */
     public function edit_profile() {
         try {
             $data['admininfo'] = $this->admininfo;
             $postdata          = $this->input->post();

             //IF 1
             if ( isset( $postdata ) && !empty( $postdata ) ) {

                 $this->form_validation->set_rules( 'Admin_Name', $this->lang->line( 'name_missing' ), 'trim|required' );

                 /* Client side validation false it will redirect to form */
                 if ( $this->form_validation->run() ) {//IF 2
                     if ( isset( $_FILES['admin_image']['name'] ) && !empty( $_FILES['admin_image']['name'] ) ) {//IF 3
                         $this->load->library( 'commonfn' );
                         $config = [];
                         $config = getConfig( UPLOAD_IMAGE_PATH, 'jpeg|jpg|png', 6000, 2048, 2048 );
                         $this->load->library( 'upload', $config );
                         if ( $this->upload->do_upload( 'admin_image' ) ) {//IF 4
                             $upload_data   = $this->upload->data();
                             $imageName     = $upload_data['file_name'];
                             $thumbFileName = $upload_data['file_name'];
                             $fileSource    = UPLOAD_IMAGE_PATH.$thumbFileName;
                             $targetPath    = UPLOAD_THUMB_IMAGE_PATH;
                             $isSuccess     = 1; #$this->commonfn->thumb_create( $thumbFileName, $fileSource, $targetPath );
                             if ( $isSuccess ) {//IF 5
                                 $thumbName = $imageName;
                             }//IF 5 END
                         }//IF 4 END
                         else {//ELSE 4
                             $this->session->set_flashdata( 'message', $this->lang->line( 'error_prefix' ).strip_tags( $this->upload->display_errors() ).$this->lang->line( 'error_suffix' ) );
                             load_views( "profile/admin_profile_edit", $data );
                             return;
                         }//ELSE 4 END
                     }//IF 3 END


                     $adminData               = [];
                     $adminData['admin_name'] = trim( $postdata['Admin_Name'] );
                     if ( isset( $imageName ) && !empty( $imageName ) ) {//IF 6
                         $adminData['admin_profile_pic']   = $imageName;
                         $adminData['admin_profile_thumb'] = $thumbName;
                     }//IF 6 END

                     $where     = array ("where" => array ('admin_id' => $this->admininfo['admin_id']));
                     $isSuccess = $this->Common_model->update_single( "admin", $adminData, $where );

                     if ( $isSuccess ) {//IF 7
                         $newAdminInfo               = $this->admininfo;
                         $newAdminInfo['admin_name'] = $adminData['admin_name'];
                         if ( !empty( $adminData['admin_profile_pic'] ) ) {//IF 8
                             $newAdminInfo['admin_profile_pic']   = $adminData['admin_profile_pic'];
                             $newAdminInfo['admin_profile_thumb'] = $adminData['admin_profile_thumb'];
                         }//IF 8 END

                         $this->session->set_userdata( 'admininfo', $newAdminInfo );
                         $this->session->set_flashdata( 'message', $this->lang->line( 'success_prefix' ).$this->lang->line( 'profile_update' ).$this->lang->line( 'success_suffix' ) );
                         redirect( base_url()."admin/profile" );
                     }//IF 7 END
                     else {//ELSE 7
                         $this->session->set_flashdata( 'message', $this->lang->line( 'error_prefix' ).$this->lang->line( 'error_suffix' ) );
                         load_views( "version/add", $data );
                     }//ELSE 7 END
                 }
             }//IF 1 END

             $data["csrfName"]  = $this->security->get_csrf_token_name();
             $data["csrfToken"] = $this->security->get_csrf_hash();
             $data['editdata']  = $this->admininfo;
             load_views( "profile/admin_profile_edit", $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



 }
