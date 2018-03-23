<?php

 class Chat_model extends CI_Model {

     public $totalmsg;

     public function __construct() {
         $this->load->database();

     }



     /**
      *
      * @param type $limit
      * @param type $offset
      * @param type $params
      * @return Array Getting user details
      */
     public function getuserlist( $limit, $offset, $params ) {
         $this->db->select( 'SQL_CALC_FOUND_ROWS user_id, middle_name,first_name,last_name, image', FALSE )
             ->from( 'ai_user as u' );
         if ( !empty( $params['searchlike'] ) ) {
             $this->db->like( 'u.middle_name', $params['searchlike'] );
         }
         $this->db->limit( $limit, $offset );
         $query          = $this->db->get();
         $err            = $this->db->error();
         $this->totalmsg = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         if ( isset( $err['code'] ) && $err['code'] != 0 ) {
             throw new Exception( $this->lang->line( 'somthing_went_wrong' ) );
         }
         else {

             return $query->result_array();
         }

     }



     /**
      *
      * @param type from_user_id , user_id , chat_created_time
      * @return Array Description chat insert in database
      */
     public function insert_history( $params ) {
         $sql    = "insert into  ai_chat_history (to_user_id,from_user_id,created_date) values(".$params['to_user_id'].",".$params['from_user_id'].",'".$params['chat_created_time']."') ON DUPLICATE KEY UPDATE is_archived = 0,created_date = '".$params['chat_created_time']."'";
         $result = $this->db->query( $sql );
         $sql    = "insert into  ai_chat_history (to_user_id,from_user_id,created_date) values(".$params['from_user_id'].",".$params['to_user_id'].",'".$params['chat_created_time']."') ON DUPLICATE KEY UPDATE is_archived = 0,created_date = '".$params['chat_created_time']."'";
         $result = $this->db->query( $sql );

     }



     /**
      *
      * @param type $limit
      * @param type $offset
      * @param type from_user_id , user_id
      * @return Array Get chat history between two user
      */
     public function fetch_history( $limit, $offset, $params ) {
         $this->db->select( 'SQL_CALC_FOUND_ROWS getmessage(Max(msg_id)) as msg,getunreadcount('.$params['user_id'].',u.user_id) as unread_count,getchattype(Max(msg_id)) as chat_type,Max(msg_id) as msg_id,c.to_user_id,c.from_user_id, middle_name,first_name,last_name, image,u.user_id,getrecentmsgtime(Max(msg_id)) as last_message_time',
                            false );
         $this->db->from( 'ai_chat as c' );
         $this->db->join( 'ai_user as u', '(IF(c.to_user_id = '.$params['user_id'].',c.from_user_id,c.to_user_id) = u.user_id)', 'left', false );
         $this->db->join( 'ai_chat_history as ch', '((u.user_id = ch.from_user_id) AND (ch.to_user_id = '.$params['user_id'].'))', 'left', false );
         $this->db->where( '(c.to_user_id = '.$params['user_id'].' OR c.from_user_id= '.$params['user_id'].')' );
         $this->db->where( 'NOT deleted_user_id like("%'.$params['user_id'].'%")' );
         $this->db->group_by( 'GREATEST(c.to_user_id, c.from_user_id), LEAST(c.to_user_id, c.from_user_id)', false );
         $this->db->order_by( 'msg_id DESC' );
         $this->db->limit( $limit, $offset );
         $query          = $this->db->get();
         $err            = $this->db->error();
         $this->totalmsg = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;
         if ( isset( $err['code'] ) && $err['code'] != 0 ) {
             throw new Exception( $this->lang->line( 'somthing_went_wrong' ) );
         }
         else {

             return array_reverse( $query->result_array() );
         }

     }



     /**
      *
      * @param type $limit
      * @param type $offset
      * @param type from_user_id , user_id
      * @return Array Get chat list between two user
      */
     public function fetch_chat_detail( $limit, $offset, $params ) {
         $this->db->select( 'SQL_CALC_FOUND_ROWS IF(to_user_id = '.$params['user_id'].',"1","2") as type,getreadstatus('.$params['from_user_id'].',c.msg_id) as is_read,u.user_id,c.msg,c.msg_id,c.chat_type,to_user_id,from_user_id,u.middle_name,u.first_name,u.last_name,c.media_url,c.media_thumb,c.chat_time as message_time',
                            false );
         $this->db->from( 'ai_chat as c' );
         $this->db->join( 'ai_user as u', '(IF(c.to_user_id = '.$params['user_id'].',c.from_user_id,c.to_user_id) = u.user_id)', 'left', false );
         $this->db->where( '((c.to_user_id = '.$params['user_id'].' AND c.from_user_id = '.$params['from_user_id'].') OR (c.to_user_id = '.$params['from_user_id'].' AND c.from_user_id = '.$params['user_id'].'))' );
         $this->db->where( 'NOT deleted_user_id like("%'.$params['user_id'].'%")' );
         if ( !empty( $params['last_msg_time'] ) ) {
             $this->db->where( 'c.chat_time > '.$params['last_msg_time'].'' );
             //$this->db->where('(UNIX_TIMESTAMP(CAST(c.chat_created_time AS DATETIME))*1000) < ' . $params['last_msg_time'] . '');
         }
         $this->db->order_by( 'c.chat_created_time desc' );
         $this->db->group_by( 'msg_id' );
         if ( empty( $params['last_msg_time'] ) ) {
             $this->db->limit( $limit, $offset );
         }
         else {
             $this->db->limit( $limit );
         }
         $query          = $this->db->get();
         $err            = $this->db->error();
         $this->totalmsg = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;

         if ( isset( $err['code'] ) && $err['code'] != 0 ) {
             throw new Exception( $this->lang->line( 'somthing_went_wrong' ) );
         }
         else {

             return array_reverse( $query->result_array() );
         }

     }



     /**
      *
      * @param type from_user_id , user_id
      * @return Array Get chat list between two user 3 secont api
      */
     public function fetch_chatdata( $params ) {
         $this->db->select( 'SQL_CALC_FOUND_ROWS IF(to_user_id = '.$params['to_user_id'].',"1","2") as type,c.msg,getreadstatus('.$params['from_user_id'].',c.msg_id) as is_read,c.msg_id,c.chat_type,to_user_id,from_user_id,c.media_url,c.media_thumb,c.chat_time as message_time',
                            false );
         $this->db->from( 'ai_chat as c' );
         $this->db->where( '((c.to_user_id = '.$params['to_user_id'].' AND c.from_user_id = '.$params['from_user_id'].') OR (c.to_user_id = '.$params['from_user_id'].' AND c.from_user_id = '.$params['to_user_id'].'))' );
         $this->db->where( 'NOT deleted_user_id like("%'.$params['to_user_id'].'%")' );
         if ( !empty( $params['last_msg_time'] ) ) {
             $this->db->where( 'c.chat_time > '.$params['last_msg_time'].'' );
//            $this->db->where('(UNIX_TIMESTAMP(CAST(c.chat_created_time AS DATETIME))*1000) > ' . $params['last_msg_time'] . '');
         }
         else {
             $this->db->limit( '1' );
         }
         $this->db->order_by( 'chat_created_time desc' );
         $query              = $this->db->get();
         $msgarr             = array ();
         $msgarr             = $query->result_array();
         $readstatus         = array ();
         $readstatus         = $this->check_readmsg( $params );
         $data               = array ();
         $data['msg']        = (!empty( $msgarr )) ? $msgarr : array ();
         $data['readstatus'] = isset( $readstatus['is_read'] ) ? $readstatus['is_read'] : "";
         return $data;

     }



     /**
      *
      * @param array $params
      * @Description in chat data list getting read msg
      * @return Array
      */
     public function check_readmsg( $params ) {
         $this->db->select( 'IF(c.read_user_id = '.$params['from_user_id'].' && c.from_user_id = '.$params['to_user_id'].',1,0) as is_read', false );
         $this->db->from( 'ai_chat as c' );
         $this->db->where( 'to_user_id', $params['from_user_id'] );
         $this->db->where( 'from_user_id', $params['to_user_id'] );
         $this->db->order_by( 'chat_created_time desc' );
         $this->db->limit( '1' );
         $query = $this->db->get();
         return $query->row_array();

     }



     /**
      *
      * @param type from_user_id , user_id
      * @return int delete users conversation
      */
     public function delete_all( $params ) {
         $updateddata              = array ();
         $updateddata['is_delete'] = 1;
         $updateddata['user_id']   = $params['user_id'];
         foreach ( $params['from_user_id'] as $from_user_id ) {
             $sql = "UPDATE ai_chat as c set deleted_user_id = (IF(deleted_user_id ='',".$params['user_id'].",concat(deleted_user_id,',".$params['user_id']."'))) where ((c.to_user_id = '".$from_user_id."' AND c.from_user_id = '".$params['user_id']."') OR (c.to_user_id = '".$params['user_id']."' AND c.from_user_id = '".$from_user_id."'))";
             $this->db->query( $sql );
         }

         return 1;

     }



     /**
      *
      * @param type msg_id , user_id
      * @return Array Delete chat
      */
     public function delete_specific( $params ) {
         $updateddata = array ();
         try {
             $sql = "UPDATE ai_chat set deleted_user_id = (IF(deleted_user_id ='',".$params['user_id'].",concat(deleted_user_id,',".$params['user_id']."'))) where msg_id IN(".$params['msg_id'].")";
             return $this->db->query( $sql );
         }
         catch ( Exception $ex ) {
             print_r( $ex.getMessage() );
             die;
         }

     }



     /**
      *
      * @param type from_user_id , user_id
      * Update read status which is fetch
      */
     public function mark_as_read( $params ) {
         try {
             $sql = "UPDATE ai_chat set read_user_id = ".$params['user_id']." where (to_user_id = ".$params['user_id']." AND from_user_id = ".$params['from_user_id'].")";
             return $this->db->query( $sql );
         }
         catch ( Exception $ex ) {
             print_r( $ex.getMessage() );
             die;
         }

     }



     /**
      * get_user_chatdetail
      *
      * @param int $login_user
      * @param int $other_user
      * @param array $where
      * @return array
      */
     public function get_user_chatdetail( $login_user, $other_user, $where, $timestamp = FALSE ) {
         $this->db->select( 'SQL_CALC_FOUND_ROWS c.message_id,c.sender_id,c.receiver_id,c.message_type,c.message_text,c.message_media,c.send_at,c.read_status', FALSE )
             ->from( "rc_chat as c" )
             ->where( "((c.sender_id=$login_user and c.receiver_id=$other_user) OR (c.sender_id=$other_user and c.receiver_id=$login_user))" )
             ->where( "(if(c.sender_id=$login_user,del_by_sender,del_by_receiver)<>1)" )
             ->where( "(c.send_at > LATEST_CLEAR_CHAT($login_user,$other_user))" )
             ->where( "(if(c.sender_id=$login_user,c.del_by_sender,c.del_by_receiver)<>1)" );

         if ( isset( $where['offset'] ) && isset( $where['limit'] ) ) {
             $this->db->limit( $where['limit'], $where['offset'] );
         }

         if ( isset( $timestamp ) && !empty( $timestamp ) ) {
//            $this->db->where("DATE_FORMAT(FROM_UNIXTIME(c.send_at),'%Y-%m-%d %h:%i')>=DATE_FORMAT(NOW(),'%Y-%m-%d %h:%i')");
             $this->db->where( "c.send_at>$timestamp" );
             $this->db->where( "c.read_status=0" );
         }
         $this->db->order_by( 'message_id ASC' );

         $query = $this->db->get();

         $res['list']       = $query->result_array();
         $res['totalcount'] = $this->db->query( 'SELECT FOUND_ROWS() count;' )->row()->count;

         return $res;

     }



 }
