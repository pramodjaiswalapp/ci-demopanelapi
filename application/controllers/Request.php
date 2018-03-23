<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Request extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('email', 'url'));
        $this->load->model('Common_model');
        $this->load->language('common');
        $this->load->library('commonfn');
    }

    public function welcomeMail() {
        $email = $this->input->get('email');
        $name = $this->input->get('name');
        $mailinfoarr = [];
        $mailinfoarr['subject'] = 'Welcome to ' . PROJECT_NAME;
        $mailinfoarr['email'] = $email;
        $mailinfoarr['mailerName'] = 'welcome';
        $mailinfoarr['name'] = $name;
        $this->commonfn->sendEmailToUser($mailinfoarr);        
    }

}
