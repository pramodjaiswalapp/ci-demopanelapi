<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Twitter_login extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper(['url']);
        $this->load->library('session');
    }

    /** @name dashboard_redirection
     * @desciption   Redirect to Dashboard on successful login */
    public function dashboard_redirection() {

        //close Twitter/Instagram login pop up window and refresh parent browser windows
        echo "</script>opener.location.href = " . base_url() . "web/Dashboard';close();</script>";
        echo "<script>var parent = window.opener;parent.location = '" . base_url() . "web/Dashboard';window.close();</script>";
    }

}
