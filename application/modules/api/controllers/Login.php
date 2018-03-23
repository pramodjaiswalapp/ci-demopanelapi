<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Login extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Common_model');
        $this->load->helper('email');
        error_reporting(0);
    }

    /**
     * @SWG\Post(path="/Login",
     *   tags={"User"},
     *   summary="Login Information",
     *   description="Login Information",
     *   operationId="index_post",
     *   consumes ={"multipart/form-data"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Password",
     *     required=true,
     *     type="string"
     *   ),
     *    @SWG\Parameter(
     *     name="device_id",
     *     in="formData",
     *     description="Unique Device Id",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="device_token",
     *     in="formData",
     *     description="Device Token required to send push",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="platform",
     *     in="formData",
     *     description="1: Android and 2: iOS",
     *     type="string"
     *   ),
     *   @SWG\Response(response=101, description="Account Blocked"),
     *   @SWG\Response(response=200, description="Login Success"),
     *   @SWG\Response(response=206, description="Unauthorized request"),
     *   @SWG\Response(response=207, description="Header is missing"),
     *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
     * )
     */
    public function index_post() {
        try {
            $postDataArr = $this->post();

            #Setting Form validation Rules
            $config = array(
                array(
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'trim|required|valid_email'
                ),
                array(
                    'field' => 'password',
                    'label' => 'Password',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'device_id',
                    'label' => 'Device Id',
                    'rules' => 'trim|required'
                ),
                array(
                    'field' => 'platform',
                    'label' => 'Device Platform',
                    'rules' => 'required'
                )
            );
            $this->form_validation->set_rules($config);

            $response_array = [];

            #checking Form validation
            if ($this->form_validation->run()) {

                $encrypt_pass = encrypt($postDataArr["password"]);
                $email        = $postDataArr['email'];

                #Fetching user details
                $userInfo = $this->Common_model->fetch_data('ai_user', 'user_id,first_name,email,IF(image !="",CONCAT("' . IMAGE_PATH . '","",image),"") as image,IF(image_thumb !="",CONCAT("' . THUMB_IMAGE_PATH . '","",image_thumb),"") as image_thumb,status', array('where' => array('email' => $email, 'password' => $encrypt_pass)), true);
                if (!empty($userInfo)) {
                    /*
                     * Check if user is not blocksed
                     */
                    if (ACTIVE == $userInfo['status']) {

                        #getting access Token
                        $accessToken = create_access_token($userInfo['user_id'], $email);

                        $postDataArr['user_id'] = $userInfo['user_id'];

                        $sessionArr = [];
                        #setting Session variable
                        $sessionArr = setSessionVariables($postDataArr, $accessToken);

                        #If App Support Single Login
                        if (IS_SINGLE_DEVICE_LOGIN) {
                            $where = array('where' => array('user_id' => $userInfo['user_id']));
                            $this->Common_model->update_single('ai_session', $sessionArr, $where);
                        } else {

                            #If App Support Multiple Login
                            $whereArr          = [];
                            $device_id         = isset($postDataArr['device_id']) ? $postDataArr['device_id'] : "";
                            $whereArr['where'] = ['device_id' => $device_id];
                            $isExist           = $this->Common_model->fetch_data('ai_session', array('session_id,device_token'), $whereArr, true);

                            /*
                             * If user has logged in previously with same device then update his detail
                             * or insert as a new row
                             */
                            #transaction Start
                            $this->db->trans_begin();

                            if (!empty($isExist)) {
                                #updating user session details if device id is same as previous
                                $isSuccess = $this->Common_model->update_single('ai_session', $sessionArr, $whereArr);
                            } else {
                                #inserting details if device id different
                                $isSuccess = $this->Common_model->insert_single('ai_session', $sessionArr);
                            }

                            #if updation/inserting failed
                            if (!$isSuccess) {
                                throw new Exception($this->lang->line('try_again'));
                            }
                        }

                        #Checking Transaction status
                        if (TRUE === $this->db->trans_status()) {
                            #commiting trasaction
                            $this->db->trans_commit();

                            #Push Notification
                            $msg                         = 'Successful Login';
                            $pushDataArr['deviceTokens'] = $isExist['device_token'];

                            #sending push notification according to device
                            if (ANDROID == $postDataArr['platform']) {
                                #sending Android Push
                                $status = $this->set_android_push($msg, $userInfo, $pushDataArr);
                            } else if (IPHONE == $postDataArr['platform']) {
                                #sending IOS push
                                $status = $this->set_ios_push($msg, $userInfo, $pushDataArr);
                            }
                            #Push Notificationend

                            $userInfo['accesstoken'] = $accessToken['public_key'] . '||' . $accessToken['private_key'];

                            #setting Response
                            $response_array = [
                                'code'   => SUCCESS_CODE,
                                'msg'    => $this->lang->line('login_successful'),
                                'result' => $userInfo
                            ];
                        }
                    } else if (BLOCKED == $userInfo['status']) {#if user is blocked/ status =2
                        #setting Response
                        $response_array = [
                            'code'   => ACCOUNT_BLOCKED,
                            'msg'    => $this->lang->line('account_blocked'),
                            'result' => []
                        ];
                    } else {
                        #credential are correct but status is not in 1 or 2
                        #setting Response
                        $response_array = [
                            'code'   => ACCOUNT_INACTIVE,
                            'msg'    => $this->lang->line('account_inactive'),
                            'result' => []
                        ];
                    }
                } else {#unable to login/invalid credential
                    #setting Response
                    $response_array = [
                        'code'   => INVALID_CREDENTIALS,
                        'msg'    => $this->lang->line('invalid_credentials'),
                        'result' => []
                    ];
                }
            } else {
                $err = $this->form_validation->error_array();
                $arr = array_values($err);

                #setting Response
                $response_array = [
                    'code'   => PARAM_REQ,
                    'msg'    => $arr[0],
                    'result' => []
                ];
            }

            #Sending Response
            $this->response($response_array);
        }#try end
        catch (Exception $e) {#catch Start
            $this->db->trans_rollback();
            $error = $e->getMessage();

            #setting Response
            $response_array = [
                'code'   => TRY_AGAIN_CODE,
                'msg'    => $error,
                'result' => []
            ];

            #Sending Response
            $this->response($response_array);
        }#catch End
    }

    /**
     * @function set_android_push
     * @descriptin sending android Push notification
     *
     * @param String $msg Message to send in notification
     * @param string|int $post_owner_info user id
     * @param array $pushDataArr data required to send push notification
     *
     * @return Boolean TRUE|FALSE
     */
    private function set_android_push($msg, $post_owner_info, $pushDataArr) {

        $status = false;

        $pay_load['message'] = $msg;
        $pay_load['user_id'] = $post_owner_info['user_id'];
        $pay_load['time']    = time();
        $pay_load['image']   = 'http://www.nzmotels.co.nz/assets/Motels/touchwood_motor_lodge/touchwood-motor-lodge_(1).jpeg';

        $pushDataArr['payload'] = $pay_load;

        #sending push notification
        $status = sendAndroidPush($pushDataArr);
        return $status;
    }

    /**
     * @function set_ios_push
     * @description Sending push notification on IOS
     *
     * @param String $msg Message to send in notification
     * @param string|int $post_owner_info user id
     * @param array $pushDataArr data required to send push notification
     *
     * @return Boolean TRUE|FALSE
     */
    private function set_ios_push($msg, $post_owner_info, $pushDataArr) {

        $status = false;

        #setting data to send IOS push
        $iosPayload['alert']['title']         = $msg;
        $iosPayload['alert']['body']          = $msg;
        $iosPayload['badge']                  = 0;
        $iosPayload['sound']                  = 'default';
        $iosPayload['mutable-content']        = 1;
        $iosPayload['badge']                  = 1;
        $iosPayload['data']['attachment_url'] = 'https://www.slicktext.com/blog/wp-content/uploads/2015/02/food-pizza-1-600x375.jpg';
        $iosPayload['data']['content_type']   = 'image';

        $pushDataArr['payload'] = $iosPayload;

        #push status TRUE or FALSE
        $status = sendIosPush($pushDataArr);

        return $status;
    }

}
