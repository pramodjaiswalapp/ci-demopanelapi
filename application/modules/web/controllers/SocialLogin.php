<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SocialLogin extends MX_Controller {

    public function __construct() {

        $this->load->helper(['url', 'form', 'custom_cookie']);
        $this->load->library("session");
        $this->load->model("Common_model");
        $this->load->model("CommonModel");
        $this->lang->load('common', "english");
        $this->userinfo  = $this->session->userdata('userinfo');
        $this->csrftoken = $this->security->get_csrf_hash();
    }

    /**
     * @function ajax_post_login
     * @description get user's basic info from FB response
     *
     * @return json
     */
    public function ajax_post_login() {

        $post = $this->input->post();

        // Check if FB ID exists in response
        if (!isset($post['response']) || empty($post['response']['id'])) {

            $errorData = ["error" => true, "message" => "facebook id is empty.", "csrf_token" => $this->security->get_csrf_hash()];
            $this->CommonModel->response($errorData);
        }

        $fb_id = $post['response']['id'];
        
        // Check if fb id exists in DB
        try {
            $where          = [];
            $where['where'] = ['fb_id' => $fb_id];

            if (!empty($post['response']['email'])) {
                $where['or_where'] = ['email' => $post['response']['email']];
            }

            //check data in DB for email or social id
            $userInfo = $this->Common_model->fetch_data('ai_user', array('fb_id', 'user_id', 'first_name', 'email', 'image', 'image_thumb'), $where, true);
        } catch (Exception $ex) {

            echo $ex->getMessage();
            die('error');
        }

        // DB transition begins
        $this->db->trans_begin();

        // check if data exists,then set userdata for Session , else insert data in User table
        $userdata = $this->check_and_set_userdata($userInfo, $post, FACEBOOK_LOGIN, $fb_id);

        // Check if all queries in transition executed successfully
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $this->session->set_userdata('userinfo', $userdata);
        }

        // Send response
        $errorData = ["error" => false, "message" => "success", "csrf_token" => $this->security->get_csrf_hash()];
    }

    /**
     * @function ajax_post_linkedin
     * @description Ajax handler - get user's basic info from Linkedin response
     *
     * @return json
     */
    public function ajax_post_linkedin() {

        $post_decode      = $this->input->post();
        $post['response'] = json_decode($post_decode['response']);
        $status           = false;
        $msg              = "success";

        // Check if Linkedin ID exists
        if (!isset($post['response']) || empty($post['response']->id)) {

            $errorData = ["error" => true, "message" => "linkedin id is empty.", "csrf_token" => $this->security->get_csrf_hash()];
            $this->CommonModel->response($errorData);
        }

        $linkedin_id = $post['response']->id;


        // Check if Linkedin id exists in DB
        try {
            $where          = [];
            $where['where'] = ['linkedin_id' => $linkedin_id];

            if (!empty($post['response']->emailAddress)) {
                $where['or_where'] = ['email' => $post['response']->emailAddress];
            }

            // check data in DB for email or social id
            $userInfo = $this->Common_model->fetch_data('ai_user', array('linkedin_id', 'user_id', 'first_name', 'email', 'image', 'image_thumb'), $where, true);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die;
        }
        // DB transition begins
        $this->db->trans_begin();

        // check if data exists,then set userdata for Session , else insert data in User table
        $userdata = $this->check_and_set_userdata($userInfo, $post, LINKEDIN_LOGIN, $linkedin_id);
        // Check if all queries in transition executed successfully
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();
            $status = true;
            $msg    = "failure";
        } else {
            $this->db->trans_commit();
            $this->session->set_userdata('userinfo', $userdata);
        }

        $errorData = [
            "error"      => $status,
            "message"    => $msg,
            "csrf_token" => $this->security->get_csrf_hash()
        ];
        $this->CommonModel->response($errorData);
    }

    /**
     * @function ajax_post_google
     * @description Ajax handler - get user's basic info from google response
     *
     * @return json
     */
    public function ajax_post_google() {

        $post = $this->input->post();

        $status = false;
        $msg    = "success";

        // Check if Google ID exists
        if (!isset($post['response']) || empty($post['response']['google_id'])) {

            $errorData = ["error" => true, "message" => "Google id is empty.", "csrf_token" => $this->security->get_csrf_hash()];
            $this->CommonModel->response($errorData);
        }

        $google_id = $post['response']['google_id'];

        // Check if google id exists in DB
        try {
            $where          = [];
            $where['where'] = ['google_id' => $google_id];

            if (!empty($post['response']['email'])) {
                $where['or_where'] = ['email' => $post['response']['email']];
            }
            $userInfo = $this->Common_model->fetch_data('ai_user', array('google_id', 'user_id', 'first_name', 'email', 'image', 'image_thumb'), $where, true);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die('error');
        }

        // DB transition begins
        $this->db->trans_begin();

        // check if data exists,then set userdata for Session , else insert data in User table
        $userdata = $this->check_and_set_userdata($userInfo, $post, GOOGLE_LOGIN, $google_id);

        // Check if all queries in transition executed successfully
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();
            $status = true;
            $msg    = "failure";
        } else {
            $this->db->trans_commit();

            $this->session->set_userdata('userinfo', $userdata);
        }
        $errorData = ["error" => $status, "message" => $msg, "csrf_token" => $this->security->get_csrf_hash()];
        $this->CommonModel->response($errorData);
    }

    /**
     * @function instagram
     * @description get instagram user's basic info
     *
     */
    public function instagram() {

        $access_token = INSTA_ACCESS_TOKEN;
        $url          = INSTA_URL . $access_token;

        // hit instgaram api to get user's basic info
        $curlSession = curl_init();
        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        $user_insta_data = json_decode(curl_exec($curlSession));
        curl_close($curlSession);

        if (empty($user_insta_data) || (!isset($user_insta_data->data) && empty($user_insta_data->data->id) )) {
            //send error on Login page
            redirect(base_url() . "web");
        }

        $instagram_id = $user_insta_data->data->id;

        $post_data['response'] = (array) $user_insta_data->data;

        // Check if instagram id exists in DB
        try {
            $where          = [];
            $where['where'] = ['instagram_id' => $instagram_id];

            if ($user_insta_data->data->username) {
                $where["or_where"] = ['username' => $user_insta_data->data->username];
            }

            $userInfo = $this->Common_model->fetch_data('ai_user', array('instagram_id', 'user_id', 'first_name', 'email', 'image', 'image_thumb'), $where, true);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die;
        }

        // DB transition begins
        $this->db->trans_begin();

        // check if data exists,then set userdata for Session , else insert data in ai_user table
        $userdata = $this->check_and_set_userdata($userInfo, $post_data, INSTAGRAM_LOGIN);

        // Check if all queries in transition executed successfully
        if ($this->db->trans_status() === FALSE) {

            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();

            $this->session->set_userdata('userinfo', $userdata);
            //  Redirect to Dashboard after session is set
            redirect(base_url() . "web/closeLoginWindow");

            //redirect(base_url() . "web/Dashboard");
        }
    }

    /**
     * @function redirect_to_instagram
     * @description redirect to Instagram Login page
     */
    public function redirect_to_instagram() {

        $client_id    = INSTA_CLIENT_ID;
        $redirect_url = INSTA_REDIRECT_URL;

        header('Location: ' . INSTA_AUTH_URL . $client_id . "&redirect_uri=" . $redirect_url . "&response_type=token");
        die();
    }

    /**
     * @function check_and_set_userdata
     * @description check if DB result contain user data, if data doesn't exist, make a fresh entry in ai_user table
     *
     * @param array userInfo
     * @param array post
     * @param string type
     * @param string social_id
     *
     * @return array
     */
    function check_and_set_userdata($userInfo, $post, $type, $social_id = NULL) {

        // If Social ID is found in the DB , set the user session
        if (!empty($userInfo)) {

            $userdata = array(
                "user_id"     => $userInfo['user_id'],
                "first_name"  => $userInfo['first_name'],
                "email"       => $userInfo['email'],
                "image"       => $userInfo['image'],
                "image_thumb" => $userInfo['image_thumb']
            );

            // Update Social Id if email is already there but social id is empty
            $column = [ FACEBOOK_LOGIN => 'fb_id', LINKEDIN_LOGIN => 'linkedin_id',
                GOOGLE_LOGIN   => 'google_id'];

            // check if social id column is empty or NULL in table, then update it
            if (isset($userInfo[$column[$type]]) && empty($userInfo[$column[$type]])) {

                $set            = [$column[$type] => $social_id];
                $where['where'] = ['email' => $userInfo['email']];

                $update = $this->update_details_in_user($set, $where);
            }
        } else {
            // Add new User in DB and set session
            $new_user_data = $this->set_signup_data($post, $type);

            $insert_id = $this->Common_model->insert_single("ai_user", $new_user_data);

            // Set user data for Session
            $userdata = array(
                "user_id"     => $insert_id,
                "first_name"  => $new_user_data['first_name'], "email"       => $new_user_data['email'],
                "image"       => $new_user_data['image'],
                "image_thumb" => ''
            );
        }
        return $userdata;
    }

    /**
     * @function twitterauth
     * @description It is callback method for twitter authentication
     *
     */
    public function twitterauth() {
        $get     = $_GET;
        $session = $_SESSION;

        try {

            if (isset($get['oauth_verifier']) && !empty(($get['oauth_verifier']))) {

                include_once APPPATH . "libraries/tmhoauth.php";

                $tmhOAuth                        = new tmhoauth(array('consumer_key' => TWITTER_CONSUMER_TOKEN, 'consumer_secret' => TWITTER_CONSUMER_SECRET, 'user_token' => '', 'user_secret' => ''));
                $tmhOAuth->config['user_token']  = $session['oauth']['oauth_token'];
                $tmhOAuth->config['user_secret'] = $session['oauth']['oauth_token_secret'];
                $code                            = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array('oauth_verifier' => $get['oauth_verifier']));

                if ($code != 200) {
                    throw new Exception('Response code failure. Please try again.');
                }

                $session['access_token']         = $tmhOAuth->extract_params($tmhOAuth->response['response']);
                $tmhOAuth->config['user_token']  = $session['access_token']['oauth_token'];
                $tmhOAuth->config['user_secret'] = $session['access_token']['oauth_token_secret'];

                $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));
                $resp = json_decode($tmhOAuth->response ['response']);

                if (empty($resp)) {
                    throw new Exception('Twitter authentication failed. Please try again.');
                }


                // Check if twitter ID exists in DB
                $where['where'] = ['twitter_id' => $resp->id];
                $userInfo       = $this->Common_model->fetch_data('ai_user', array('user_id', 'first_name', 'email', 'image', 'image_thumb'), $where, true);

                $response['response'] = $resp;

                //DB transition begins

                $this->db->trans_begin();

                // check if data exists,then set userdata for Session , else insert data in ai_user table
                $userdata = $this->check_and_set_userdata($userInfo, $response, TWITTER_LOGIN);

                // Check if all queries in transition executed successfully
                if ($this->db->trans_status() === FALSE) {

                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();

                    $this->session->set_userdata('userinfo', $userdata);

                    redirect(base_url() . "web/closeLoginWindow");
                }
            } else {

                redirect(twitter_auth());
            }
        } catch (Exception $ex) {
            redirect(base_url() . "web");
        }
    }

    /**
     * @function update_details_in_user
     * @description it is to update social id in ai_user table
     *
     * @param array set
     * @param array data
     *
     * @return string
     */
    function update_details_in_user($set, $data) {

        return $update = $this->Common_model->update_single
                ('ai_user', $set, $data);
    }

    /**
     * @function set_signup_data
     * @description it is to set insert data for inserting in ai_user Table
     *
     * @param array post
     * @param string type
     *
     * @return array
     */
    function set_signup_data($post, $type) {

        $post_data_res = $post['response'];
        $post_data     = (array) $post_data_res;

        $signupArr = [];

        //  Set basic info from Facebook API response
        if ($type == FACEBOOK_LOGIN) {

            $signupArr["fb_id"]      = isset($post_data["id"]) ? trim($post_data["id"]) : "";
            $signupArr["first_name"] = isset($post_data["first_name"]) ? trim($post_data["first_name"]) : "";
            $signupArr["image"]      = isset($post_data['picture']['data']['url']) ? $post_data['picture']['data']['url'] : "";
            $signupArr["email"]      = isset($post_data['email']) ? $post_data['email'] : "";
            $signupArr["last_name"]  = isset($post_data["last_name"]) ? trim($post_data["last_name"]) : "";
        }

        //  Set basic info from Linkedin API response
        else if ($type == LINKEDIN_LOGIN) {

            $signupArr["linkedin_id"] = isset($post_data['id']) ? trim($post_data['id']) : "";
            $signupArr["first_name"]  = isset($post_data['firstName']) ? trim($post_data['firstName']) : "";
            $signupArr["image"]       = isset($post_data['pictureUrl']) ? $post_data['pictureUrl'] : "";
            $signupArr["email"]       = isset($post_data['emailAddress']) ? $post_data['emailAddress'] : "";
            $signupArr["last_name"]   = isset($post_data['lastName']) ? trim($post_data['lastName']) : "";
        }

        //  Set basic info from Google API response
        else if ($type == GOOGLE_LOGIN) {

            $signupArr["google_id"]  = isset($post_data['google_id']) ? trim($post_data['google_id']) : "";
            $signupArr["first_name"] = isset($post_data['first_name']) ? trim($post_data['first_name']) : "";
            $signupArr["image"]      = isset($post_data['image']) ? $post_data['image'] : "";
            $signupArr["email"]      = isset($post_data['email']) ? $post_data['email'] : "";
            $signupArr["last_name"]  = isset($post_data['last_name']) ? trim($post_data['last_name']) : "";
        }

        //  Set basic info from Instagram API response
        else if ($type == INSTAGRAM_LOGIN) {

            $signupArr["instagram_id"] = isset($post_data['id']) ? trim($post_data['id']) : "";
            $signupArr["first_name"]   = isset($post_data['full_name']) ? trim($post_data['full_name']) : "";
            $signupArr["image"]        = isset($post_data['profile_picture']) ? $post_data['profile_picture'] : "";
            $signupArr["email"]        = isset($post_data['email']) ? $post_data['email'] : "";
            $signupArr["last_name"]    = isset($post_data['last_name']) ? trim($post_data['last_name']) : "";
            $signupArr["username"]     = isset($post_data['username']) ? trim($post_data['username']) : "";
        }

        //  Set basic info from Twitter API response
        else if ($type == TWITTER_LOGIN) {

            $name                    = explode(' ', $post_data['name']);
            $signupArr['first_name'] = (isset($name[0]) && !empty($name[0])) ? trim($name[0]) : '';
            $signupArr['last_name']  = (isset($name[count($name) - 1]) ) ? $name[count($name) - 1] : '';
            $signupArr["twitter_id"] = isset($post_data['id']) ? trim($post_data['id']) : "";
            $signupArr["image"]      = isset($post_data['profile_image_url_https']) ? $post_data['profile_image_url_https'] : "";
            $signupArr["email"]      = isset($post_data['email']) ? $post_data['email'] : "";
        }

        $signupArr["registered_date"] = date('Y-m-d H:i:s');
        $signupArr["middle_name"]     = isset($post_data["middle_name"]) ? trim($post_data["middle_name"]) : "";
        $signupArr["dob"]             = isset($post_data["dob"]) ? date('Y-m-d', strtotime($post_data["dob"])) : "";
        $signupArr["age"]             = isset($post_data["age"]) ? trim($post_data["age"]) : "";
        $signupArr["phone"]           = isset($post_data["phone"]) ? trim($post_data["phone"]) : "";
        $signupArr["address"]         = isset($post_data["address"]) ? trim($post_data["address"]) : "";
        $signupArr["user_lat"]        = isset($post_data["user_lat"]) ? $post_data["user_lat"] : "";
        $signupArr["user_long"]       = isset($post_data["user_long"]) ? $post_data["user_long"] : "";
        $signupArr["country_id"]      = isset($post_data['country_id']) ? $post_data['country_id'] : "";
        $signupArr["state_id"]        = isset($post_data['state_id']) ? $post_data['state_id'] : "";
        $signupArr["city_id"]         = isset($post_data['city_id']) ? $post_data['city_id'] : "";
        $signupArr["password"]        = isset($post_data['password']) ? encrypt($post_data["password"]) : "";

        if (isset($post_data["gender"])) {
            if (trim(strtolower($post_data["gender"])) == 'male') {
                $signupArr["age"] = MALE_GENDER;
            } else if (trim(strtolower($post_data["gender"])) == 'female') {
                $signupArr["age"] = FEMALE_GENDER;
            } else if (trim(strtolower($post_data["gender"])) == 'other') {
                $signupArr["age"] = OTHER_GENDER;
            }
        }
        return $signupArr;
    }

}
