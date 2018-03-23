<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notify extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('Common_model');
        $this->load->library('commonfn');
    }

    /**
     * @name index
     * @description This method is used to list all the customers.
     */
    public function index() {
        $getDataArr = [];
        $getDataArr = $this->input->get();
        $chunkId = (isset($getDataArr['chunkId']) && !empty($getDataArr['chunkId'])) ? $getDataArr['chunkId'] : "1";
        /*
         * Find relevant data and send push
         */
        if (!empty($chunkId)) {
            $whereArr = [];
            $whereArr['where'] = array('id' => $chunkId);
            $pushDataArr = $this->Common_model->fetch_data('ai_noti_chunk', array('chunk_type', 'data', 'payload_data'), $whereArr, true);
            if ($pushDataArr['chunk_type'] == 'android') {
                $isSuccess = $this->sendAndroidBulkPush($pushDataArr);
            } else {
                $isSuccess = $this->sendIosBulkPush($pushDataArr);
            }
            $this->Common_model->delete_data('ai_noti_chunk', $whereArr);
            echo $isSuccess;
            die;
        }
    }

    /*
     * @param type: Array()
     */

    private function sendAndroidBulkPush($pushDataArr) {
        /*
         * Create Android Payload
         */
        $payLoadData = json_decode($pushDataArr['payload_data'], true);
        $payload = [];
        $payload['message'] = $payLoadData['title'];
        $payload['link'] = (isset($payLoadData['link']) && !empty($payLoadData['link'])) ? $payLoadData['link'] : "";
        $payload['desc'] = (isset($payLoadData['message']) && !empty($payLoadData['message'])) ? $payLoadData['link'] : "";
        $payload['time'] = time();
        /*
         * Extract device token of users
         */
        $userData = json_decode($pushDataArr['data'], true);
        $deviceTokens = array_column($userData, 'device_token');
        $isSuccess = $this->commonfn->androidPush($deviceTokens, $payload);
        return $isSuccess;
    }

    private function sendIosBulkPush($pushDataArr) {

        /*
         * Create iOS Payload
         */
        $payLoadData = json_decode($pushDataArr['payload_data'], true);
        $bodyContent = (isset($payLoadData['message']) && !empty($payLoadData['message'])) ? $payLoadData['message'] : $payLoadData['link'];
        $payload = [];
        $payload['alert'] = array('title' => $payLoadData['title'], 'body' => $bodyContent);
        $payload['badge'] = 0;
        $payload['sound'] = 'beep.mp3';
        /*
         * Extract device token of users
         */
        $userData = json_decode($pushDataArr['data'], true);
        $deviceTokens = array_column($userData, 'device_token');

        $isSuccess = $this->commonfn->iosPush($deviceTokens, $payload);
        return $isSuccess;
    }

    /*
     * @params Array();
     */

    public function sendIosPush(){
        $pushDataArr = $this->input->post();
        $isSuccess = $this->commonfn->iosPush($pushDataArr['deviceTokens'], $pushDataArr['payload']);
        return $isSuccess;
    }
    
    /*
     * @params Array();
     */

    public function sendAndroidPush() {
        $pushDataArr = $this->input->post();
        $isSuccess = $this->commonfn->androidPush($pushDataArr['deviceTokens'], $pushDataArr['payload']);
        return $isSuccess;
    }

}
