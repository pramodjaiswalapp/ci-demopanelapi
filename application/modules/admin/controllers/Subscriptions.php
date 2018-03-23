<?php
 if ( !defined( 'BASEPATH' ) ) {
     exit( 'No direct script access allowed' );
 }

 /**
  * @author     Appinventiv
  * @date       26-02-2018
  * @controller Subscriptions
  */
 class Subscriptions extends MY_Controller {

     public function __construct() {
         parent::__construct();
         $this->load->model( 'subscription_model' );
         $this->load->language( 'common', 'english' );
         $this->admininfo          = $this->session->userdata( 'admininfo' );
         $this->subscription_table = 'ai_subscriptions';
         $this->data               = [];
         $this->data['admininfo']  = $this->admininfo;

     }



     /**
      * @function index
      * @description to list all subscription related data
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

             $defaultValue['admininfo'] = $this->admininfo;

             //Get subscription data from DB
             $respdata             = $this->subscription_model->getSubscriptionsData( $defaultValue['limit'], $offset, $default );
             $defaultValue['link'] = $this->commonfn->pagination( $pageurl, $respdata['totalrows'], $defaultValue['limit'] );

             $defaultValue['records']               = $respdata['records'];
             $defaultValue['allUsersCount']         = $respdata['totalrows'];
             $defaultValue['totalrows']             = $respdata['totalrows'];
             $defaultValue['subscription_type_arr'] = [1 => 'Daily', 2 => 'Wekly', 3 => 'Monthly', 4 => 'Yearly', 5 => 'One-Time'];

             //Csrf token manage
             $defaultValue['csrfName']  = $this->security->get_csrf_token_name();
             $defaultValue['csrfToken'] = $this->security->get_csrf_hash();

             load_views( 'subscriptions/index', $defaultValue );
         }//TRY END
         catch ( Exception $exception ) {

             showException( $exception->getMessage() );
             exit;
         }//CATCH END

     }



     /**
      * @function add
      * @description to add new subscription data in DB
      */
     public function add() {

         try {

             $data['admininfo'] = $this->admininfo;
             $post              = $this->input->post();

             //Server Side validation
             $this->form_validation->set_rules( 'title', 'Subscription Name', 'trim|required' );
             $this->form_validation->set_rules( 'sub_price', 'Price', 'trim|required' );

             $subs_recuring = 0;
             if ( TRUE !== $this->form_validation->run() ) {
                 $this->session->set_flashdata( 'add_subscriptions_error', $data );
                 redirect( '/admin/Subscriptions' );
             }
             else {
                 $subs_recuring = 0;

                 // If post request
                 if ( isset( $post ) && !empty( $post ) ) {
                     $subsInsertArr = [];
                     $subsInsertArr = [
                         'subscription_name' => trim( $post['title'] ),
                         'status'            => ACTIVE,
                         'price'             => $post['sub_price'],
                         'description'       => $post['description'],
                         'create_date'       => date( 'Y-m-d H:i:s' ),
                         'update_date'       => date( 'Y-m-d H:i:s' )
                     ];
                     switch ( $post['one_time_option'] ) {
                         case RECURRING_DAY:

                             $subs_recuring = RECURRING_DAY;
                             break;
                         case RECURRING_WEEK:

                             $subs_recuring = RECURRING_WEEK;
                             break;
                         case RECURRING_MONTH:

                             $subs_recuring = RECURRING_MONTH;
                             break;
                         case RECURRING_YEAR:

                             $subs_recuring = RECURRING_YEAR;
                             break;
                         case ONE_TIME:

                             $subs_recuring = ONE_TIME;
                             break;

                         default:
                             break;
                     }
                     $subsInsertArr['subs_recurring'] = $subs_recuring;

                     //insert subscription data in DB
                     $subs_id = $this->Common_model->insert_single( $this->subscription_table, $subsInsertArr );

                     if ( $subs_id ) {
                         $alertMsg         = [];
                         $alertMsg['text'] = $this->lang->line( 'subscription_created' );
                         $alertMsg['type'] = 'Success!';

                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                         redirect( '/admin/Subscriptions' );
                     }
                     $data['saveErr'] = 'Please try again';
                 }
                 else {
                     //Csrf token manage
                     $data['csrfName']  = $this->security->get_csrf_token_name();
                     $data['csrfToken'] = $this->security->get_csrf_hash();
                 }
                 load_views( '/subscriptions/index', $data );
             }
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function edit
      * @description to edit a subscription data and update in DB
      */
     public function edit() {


         try {
             $data['admininfo'] = $this->admininfo;
             $post              = $this->input->post();
             //Server Side validation
             $this->form_validation->set_rules( 'title', 'Subscription Name', 'trim|required' );
             $this->form_validation->set_rules( 'sub_price', 'Price', 'trim|required' );

             if ( !$this->form_validation->run() ) {
                 $this->session->set_flashdata( 'edit_subscriptions_error', $data );
                 redirect( '/admin/Subscriptions' );
             }
             else {
                 $post          = $this->input->post();
                 $subs_recuring = 0;
                 $id            = (!empty( $post['id_form'] )) ? encryptDecrypt( $post['id_form'], 'decrypt' ) : 0;
                 /*
                  * If post request
                  */
                 if ( isset( $post ) && !empty( $post ) ) {

                     $subsUpdateArr = array (
                         'subscription_name' => trim( $post['title'] ),
                         'price'             => $post['sub_price'],
                         'description'       => $post['description'],
                         'update_date'       => date( 'Y-m-d H:i:s' )
                     );


                     switch ( $post['one_time_option'] ) {
                         case RECURRING_DAY:

                             $subs_recuring = RECURRING_DAY;
                             break;
                         case RECURRING_WEEK:

                             $subs_recuring = RECURRING_WEEK;
                             break;
                         case RECURRING_MONTH:

                             $subs_recuring = RECURRING_MONTH;
                             break;
                         case RECURRING_YEAR:

                             $subs_recuring = RECURRING_YEAR;
                             break;

                         default:
                             break;
                     }
                     $subsUpdateArr['subs_recurring'] = $subs_recuring;


                     $this->db->trans_begin();
                     $where["where"] = ["subscription_id" => $id];
                     $subs_id        = $this->Common_model->update_single( $this->subscription_table, $subsUpdateArr, $where );

                     if ( $this->db->trans_status() === TRUE && $subs_id ) {

                         $this->db->trans_commit();

                         $alertMsg         = [];
                         $alertMsg['text'] = $this->lang->line( 'subscription_updated' );
                         $alertMsg['type'] = 'Success!';
                         $this->session->set_flashdata( 'alertMsg', $alertMsg );
                         redirect( '/admin/Subscriptions' );
                     }

                     $this->db->trans_rollback();
                     $data['saveErr'] = 'Please try again';
                 }
                 else {
                     //Csrf token manage
                     $data['csrfName']  = $this->security->get_csrf_token_name();
                     $data['csrfToken'] = $this->security->get_csrf_hash();
                 }
                 load_views( '/subscriptions/index', $data );
             }
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



     /**
      * @function view subscription detail
      * @description to view details of a subscription
      */
     public function view() {
         try {
             $data['admininfo'] = $this->admininfo;
             $get               = $this->input->get();

             //If request is empty,redirect to list Page
             if ( !isset( $get["id"] ) || empty( $get["id"] ) ) {
                 redirect( '/admin/Subscriptions' );
             }
             //fetch data from DB
             $where["where"]            = ['status' => ACTIVE, "subscription_id" => $get["id"]];
             $data["subscription_data"] = $this->Common_model->fetch_data( $this->subscription_table, '*', $where );
             if ( empty( $data["subscription_data"] ) ) {
                 redirect( '/admin/Subscriptions' );
             }
             //Csrf token manage
             $data['csrfName']  = $this->security->get_csrf_token_name();
             $data['csrfToken'] = $this->security->get_csrf_hash();

             load_views( '/subscriptions/view_subs', $data );
         }
         catch ( Exception $exception ) {
             showException( $exception->getMessage() );
             exit;
         }

     }



 }

?>