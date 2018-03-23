<?php

class Review_model extends CI_Model {

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
    public function getReviews($params) {

        $this->db->select('SQL_CALC_FOUND_ROWS id as review_id,IF(r.user_id=' . $params['user_id'] . ',1,0) as is_reviewed,CONCAT(first_name," ",last_name) as name,r.review,r.rating',false);
        $this->db->from('ai_reviews as r');
        $this->db->join('ai_user as u', 'u.user_id = r.user_id', 'left');
        $this->db->where('post_id', $params['post_id']);
        $this->db->limit($params['limit'], $params['offset']);
        $query = $this->db->get();
        $resArr = [];
        $resArr['result'] = $query->result_array();
        $resArr['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $resArr;
    }

}
