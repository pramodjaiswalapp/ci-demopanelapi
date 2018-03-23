<?php
 if ( !defined( 'BASEPATH' ) ) {
     exit( 'No direct script access allowed' );
 }

 /**
  * @author     Appinventiv
  * @date       19-04-2017
  * @controller Admin
  */
 class Subadmin extends MY_Controller {

     public function __construct() {
         parent::__construct();
         $this->load->model( 'Subadmin_model' );
         $this->lang->load( 'common', 'english' );
         $this->admininfo         = $this->session->userdata( 'admininfo' );
         $this->data              = [];
         $this->data['admininfo'] = $this->admininfo;

     }



     /**
      * @function index
      * @description  List out all Sub-Admin
      */
     public function index() {
         try {
             $default      = array (
                 "searchlike" => "",
                 "limit"      => 10,
                 "page"       => 1,
                 "status"     => "",
                 "field"      => "",
                 "order"      => ""
             );
             $defaultValue = defaultValue( $this->input->get(), $default );

             $offset = ($defaultValue['page'] - 1) * $defaultValue['limit'];

             $pageurl = 'admin/Subadmin';

             $this->load->library( 'commonfn' );
             /* Sorting Query */
             $getQuery                  = http_build_query( array_filter( ["limit" => $defaultValue['limit'], "page" => $defaultValue['page']] ) );
             $defaultValue['get_query'] = "&".$getQuery;

             $defaultValue["order_by_email"] = $defaultValue["order_by_name"]  = $defaultValue["order_by_date"]  = "sorting";

             //Default Order by
             $defaultValue["order_by"] = "asc";

             if ( !empty( $defaultValue['order'] ) ) {//IF 1 START
                 $defaultValue["order_by"] = $defaultValue["order"] == "desc" ? "asc" : "desc";
                 if ( !empty( $defaultValue["field"] ) ) {
                     switch ( trim( $defaultValue["field"] ) ) {
                         case "added":
                             $defaultValue["order_by_date"]  = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                         case "name":
                             $defaultValue["order_by_name"]  = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                         case "email":
                             $defaultValue["order_by_email"] = $defaultValue["order"] == "desc" ? "sort-descending" : "sort-ascending";
                             break;
                     }
                 }
             }//IF 1 END
             $defaultValue['admininfo']     = $this->admininfo;
             $respdata                      = $this->Subadmin_model->getsubadmindata( $defaultValue['limit'], $offset, $defaultValue );
             $defaultValue['link']          = $this->commonfn->pagination( $pageurl, $respdata['totalrows'], $defaultValue['limit'] );
             $defaultValue['data']          = $respdata['records'];
             $defaultValue['allUsersCount'] = $respdata['totalrows'];
             $defaultValue['totalrows']     = $respdata['totalrows'];

             #IF user is on other than First page, having only one element
             #IF last row is deleted by user
             #than page will redirected to previous page
             if ( !$respdata['records'] && $defaultValue['page'] > 1 ) {
                 $defaultValue['page'] = ( string ) ($defaultValue['page'] - 1);
                 redirect( base_url()."admin/subadmin?data=".queryStringBuilder( $defaultValue ) );
             }

             /* Csrf token manage */
             $defaultValue['csrfName']  = $this->security->get_csrf_token_name();
             $defaultValue['csrfToken'] = $this->security->get_csrf_hash();

             $defaultValue['controller'] = $this->router->fetch_class();
             $defaultValue['method']     = $this->router->fetch_method();
             $defaultValue['module']     = $this->router->fetch_module();

             load_views( '/subadmin/index', $defaultValue );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function add
      * @description to add new sub admin in DB
      */
     public function add() {
         try {//TRY Start
             $this->config->load( 'ACL_config', TRUE );
             $data['admininfo']  = $this->admininfo;
             $data['acl_config'] = $this->config->item( 'permission', 'ACL_config' );


             //Server Side validation
             $this->form_validation->set_rules( 'name', 'Admin Name', 'trim|required' );
             $this->form_validation->set_rules( 'email', 'Email', 'trim|required|is_unique[admin.admin_email]', array ('is_unique' => '{field} must be unique') );
             $this->form_validation->set_rules( 'password', 'Password', 'trim|required|min_length[8]|max_length[16]' );
             $this->form_validation->set_rules( 'status', 'Status', 'trim|required' );

             //IF FORM VALIDATION FAILED
             if ( ($this->form_validation->run() ) ) {
                 $post = $this->input->post();

                 //is Post request setted
                 if ( isset( $post ) && !empty( $post ) ) {//IF 1 START
                     $adminInsertArr = array (
                         'admin_name'     => trim( $post['name'] ),
                         'admin_email'    => trim( $post['email'] ),
                         'admin_password' => hash( 'sha256', trim( $post['password'] ) ),
                         'status'         => $post['status'],
                         'role_id'        => 2,
                         'create_date'    => datetime(),
                         'update_date'    => datetime(),
                         "permission"     => json_encode( ( object ) array ("permission" => json_decode( $post['permission'] )) )
                     );


                     $adminid = $this->Common_model->insert_single( 'admin', $adminInsertArr );

                     if ( $adminid ) {//IF 2 START
                         $alertMsg         = [];
                         $alertMsg['text'] = $this->lang->line( 'subadmin_created' );
                         $alertMsg['type'] = 'Success!';
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                         redirect( '/admin/subadmin' );
                     }//IF 2 END
                     else {//ELSE START
                         $data['saveErr'] = $this->lang->line( 'something_went_Worng' );
                     }//ELSE END
                 }//IF 1 END
                 else {//ELSE START
                     //Csrf token manage
                     $data['csrfName']  = $this->security->get_csrf_token_name();
                     $data['csrfToken'] = $this->security->get_csrf_hash();
                 }//ELSE END
             }//ELSE START

             load_views( '/subadmin/add-new', $data );
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function check_email_avalibility
      * @description
      */
     public function check_email_avalibility() {

         try {
             // Checking, is function is called as ajax request
             if ( !$this->input->is_ajax_request() ) {
                 exit( 'No direct script access allowed' );
             }

             $this->load->model( 'Subadmin_model' );
             $respArr = array ();
             if ( $this->Subadmin_model->is_email_available( $_POST['email'] ) ) {
                 $respArr = array ('code' => 201, 'msg' => 'Email Already Registered');
             }
             else {
                 $respArr = array ('code' => 200, 'msg' => 'Email Available');
             }
             echo json_encode( $respArr );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function view
      * @description To fetch out particular sub-admin details
      * @return type null;
      */
     public function view() {

         try {
             $getDataArr = $this->input->get();
             $admin_id   = $getDataArr['id'];
             if ( empty( $admin_id ) ) {
                 show404( 'Invalid request' );
                 return;
             }
             #new Code
             $this->config->load( 'ACL_config', TRUE );
             $data['admininfo']  = $this->admininfo;
             $data['acl_config'] = $this->config->item( 'permission', 'ACL_config' );
             #new code End

             $whereArr          = [];
             $whereArr['where'] = array ('admin_id' => $admin_id);
             $adminField        = ['admin_id', 'admin_name', 'status', 'admin_email', 'create_date', 'permission'];
             $adminInfo         = $this->Common_model->fetch_data( 'admin', $adminField, $whereArr, true );

             $data['admindetail'] = $adminInfo;
             $data['permission']  = $adminInfo['permission'];
             load_views( '/subadmin/admin-view', $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function deleterecords
      * @description function to permanent delete a record from DB
      */
     public function deleterecords() {

         try {
             $get    = $this->input->get();
             $userId = $get['userId'];
             $this->Subadmin_model->delete_data( $userId );
             redirect( '/subadmin' );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function edit
      * @description to edit/update subadmin details and permissions
      */
     public function edit() {

         try {
             $getData = $this->input->get();

             $post = $this->input->post();

             $this->config->load( 'ACL_config', TRUE );
             $data['admininfo']  = $this->admininfo;
             $data['acl_config'] = $this->config->item( 'permission', 'ACL_config' );

             if ( isset( $post ) && !empty( $post ) ) {//PARENT IF START
                 $admin_id = (isset( $post['token'] ) && !empty( $post['token'] )) ? encryptDecrypt( $post['token'], 'decrypt' ) : '';

                 $subAdminUpdateArr = [];
                 $subAdminUpdateArr = array (
                     'admin_name'  => $post['name'],
                     'admin_email' => $post['email'],
                     'status'      => $post['status'],
                     "permission"  => json_encode( ( object ) array ("permission" => json_decode( $post['permission'] )) )
                 );

                 if ( isset( $post['newpassword'] ) && !empty( $post['newpassword'] ) ) {//IF 1 START
                     $subAdminUpdateArr['admin_password'] = hash( 'sha256', trim( $post['newpassword'] ) );
                 }//IF 1 END

                 $whereArr          = [];
                 $whereArr['where'] = array ('admin_id' => $admin_id);
                 $isSuccess         = $this->Common_model->update_single( 'admin', $subAdminUpdateArr, $whereArr );

                 if ( 1 !== $post['status'] ) {//IF 2 START
                     $this->Common_model->update_single( 'sub_admin', ['status' => 2], $whereArr );
                 }//IF 2 END

                 $this->Common_model->delete_data( 'sub_admin', $whereArr );


                 if ( $isSuccess ) {//IF 4 START
                     $alertMsg         = [];
                     $alertMsg['text'] = $this->lang->line( 'subadmin_updated' );
                     $alertMsg['type'] = 'Success!';
                     $this->session->set_flashdata( 'alertMsg', $alertMsg );
                     redirect( '/admin/subadmin' );
                 }//IF 4 END
                 else {//ELSE 4 START
                     $data['msg'] = $this->lang->line( 'something_went_Worng' );
                     load_views( '/subadmin/edit', $data );
                 }//ELSE 4 END
             }//PARENT IF END
             else {//PARENT ELSE START
                 $admin_id = (isset( $getData['id'] ) && !empty( $getData['id'] )) ? $getData['id'] : '';

                 if ( empty( $admin_id ) ) {//IF 1 START
                     show404( $this->lang->line( 'no_user' ) );
                 }//IF 1 END

                 $whereArr          = [];
                 $whereArr['where'] = array ('admin_id' => $admin_id);
                 $adminField        = ['admin_id', 'admin_name', 'status', 'admin_email', 'create_date', 'permission'];
                 $adminInfo         = $this->Common_model->fetch_data( 'admin', $adminField, $whereArr, true );


                 $data['permission']  = stripslashes( $adminInfo['permission'] );
                 $data['admindetail'] = $adminInfo;
                 $data['admin_id']    = $admin_id;

                 load_views( '/subadmin/edit', $data );
             }//PARENT ELSE END
         }//TRY END
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function block
      * @description to Update subadmin status as blocked
      */
     public function block() {

         try {
             if ( !$this->input->is_ajax_request() ) {
                 exit( 'No direct script access allowed' );
             }
             try {
                 $id      = $this->input->post( 'userId' );
                 $status  = $this->input->post( 'status' );
                 $where   = array ('userId' => $id);
                 $params  = array ('status' => $status);
                 $result1 = $this->Subadmin_model->blockuser( $params, $where );
                 if ( $result1 == true ) {
                     $result = array ('code' => 200);
                 }
                 else {
                     $result = array ('code' => 201);
                 }
                 echo json_encode( $result );
             }
             catch ( Exception $e ) {
                 echo 'Message: '.$e->getMessage();
                 die;
             }
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



 }

?>