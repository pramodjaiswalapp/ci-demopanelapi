<?php

class Merchant_Model extends CI_Model {

    public $finalrole = array();

    public function __construct() {
        $this->load->database();
        $this->load->library('session');
    }
 /*After Login detail for customer/Merchant*/
    public function fetchuserdetal($where){
      $this->db->select("u.id,u.email,u.user_type,u.latitude,u.longitude,u.mobile_number,u.profile_picture,u.user_status,udd.device_notification_status as notification_status");  
      $this->db->from('users as u');
      $this->db->join('user_device_details as udd', 'udd.device_user_id=u.id', 'left');  
      $this->db->where($where);
       $query = $this->db->get();
//      echo $this->db->last_query();die;
        return $query->result_array();  
    }
    
    /*fetch nearby restaurent for customer with its deals*/
        public function Get_merchant($latitude, $longitude, $limit,$offset,$where) {
  $this->db->select("SQL_CALC_FOUND_ROWS u.*,GetDeals(u.id) as deals"
       . ",GeoDistDiff('km', u.latitude,u.longitude, {$latitude}, {$longitude}) as distance_km",FALSE);
        $this->db->from('users as u');
        $this->db->join('deals as d',"u.id=d.created_by", 'right');
        $this->db->where($where);
//        $this->db->group_by("");

        if ((int) $limit >= 0 && (int) $offset >= 0) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
      echo $this->db->last_query();die;
        $res['result'] = $query->result();
        
        
        $res['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $res;
    }
    
      /**
      * groups double pipe "||" separated tasks detail and converts them into array of JSON objects
      * used for handling additional jobs list
      * @param array $userData User data array
      * @return array 
      */
    private function categoryGroupHandler($categoryData)
    {
        foreach( $categoryData as $key => $value ) {    
            $categoryData[$key]["sub_category_data"] = [];
            $subCategoryName = explode("||", $value["sub_category_name"]);
            $subCategoryImage = explode("||", $value["sub_category_image"]);
            $subCategoryId = explode("||", $value["sub_category_id"]);
            if ( !empty(array_filter($subCategoryId)) ) {
                foreach($subCategoryId as $keyInner => $valueInner) {
                    $categoryData[$key]["sub_category_data"][$keyInner]["sub_category_id"] = $valueInner;
                    $categoryData[$key]["sub_category_data"][$keyInner]["sub_category_image"] = $subCategoryImage[$keyInner];
                    $categoryData[$key]["sub_category_data"][$keyInner]["sub_category_name"] = $subCategoryName[$keyInner];
                } 
            }
            unset($categoryData[$key]["sub_category_name"]);
            unset($categoryData[$key]["sub_category_image"]);
            unset($categoryData[$key]["sub_category_id"]);
        }
        return $categoryData;
    }
}
 