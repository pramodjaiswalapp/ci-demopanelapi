<?php

 class Subadmin_model extends CI_Model {

     //check wheater the email is exit in the database or not
     function is_email_available( $email ) {
         $this->db->where( 'email', $email );
         $query = $this->db->get( "oe_admins" );
         if ( $query->num_rows() > 0 ) {
             return true;
         }
         else {
             return false;
         }

     }



     /**
      * @function getsubadmindata
      * @description fetch the data according to search the data of subadmin according to admin search
      *
      * @param int $offset To set offset in MySql Query. E.g : select * from xxxx limit offset, limit
      * @param int $limit To set number of Rows at a time
      * @param array $params An array of parameters to filter out CMS content list
      * @return array $res An array of fetched result
      */
     public function getsubadmindata( $limit, $offset, $params ) {
         $sortMap = [
             "added" => "create_date",
             "name"  => "admin_name",
             "email" => "admin_email"
         ];


         $this->db->select( 'SQL_CALC_FOUND_ROWS a.admin_id,admin_email,admin_name,admin_profile_pic,create_date,status', false );
         $this->db->from( 'admin as a' );
         $this->db->where( 'role_id', 2 );

         if ( !empty( $params['searchlike'] ) ) {
             $this->db->group_start();
             $this->db->like( 'a.admin_name', $params['searchlike'], 'after' );
             $this->db->or_like( 'a.admin_name', ' '.$params['searchlike'] );
             $this->db->or_like( 'a.admin_email', $params['searchlike'], 'after' );
             $this->db->group_end();
         }

         /**/
         if ( (isset( $params["field"] ) && !empty( $params["field"] ) && in_array( $params["field"], array_keys( $sortMap ) ) ) &&
             (isset( $params["order_by"] ) && !empty( $params["order_by"] )) ) {
             $this->db->order_by( $sortMap[$params["field"]], $params["order_by"] );
         }
         else {
             $this->db->order_by( "a.admin_name", "ASC" );
         }
         /**/
         $this->db->where( 'status != 3' );

         $this->db->limit( $limit, $offset );
         $query = $this->db->get();

         $respdata              = array ();
         $respdata['totalrows'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         $respdata['records']   = $query->result_array();
         return $respdata;

     }



     /**
      * @function delete_data
      * @description delete the records
      *
      * @param int $userId id to delete a particular record
      */
     public function delete_data( $userId ) {
         $this->db->where( 'userId', $userId );
         $this->db->delete( 'oe_admins' );

     }



     /**
      * @function update
      * @description update the records
      *
      * @param string $table table name to perform update action
      * @param type $data
      * @param type $where
      * @return boolean
      */
     public function update( $table, $data, $where ) {
         $this->db->where( $where );
         $this->db->update( $table, $data );
         if ( $this->db->affected_rows() > 0 ) {
             return true;
         }
         else {
             return false;
         }

     }



     /**
      * @function insertpermission
      * @description insert the records if not exit
      *
      * @param array $updateArr data to save in DB
      * @param type $where
      * @return boolean
      */
     public function insertpermission( $updateArr, $where ) {
         // print_r($where);die;
         //$sql = "insert into sub_admin (viewp,blockp,editp,deletep,addp,access_permission,admin_Id)values(" . $updateArr['viewp'] . "," . $updateArr['blockp'] . "," . $updateArr['editp'] . "," . $updateArr['deletep'] . "," . $updateArr['addp'] . "," . $updateArr['access_permission'] . "," . $where['admin_Id'] . ")ON DUPLICATE KEY UPDATE viewp='" . $updateArr['viewp'] . "',blockp='" . $updateArr['blockp'] . "',editp='" . $updateArr['editp'] . "',deletep='" . $updateArr['deletep'] . "',addp='" . $updateArr['addp'] . "' where access_permission = ".$updateArr['access_permission']." AND admin_Id = ".$where['admin_Id']."";
         $sql           = "insert into sub_admin (viewp,blockp,editp,deletep,addp,access_permission,admin_Id)values(".$updateArr['viewp'].",".$updateArr['blockp'].",".$updateArr['editp'].",".$updateArr['deletep'].",".$updateArr['addp'].",".$updateArr['access_permission'].",".$where['admin_Id'].")";
         $query         = $this->db->query( $sql );
         $afftectedRows = $this->db->affected_rows();
         if ( $query->$afftectedRows == 1 ) {
             return $query->result_array();
         }
         else {
             return false;
         }

     }



 }

?>
