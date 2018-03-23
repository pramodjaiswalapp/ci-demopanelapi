<?php

class Favorite_model extends CI_Model {

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
    public function getFavorites($params) {

        $this->db->select('SQL_CALC_FOUND_ROWS u.user_id,CONCAT(first_name," ",last_name) as name',false);
        $this->db->from('ai_favorite as f');
        $this->db->join('ai_user as u', '(u.user_id = f.favorited_userid AND f.user_id='.$params['user_id'].')', 'left');
        $this->db->where('f.user_id =  '.$params['user_id'].'');
        $this->db->limit($params['limit'], $params['offset']);
        $query = $this->db->get();
//        echo $this->db->last_query();die;
        $resArr = [];
        $resArr['result'] = $query->result_array();
        $resArr['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $resArr;
    }

}
