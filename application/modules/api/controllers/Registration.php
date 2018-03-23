<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/Authentication.php';

class Registration extends Authentication {

    function __construct() {
        parent::__construct();
    }

    public function signup_post() {

        $array = array(
            'CODE'    => 200,
            'MESSAGE' => $this->lang->line('account_creation_successful')
        );
        $this->response($array);
    }

}
