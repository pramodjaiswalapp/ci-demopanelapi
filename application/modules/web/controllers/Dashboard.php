<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'custom_cookie', 'cookie']);
        $this->load->model('Common_model');
        $this->load->library('session');

        $this->userinfo = $this->session->userdata('userinfo');
        if (empty($this->userinfo)) {
            redirect(base_url() . 'web');
        }
        $this->data             = [];
        $this->data['userinfo'] = $this->userinfo;
    }

    /* Home page function */

    public function index() {

        $data['userinfo'] = $this->data['userinfo'];
        load_views_web("home", $data);
    }

    /*  Logout */

    public function logout() {
        session_destroy();
        delete_cookie('rcc_user_appinventiv');
        $this->session->unset_userdata('userinfo');
        redirect('/web');
    }

}
