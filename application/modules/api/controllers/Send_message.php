<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


/**
 * @SWG\Post(path="/Send_message",
 *   tags={"Chat"},
 *   summary="Send message",
 *   description="Send chat messages",
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
 *     name="Accesstoken",
 *     in="header",
 *     description="",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Parameter(
 *     name="to_user",
 *     in="formData",
 *     description="other user",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Parameter(
 *     name="message_type",
 *     in="formData",
 *     description="type of message 1: text message, 2: image,3: video,4: image and video ",
 *     required=true,
 *     type="string"
 *   ),
 *    @SWG\Parameter(
 *     name="message_text",
 *     in="formData",
 *     description="text message",
 *      required=true,
 *     type="string"
 *   ),
 *   @SWG\Parameter(
 *     name="message_media",
 *     in="formData",
 *     description="image url if any",
 *     type="string"
 *   ),
 *   @SWG\Response(response=501, description="Parameter missing"),
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=206, description="Unauthorized request"),
 *   @SWG\Response(response=207, description="Header is missing"),
 * )
 */

/**
 * Send message
 *
 * @package RCC
 * @subpackage Api
 * @category chat
 */
class Send_message extends REST_Controller {

    /**
     * current timestamp
     *
     * @var integer
     */
    private $timestamp;

    /**
     * login user id
     *
     * @var integer
     */
    private $login_user;

    /**
     *  chat table name here
     * @var string
     */
    private $chat_table = 'rc_chat';

    /**
     * message type text
     */
    const TEXT_MESSAGE = 1;

    /**
     * image message
     */
    const IMAGE_MESSAGE = 2;

    /**
     * video message
     */
    const VIDEO_MESSAGE = 3;

    /**
     * image with text
     */
    const IMAGE_TEXT_MESSAGE = 4;

    /**
     * device type android
     */
    const ANDROID = 1;

    /**
     * device type iphone
     */
    const IPHONE = 2;

    /**
     * your push notification type here
     */
    const PUSH_TYPE = 1;

    /**
     * constructor
     */
    function __construct() {
        parent::__construct();

        /**
         * load model
         */
        $this->load->model("Common_model", "cm");
        $this->load->model("Chat_model", "chat");

        /**
         * init time
         */
        $this->timestamp = time();
    }

    /**
     * index
     * @method post
     */
    public function index_post() {
        try {
            #get post data
            $post = $this->post();
            #check for post array
            
            #mandatory fields
             
            $mandatory = ["to_user", "message_type"];
            #at least one mandatory
             
            $any_one   = ["message_text", "message_media"];
            #check for mandatory fields
             
            if (isset($mandatory) && !empty($mandatory) &&
                    !$this->check_mandatory($post, $mandatory, $any_one)) {
                $response_array = ['CODE'    => PARAM_REQ,
                                    'MESSAGE' => $this->lang->line('missing_parameter')];
            }
            #send push message
            $this->send_push($post);
            
            #send message
            $this->send_msg($post);

            #send response
            $this->response($response_array);
        }#TRY END 
        catch (Exception $ex) {
            #log exception
            log_message("error", $ex->getMessage());
            
            #response exception
            $response_array = ["CODE" => $ex->getCode(), "MESSAGE" => $ex->getMessage()]; 
            $this->response($response_array);
        }#CATCH END
    }

    /**
     * send message
     * @param type $post
     */
    private function send_msg($post) {
        $this->payload = $this->init_message_array($post);
        try {
            /**
             * insert message record in database
             */
            if (isset($this->payload) && !empty($this->payload) &&
                    $this->payload !== FALSE) {
                /**
                 * insert record in database
                 */
                $last_id = $this->cm->insert_single(
                        $this->chat_table, $this->payload);
            }
            if (isset($last_id) && !empty($last_id)) {
                /**
                 * Current message id
                 */
                $this->payload['message_id'] = $last_id;

                $this->response(["CODE"    => SUCCESS_CODE,
                    "MESSAGE" => $this->lang->line("success"),
                    "Result"  => $this->payload]);
            }
        } catch (Exception $ex) {
            /**
             * log exception
             */
            log_message("error", $ex->getMessage());
            /**
             * response exception
             */
            $this->response(["CODE"    => $ex->getCode(),
                "MESSAGE" => $ex->getMessage()]);
        }
    }

