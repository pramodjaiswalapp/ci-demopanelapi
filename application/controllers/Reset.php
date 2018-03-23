<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('email', 'url'));
        $this->load->library('encrypt');
        $this->load->model('Common_model');
        $this->load->language('common');
    }

    public function index() {
        $token = $this->input->get('token');
        $resetSuccess = $this->input->get('resetSuccess');
        $resetSuccess = isset($resetSuccess) ? $resetSuccess : "";
        if (isset($token) && !empty($token)) {
            $where = array('where' => array('reset_token' => $token));
            $userinfo = $this->Common_model->fetch_data('ai_user', array('user_id', 'isreset_link_sent', 'reset_link_time', 'status'), $where, true);
            $currenttime = time();
            $linksenttime = strtotime($userinfo['reset_link_time']);
            $timediff = $currenttime - $linksenttime;
            if (!empty($userinfo) && $userinfo['status'] == 1) {
                $data = array();
                $data['token'] = $token;
                $data['userId'] = $userinfo['user_id'];
                $data["csrfName"] = $this->security->get_csrf_token_name();
                $data["csrfToken"] = $this->security->get_csrf_hash();
                $data["token"] = $token;
                $this->load->view('reset/index', $data);
            } else if (!empty($userinfo) && $userinfo['status'] == 2) {
                show404($this->lang->line('account_blocked'));
            } else if ($userinfo['isreset_link_sent'] != 1 || ($timediff > (24 * 3600))) {
                show404($this->lang->line('link_expired'));
            } else {
                show404($this->lang->line('invalid_token'));
            }
        } else if ($resetSuccess == 1) {
            $data["csrfName"] = $this->security->get_csrf_token_name();
            $data["csrfToken"] = $this->security->get_csrf_hash();
            $this->load->view('reset/index',$data);
        } else {
            show404($this->lang->line('invalid_request'));
        }
    }

    public function resetpassword() {
        $token = $this->input->post('token');
        $password = $this->input->post('password');

        $alertMsg = array();
        if (empty($token) || empty($password)) {
            $alertMsg['text'] = $this->lang->line('invalid_request');
            $alertMsg['type'] = $this->lang->line('error');
        }

        $where = array('where' => array('reset_token' => $token));
        $userinfo = $this->Common_model->fetch_data('ai_user', array('user_id', 'isreset_link_sent'), $where, true);

        if (!empty($userinfo) && $userinfo['isreset_link_sent'] != 0) {
            /*
             * Encrypt the password
             */
            $password = encrypt($password);
            $updatearr = array('password' => $password, 'isreset_link_sent' => 0, 'reset_token' => "");
            $where = array('where' => array('user_id' => $userinfo['user_id']));
            try {
                $issuccess = $this->Common_model->update_single('ai_user', $updatearr, $where);
            } catch (Exception $ex) {
                $alertMsg['text'] = $ex->getMessage();
                $alertMsg['type'] = $this->lang->line('error');
            }

            if ($issuccess) {
                $alertMsg['text'] = $this->lang->line('reset_success');
                $alertMsg['type'] = $this->lang->line('success');
            } else {
                $alertMsg['text'] = $this->lang->line('try_again');
                $alertMsg['type'] = $this->lang->line('error');
            }
        } else {
            $alertMsg['text'] = $this->lang->line('password_already_reset');
            $alertMsg['type'] = $this->lang->line('error');
        }
        /*
         * Redirect if passoword change success
         */
        $this->session->set_flashdata("alertMsg", $alertMsg);
        redirect('/reset?resetSuccess=1');
    }

    public function success() {
        echo 'Password Reset Success';
        die;
    }

}
