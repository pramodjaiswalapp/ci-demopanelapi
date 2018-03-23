<?php

class Comment_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        /**
         * load database
         */
        $this->load->database();
    }

    /**
     * get review list
     * @param array
     * @return array
     */
    public function getComments($params) {

        $this->db->select('SQL_CALC_FOUND_ROWS id as comment_id,IF(c.user_id=' . $params['user_id'] . ',1,0) as is_commented,CONCAT(first_name," ",last_name) as name,c.comment',false);
        $this->db->from('ai_comments as c');
        $this->db->join('ai_user as u', 'u.user_id = c.user_id', 'left');
        $this->db->where('post_id', $params['post_id']);
        $this->db->limit($params['limit'], $params['offset']);
        $query = $this->db->get();
        $resArr = [];
        $resArr['result'] = $query->result_array();
        $resArr['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $resArr;
    }

}
