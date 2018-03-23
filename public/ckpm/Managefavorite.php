<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Managefavorite extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Common_model');
        $this->load->model('Favorite_model');
        $this->load->library('form_validation');
    }

    /**
     * @SWG\Post(path="/managefavorite",
     *   tags={"Favorite"},
     *   summary="Make a post favorite",
     *   description="Make a post favorite",
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
     *  @SWG\Parameter(
     *     name="accesstoken",
     *     in="query",
     *     description="Access token received during signup or login",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     description="User Id",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=206, description="Unauthorized request"),     
     *   @SWG\Response(response=207, description="Header is missing"),       
     *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
     *   @SWG\Response(response=507, description="Already favorite")
     * )
     */
    public function index_post() {

        $postDataArr = $this->post();
        $config = [];

        $config = array(
            array(
                'field' => 'accesstoken',
                'label' => 'Access Token',
                'rules' => 'required'
            ),
            array(
                'field' => 'user_id',
                'label' => 'User Id',
                'rules' => 'required'
            )
        );

        $this->form_validation->set_rules($config);
        /*
         * Setting Error Messages for rules
         */
        $this->form_validation->set_message('required', 'Please enter the %s');

        if ($this->form_validation->run()) {
            try {
                $user_id = $this->getUserId($postDataArr['accesstoken']);
                $whereArr = [];
                $whereArr['where'] = ['user_id' => $user_id, 'favorited_userid' => $postDataArr['user_id']];
                $isRequestExist = $this->Common_model->fetch_data('ai_favorite', ['id'], $whereArr, true);
                if (empty($isRequestExist)) {
                    /*
                     * Request Array
                     */
                    $favoriteInsertArr = [];
                    $favoriteInsertArr['user_id'] = $user_id;
                    $favoriteInsertArr['favorited_userid'] = $postDataArr['user_id'];
                    $favoriteInsertArr['created_at'] = datetime();
                    $isRequestSuccess = $this->Common_model->insert_single('ai_favorite', $favoriteInsertArr);

                    if ($isRequestSuccess) {
                        $this->response(array('code' => SUCCESS_CODE, 'msg' => $this->lang->line('favorite_success'), 'result' => []));
                    } else {
                        $this->response(array('code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line('try_again'), 'result' => []));
                    }
                } else {
                    $this->response(array('code' => ALREADY_FAVORITE, 'msg' => $this->lang->line('already_favorite'), 'result' => []));
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                list($msg, $code) = explode(" || ", $error);
                $this->response(array('code' => $code, 'msg' => $msg, 'result' => []));
            }
        } else {
            $err = $this->form_validation->error_array();
            $arr = array_values($err);
            $this->response(array('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []));
        }
    }

    /**
     * @SWG\Get(path="/managefavorite",
     *   tags={"Comments"},
     *   summary="View the comments of a post",
     *   description="View the comments of a post",
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
     *     name="accesstoken",
     *     in="query",
     *     description="Access token received during signup or login",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     description="User Id",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="page no.",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="searchlike",
     *     in="query",
     *     description="Search key parameter",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="req_type",
     *     in="query",
     *     description="request type 1 for getting list of users",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=201, description="Please try again"),       
     *   @SWG\Response(response=202, description="No data found"), 
     *   @SWG\Response(response=206, description="Unauthorized request"),     
     *   @SWG\Response(response=207, description="Header is missing"),             
     *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
     * )
     */
    public function index_get() {

        $getDataArr = $this->input->get();

        $config = [];
        /*
         * Req type 1 pending request,2 sent pending request and empty for getting friends list
         */
        $config = array(
            array(
                'field' => 'accesstoken',
                'label' => 'Access Token',
                'rules' => 'required'
            )
        );

        $set_data = array(
            'accesstoken' => $this->input->get('accesstoken'),
            'user_id' => $this->input->get('user_id'),
        );

        $this->form_validation->set_data($set_data);
        $this->form_validation->set_rules($config);
        /*
         * Setting Error Messages for rules
         */
        $this->form_validation->set_message('required', 'Please enter the %s');

        if ($this->form_validation->run()) {
            try {
                /*
                 * Get user id with public and private key
                 */
                $user_id = $this->getUserId($getDataArr['accesstoken']);
                $page = isset($getDataArr['page']) ? $getDataArr['page'] : 1;
                $req_type = isset($getDataArr['req_type']) ? $getDataArr['req_type'] : "";
                $limit = 20;
                $offset = ($page - 1) * $limit;
                $params = [];
                $params['user_id'] = $user_id;
                $params['limit'] = $limit;
                $params['offset'] = $offset;

                if ($req_type == 1) {
                    $params['userlist_type'] = 3;
                    $usersList = $this->Common_model->getUserList($params);
                } else {
                    $this->Favorite_model->getFavorites($params);
                }

                /*
                 * fetching recieved pending requests
                 */
                if (($usersList['count'] > ($page * $limit))) {
                    $page++;
                } else {
                    $page = 0;
                }
                if (!empty($usersList['result'])) {
                    $this->response(array('code' => SUCCESS_CODE, 'msg' => $this->lang->line('user_list_fetched'), 'next_page' => $page, 'total_rows' => $usersList['count'], 'result' => $usersList['result']));
                } else {
                    $this->response(array('code' => NO_DATA_FOUND, 'msg' => $this->lang->line('no_users_found'), 'result' => []));
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                list($msg, $code) = explode(" || ", $error);
                $this->response(array('code' => $code, 'msg' => $msg, 'result' => []));
            }
        } else {
            $err = $this->form_validation->error_array();
            $arr = array_values($err);
            $this->response(array('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []));
        }
    }

    /**
     * @SWG\Delete(path="/managefavorite",
     *   tags={"Comments"},
     *   summary="Unfavorite the post",
     *   description="Unfavorite the post",
     *   operationId="index_delete",
     *   consumes ={"multipart/form-data"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="accesstoken",
     *     in="query",
     *     description="Access token received during signup or login",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     description="Comment Id of Comment which we want to delete",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=206, description="Unauthorized request"),     
     *   @SWG\Response(response=207, description="Header is missing"),       
     *   @SWG\Response(response=418, description="Required Parameter Missing or Invalid"),
     * )
     */
    public function index_delete() {
        $deleteDataArr = $this->delete();
        $config = [];
        /*
         * Req type 1 if wants to reject the request 2 if wants to cancel pending request
         */
        $config = array(
            array(
                'field' => 'accesstoken',
                'label' => 'Access Token',
                'rules' => 'required'
            ),
            array(
                'field' => 'user_id',
                'label' => 'User Id',
                'rules' => 'required'
            )
        );

        $set_data = array(
            'user_id' => $this->delete('user_id'),
            'accesstoken' => $this->delete('accesstoken')
        );

        $this->form_validation->set_data($set_data);
        $this->form_validation->set_rules($config);
        /*
         * Setting Error Messages for rules
         */
        $this->form_validation->set_message('required', 'Please enter the %s');

        if ($this->form_validation->run()) {
            try {

                $user_id = $this->getUserId($deleteDataArr['accesstoken']);
                $whereArr = [];
                $whereArr['where'] = ['favorited_userid' => $deleteDataArr['user_id'], 'user_id' => $user_id];
                $isDeleteSuccess = $this->Common_model->delete_data('ai_favorite', $whereArr);

                if ($isDeleteSuccess) {
                    $this->response(array('code' => SUCCESS_CODE, 'msg' => $this->lang->line('unfavorite_success'), 'result' => []));
                } else {
                    $this->response(array('code' => TRY_AGAIN_CODE, 'msg' => $this->lang->line('try_again'), 'result' => []));
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                list($msg, $code) = explode(" || ", $error);
                $this->response(array('code' => $code, 'msg' => $msg, 'result' => []));
            }
        } else {
            $err = $this->form_validation->error_array();
            $arr = array_values($err);
            $this->response(array('code' => PARAM_REQ, 'msg' => $arr[0], 'result' => []));
        }
    }

    private function getUserId($accessToken) {
        $accessTokenArr = explode("||", $accessToken);
        if (count($accessTokenArr) != 2) {
            $this->response(array('code' => INVALID_ACCESS_TOKEN, 'msg' => $this->lang->line('invalid_access_token'), 'result' => []));
        }
        $whereArr = [];
        $whereArr['where'] = ['public_key' => $accessTokenArr[0], 'private_key' => $accessTokenArr[1], 'login_status' => 1];
        $userInfo = $this->Common_model->fetch_data('ai_session', ['user_id'], $whereArr, true);
        if (!empty($userInfo)) {
            return $userInfo['user_id'];
        } else {
            $this->response(array('code' => ACCESS_TOKEN_EXPIRED, 'msg' => $this->lang->line('access_token_expired'), 'result' => []));
        }
    }

}
