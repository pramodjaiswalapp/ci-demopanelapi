<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/Rcc_Controller.php';

/**
 * @SWG\Get(path="/Chat_list",
 *   tags={"Chat"},
 *   summary="Chat_list",
 *   description="get chat Threads",
 *   operationId="index_get",
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
 *     name="count",
 *     in="query",
 *     description="initially count=0 after that count=next_count",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Response(response=501, description="Parameter missing"),
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=206, description="Unauthorized request"),
 *   @SWG\Response(response=207, description="Header is missing"),
 *   @SWG\Response(response=307, description="No record found"),
 * )
 */

/**
 * Chat list
 *
 * @package RCC
 * @subpackage Api
 * @category chat
 */
class Chat_list extends Rcc_Controller {

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

        /**
         * authenticate user
         */
        $this->login_user = $this->authenticate_user();
    }

    /**
     * index
     * @method get
     */
    public function index_get() {
        try {

            $get = $this->get();

            if (!isset($get) && empty($get) || !is_array($get)) {

                $this->response(array("CODE"    => MISSING_PARAMETER,
                    "MESSAGE" => $this->lang->line('Parameter_missing')));
            }

            if (!isset($get['count']) || !is_numeric($get['count'])) {
                $this->response(array("CODE"    => MISSING_PARAMETER,
                    "MESSAGE" => $this->lang->line('Parameter_missing')));
            }
            /**
             * get chat list
             */
            $this->get_chat_list($get['count']);
        } catch (Exception $ex) {
            /**
             * log exception
             */
            log_message("error", $ex->getMessage());
            /**
             * response exception
             */
            $this->response(["CODE" => $ex->getCode(), "MESSAGE" => $ex->getMessage()]);
        }
    }

    /**
     * get chat list of logged in user
     * @param int $count
     */
    private function get_chat_list($count) {
        try {
            /**
             * limit =20
             */
//            $limit = QUERY_OFFSET;
            $where['limit']  = 20;
            /**
             * set offset
             */
            $where['offset'] = isset($count) ? $count : 0;

            /**
             * next count
             */
            $this->next_count = $where['offset'] + $where['limit'];

            $all_data    = $this->chat->get_user_chatlist($this->login_user['user_id'], $where);
            /**
             * result set
             */
            $list        = $all_data['list'];
            /**
             * total record
             */
            $total_count = $all_data['totalcount'];

            /**
             * check is data remaining or not
             */
            if ($total_count <= $this->next_count) {
                $this->next_count = '-1';
            }

            if (isset($list) && !empty($list)) {

                $this->response(array('CODE' => SUCCESS_CODE, 'MESSAGE' => 'success', 'RESULT' => $list, 'NEXT' => $this->next_count, 'TOTAL' => $total_count));
            } else {

                $this->response(array("CODE" => RECORD_NOT_EXISTS, 'MESSAGE' => $this->lang->line('NO_RECORD_FOUND'), 'RESULT' => array()));
            }
        } catch (Exception $ex) {
            /**
             * log exception
             */
            log_message('error', $ex->getMessage());
            /**
             * response exception
             */
            $this->response(["CODE" => $ex->getCode(), "MESSAGE" => $ex->getMessage()]);
        }
    }

    /**
     * destructor
     */
    function __destruct() {
        parent::__destruct();

        unset($this->chat);

        unset($this->login_user);

        unset($this->private);
        unset($this->public);
    }

}
