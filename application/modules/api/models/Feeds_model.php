<?php

class Feeds_model extends CI_Model {

    public $totalmsg;

    public function __construct() {
        $this->load->database();
    }

    public function getFeeds($params) {
        $postfield = "post_description,is_checked,checkin_lat,checkin_long,like_count,comment_count";
        $mediafield = "GROUP_CONCAT(url) as url,GROUP_CONCAT(media_type) as media_type";
        $mediatags = "GROUP_CONCAT(coordinates) as url,GROUP_CONCAT(media_type) as media_type";
        $this->db->select("SQL_CALC_FOUND_ROWS " . $postfield . "," . $mediafield, false);
        $this->db->from('ai_post as p');
        $this->db->join('post_media as pm', 'p.post_id=pm.post_id', 'left');
        $this->db->join('post_image_tags as imgtags', 'pm.id=imgtags.media_id', 'left');
        $this->db->group_by(["p.post_id"]);
        $query = $this->db->get();
//        echo $this->db->last_query();
//        die;
        $resArr = [];
        $resArr['feeds'] = $query->result_array();
        $resArr['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $resArr;
    }

}
