<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper(['url', 'form', 'custom_cookie', 'common']);
        $this->load->model('Common_model');
        $this->load->library('session');
        $this->lang->load('common', "english");
        $this->load->library('form_validation');
        $this->remember_me = $sessionData       = validate_user_cookie('rcc_user_appinventiv', 'ai_user');

        if ($sessionData) {
            $this->session->set_userdata('userinfo', $sessionData);
        }
        $this->userinfo = $this->session->userdata('userinfo');

        if ($this->userinfo) {
            redirect(base_url() . "web/Dashboard");
        }
    }

    /**
     * @function: index
     * @description: if email and password are correct then he can login
     *
     * @param:username:email
     * @param:password:password
     *
     */
    public function index() {

        $data              = [];
        $data["csrfName"]  = $this->security->get_csrf_token_name();
        $data["csrfToken"] = $this->security->get_csrf_hash();

        $data["twitter_url"] = twitter_auth();

        if ($this->input->post()) {
            $postDataArr = $this->input->post();

            $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required');
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                load_views_web('login', $data);
            } else {
                $email    = $postDataArr['email'];
                $password = $postDataArr['password'];
                $pass     = hash('sha256', $password);

                // Matched the Credentials
                try {
                    $userInfo = $this->Common_model->fetch_data('ai_user', array('user_id', 'first_name', 'email', 'image', 'image_thumb', 'registered_date'), array('where' => array('email' => $email, 'password' => $pass, 'status' => 1)), true);
                } catch (Exception $ex) {

                    echo $ex->getMessage();
                }

                //If credentials are matched set the session
                if (!empty($userInfo)) {

                    $userdata = array(
                        "user_id"     => $userInfo ['user_id'],
                        "first_name"  => $userInfo ['first_name'],
                        "email"       => $userInfo ['email'],
                        "image"       => $userInfo ['image'],
                        "image_thumb" => $userInfo ['image_thumb']
                    );

                    // SETS COOKIE DATA in Remember Me case
                    if (isset($postDataArr["filter"]) && $postDataArr["filter"] == "remember_me") {
                        $this->load->helper(["cookie", "string"]);
                        $cookieData["cookie_validator"] = random_string('alnum', 12);
                        $cookieData["cookie_selector"]  = hash("sha256", date("Y-m-d H:i:s") . $postDataArr["email"]);

                        $cookieExpiryTime = time() + COOKIE_EXPIRY_TIME;

                        set_cookie(
                                "rcc_user_appinventiv", "{$cookieData['cookie_selector']}:{$cookieData['cookie_validator']}", $cookieExpiryTime
                        );

                        $cookieData["cookie_validator"] = hash("sha256", $cookieData["cookie_validator"] . $userInfo["registered_date"]);

                        // Update cookie data in User table
                        $this->Common_model->update_single("ai_user", $cookieData, ["where" => ["user_id" => $userInfo["user_id"]]]);
                    }

                    $this->session->set_flashdata("greetings", "Welcome!");
                    $this->session->set_flashdata("message", "You have successfully logged in");
                    $this->session->set_userdata('userinfo', $userdata);

                    redirect(base_url() . "web");
                } else {
                    $data['email']    = $email;
                    $data['password'] = $password;
                    $data['error']    = $this->lang->line('invalid_email_password');
                    load_views_web('login', $data);
                }
            }
        } else {
            $data['user_details'] = $this->remember_me;
            load_views_web('login', $data);
        }
    }

    /*  Redirect to twitter login */

    public function redirect() {
        redirect(twitter_auth());
    }

}
