<?php
 defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

 class Dashboard extends MY_Controller {

     private $months = [];

     function __construct() {
         parent::__construct();
         $this->load->model( array ('Common_model', 'Dashboard_Model') );
         $this->data              = [];
         $this->data['admininfo'] = $this->session->userdata( 'admininfo' );

         $this->months = array (
             'January',
             'February',
             'March',
             'April',
             'May',
             'June',
             'July',
             'August',
             'September',
             'October',
             'November',
             'December'
         );

     }



     /**
      * @function index
      * @description to load and render dashboard
      */
     public function index() {
         $where             = [];
         $dataCount         = [];
         $where['where']    = ['status !=' => 3];
         $dataCount         = $this->Common_model->fetch_data( 'ai_user', array ('count(*) as userCount'), $where, true );
         $data['admininfo'] = $this->data['admininfo'];
         $data['userCount'] = $dataCount['userCount'];
         $data['months']    = $this->months;
         load_views( "dashboard/home", $data );

     }



     /**
      *
      */
     function fetch_data() {
         $res = [];
         if ( $this->input->is_ajax_request() ) {
             $post = $this->input->post();
             switch ( $post['type'] ) {
                 case "yearly":
                     $response = $this->Dashboard_Model->get_yearly_data();
                     if ( null !== $response ) {
                         $years = [];
                         $users = [];
                         foreach ( $response as $index => $user ) {
                             $years[] = ( int ) $user['y'];
                             $users[] = ( int ) $user['total'];
                         }
                         $res['year']   = $years;
                         $res['users']  = $users;
                         $res['status'] = true;
                     }
                     else {
                         $res['status'] = false;
                     }
                     echo json_encode( $res );
                     break;
                 case "monthly":
                     $response = $this->Dashboard_Model->get_monthly_data( $post['year'] );
                     if ( null !== $response ) {
                         $years  = [];
                         $users  = [];
                         $months = $this->months;
                         foreach ( $response as $index => $user ) {
                             $month[] = $months[(( int ) $user['y']) - 1];
                             $users[] = ( int ) $user['total'];
                         }
                         $res['year']   = $month;
                         $res['users']  = $users;
                         $res['status'] = true;
                     }
                     else {
                         $res['status'] = false;
                     }
                     echo json_encode( $res );
                     break;
                 case "weekly":
                     $month    = array_flip( $this->months );
                     $response = $this->Dashboard_Model->get_weekly_data( $post['year'], $month[$post['month']] + 1 );

                     if ( null !== $response ) {
                         $years  = [];
                         $users  = [];
                         $months = $this->months;
                         foreach ( $response as $index => $user ) {
                             $month[] = $user['y'];
                             $users[] = ( int ) $user['total'];
                         }
                         $res['year']   = $month;
                         $res['users']  = $users;
                         $res['status'] = true;
                     }
                     else {
                         $res['status'] = false;
                     }
                     echo json_encode( $res );
                     break;
             }
         }

     }



 }