    /**
     *  send chat push notification
     * @param array $param
     */
    private function send_push($param) {
        #get user details and device details
        $details = $this->chat->get_user_device_dedails($param['to_user']);

        if ( !empty($details) ) {

            #alert content for push payload
            $alert_data = $this->get_push_content($param);
            
            #push payload 
            $packet     = [
                'alert' => isset($alert_data) && !empty($alert_data) && $alert_data !== FALSE ? $alert_data : 'message',
                'badge' => 1,
                'sound' => 'default',
                'type'  => self::PUSH_TYPE,
                'time'  => strtotime('now'),
                'name'  => ucfirst($details[0]['first_name']) . ' ' . ucfirst($details[0]['last_name']),
            ];

            foreach ($details as $device) {

                if ($device['platform'] == self::ANDROID):
                    
                    #android FCM push
                    $this->cm->sendFCMNotification($device['device_token'], $packet);

                elseif ($device['platform'] == self::IPHONE) :
                
                #iphone push
                //  $this->cm->sendIphonePushMessage_single($device['device_token'], $packet);
                endif;
            }
        }
    }

    /**
     *  get content for alert data in push payload
     * @param type $data_array
     * @return boolean|string
     */
    private function get_push_content($data_array) {

        switch ($data_array['message_type']) {

            case self::TEXT_MESSAGE:

                $this->alert = $data_array['message_text'];
                break;
            case self::IMAGE_MESSAGE:

                $this->alert = 'image';
                break;
            case self::VIDEO_MESSAGE:

                $this->alert = 'video';
                break;
            case self::IMAGE_TEXT_MESSAGE:

                $this->alert = $data_array['message_text'];
                break;

            default :
                $this->alert = '';
                break;
        }
        if (isset($this->alert) && !empty($this->alert) && is_string($this->alert)) {
            return $this->alert;
        } else {
            return FALSE;
        }
    }

    /**
     * initialize array of data and return
     * @param type $post_data
     * @return boolean|array
     */
    private function init_message_array($post_data) {
        $this->message = [];
        $status = FALSE;
         #initialize message payload for different message types
         switch ($post_data['message_type']) {
            case self::TEXT_MESSAGE:

                $this->message = ["sender_id"    => $this->login_user['user_id'],
                    "receiver_id"  => $post_data['to_user'],
                    "message_text" => $post_data['message_text'],
                    "message_type" => $post_data['message_type'],
                    "send_at"      => $this->timestamp];
                break;
            case self::IMAGE_MESSAGE:

                $this->message = ["sender_id"     => $this->login_user['user_id'],
                    "receiver_id"   => $post_data['to_user'],
                    "message_media" => $post_data['message_media'],
                    "message_type"  => $post_data['message_type'],
                    "send_at"       => $this->timestamp];
                break;
            case self::VIDEO_MESSAGE:

                $this->message = ["sender_id"     => $this->login_user['user_id'],
                    "receiver_id"   => $post_data['to_user'],
                    "message_media" => $post_data['message_media'],
                    "media_thumb"   => $post_data['media_thumb'],
                    "message_type"  => $post_data['message_type'],
                    "send_at"       => $this->timestamp];
                break;
            case self::IMAGE_TEXT_MESSAGE:

                $this->message = ["sender_id"     => $this->login_user['user_id'],
                    "receiver_id"   => $post_data['to_user'],
                    "message_media" => $post_data['message_media'],
                    "media_thumb"   => $post_data['media_thumb'],
                    "message_type"  => $post_data['message_type'],
                    "send_at"       => $this->timestamp];
                break;

            default :
                $this->message = [];
                break;
        }
        if (isset($this->message) && !empty($this->message) && is_array($this->message)) {
            $status =  $this->message;
        } 
        return $status;
    }

    /**
     * destructor
     */
    function __destruct() {
        parent::__destruct();

        unset($this->chat);
        unset($this->alert);
        unset($this->cm);
        unset($this->login_user);
        unset($this->payload);
        unset($this->private);
        unset($this->public);
    }

}
