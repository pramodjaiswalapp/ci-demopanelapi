<?php

class Common_model extends CI_Model {

    public $finalrole = array();

    public function __construct() {
        $this->load->database();
        $this->load->library('session');
    }

    /**
     * Fetch data from any table based on different conditions
     *
     * @access	public
     * @param	string
     * @param	string
     * @param	array
     * @return	bool
     */
    public function fetch_data($table, $fields = '*', $conditions = array(), $returnRow = false) {
        //Preparing query
        $this->db->select($fields);
        $this->db->from($table);

        //If there are conditions
        if (count($conditions) > 0) {
            $this->condition_handler($conditions);
        }
        $query = $this->db->get();
        //Return
        return $returnRow ? $query->row_array() : $query->result_array();
    }

    /**
     * Insert data in DB
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	string
     * @return	string
     */
    public function insert_single($table, $data = array()) {
        //Check if any data to insert
        if (count($data) < 1) {
            return false;
        }

        $this->db->insert($table, $data);
        //echo $this->db->last_query();die;

        return $this->db->insert_id();
    }

    /**
     * Insert batch data
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	array
     * @param	bool
     * @return	bool
     */
    public function insert_batch($table, $defaultArray, $dynamicArray = array(), $updatedTime = false) {
        //Check if default array has values
        if (count($dynamicArray) < 1) {
            return false;
        }

        //If updatedTime is true
        if ($updatedTime) {
            $defaultArray['UpdatedTime'] = time();
        }

        //Iterate it
        foreach ($dynamicArray as $val) {
            $updates[] = array_merge($defaultArray, $val);
        }
        return $this->db->insert_batch($table, $updates);
    }

    /**
     * Delete data from DB
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	string
     * @return	string
     */
    public function delete_data($table, $conditions = array()) {
        //If there are conditions
        if (count($conditions) > 0) {
            $this->condition_handler($conditions);
        }
        return $this->db->delete($table);
    }

    /**
     * Handle different conditions of query
     *
     * @access	public
     * @param	array
     * @return	bool
     */
    private function condition_handler($conditions) {


        //Custom Where
        if (array_key_exists('customWhere', $conditions)) {
            $this->db->where($conditions['customWhere']);
        }
        //Where
        if (array_key_exists('where', $conditions)) {

            //Iterate all where's
            foreach ($conditions['where'] as $key => $val) {
                $this->db->where($key, $val);
            }
        }

        //Where OR
        if (array_key_exists('or_where', $conditions)) {

            //Iterate all where or's
            foreach ($conditions['or_where'] as $key => $val) {
                $this->db->or_where($key, $val);
            }
        }

        //Where In
        if (array_key_exists('where_in', $conditions)) {

            //Iterate all where in's
            foreach ($conditions['where_in'] as $key => $val) {
                $this->db->where_in($key, $val);
            }
        }

        //Where Not In
        if (array_key_exists('where_not_in', $conditions)) {

            //Iterate all where in's
            foreach ($conditions['where_not_in'] as $key => $val) {
                $this->db->where_not_in($key, $val);
            }
        }

        //Having
        if (array_key_exists('having', $conditions)) {
            $this->db->having($conditions['having']);
        }

        //Group By
        if (array_key_exists('group_by', $conditions)) {
            $this->db->group_by($conditions['group_by']);
        }

        //Order By
        if (array_key_exists('order_by', $conditions)) {

            //Iterate all order by's
            foreach ($conditions['order_by'] as $key => $val) {
                $this->db->order_by($key, $val);
            }
        }

        //Order By
        if (array_key_exists('like', $conditions)) {

            //Iterate all likes
            foreach ($conditions['like'] as $key => $val) {
                $this->db->like($key, $val);
            }
        }

        //Limit
        if (array_key_exists('limit', $conditions)) {

            //If offset is there too?
            if (count($conditions['limit']) == 1) {
                $this->db->limit($conditions['limit'][0]);
            } else {
                $this->db->limit($conditions['limit'][0], $conditions['limit'][1]);
            }
        }
    }

    /**
     * Update Batch
     *
     * @access	public
     * @param	string
     * @param	array
     * @return	boolean
     */
    public function update_batch_data($table, $defaultArray, $dynamicArray = array(), $key) {
        //Check if any data
        if (count($dynamicArray) < 1) {
            return false;
        }

        //Prepare data for insertion
        foreach ($dynamicArray as $val) {
            $data[] = array_merge($defaultArray, $val);
        }
        return $this->db->update_batch($table, $data, $key);
    }

    /**
     * Update details in DB
     *
     * @access	public
     * @param	string
     * @param	array
     * @param	array
     * @return	string
     */
    public function update_single($table, $updates, $conditions = array()) {
        //If there are conditions
        if (count($conditions) > 0) {
            $this->condition_handler($conditions);
        }
        return $this->db->update($table, $updates);
    }

    public function update_single_withcurrent($table, $updates, $conditions = array()) {

        //If there are conditions
        if (count($conditions) > 0) {
            $this->condition_handler($conditions);
        }
        $this->db->set($updates['field'], $updates['value'], FALSE);
        return $this->db->update($table);
    }

    public function updateTableData($data, $tableName, $where) {
        $this->db->set($data);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        if (!$this->db->update($tableName)) {
            throw new Exception("Update error");
        } else {
            return true;
        }
    }

    /**
     * Count all records
     *
     * @access	public
     * @param	string
     * @return	array
     */
    public function fetch_count($table, $conditions = array()) {
        $this->db->from($table);
        //If there are conditions
        if (count($conditions) > 0) {
            $this->condition_handler($conditions);
        }
        return $this->db->count_all_results();
    }

    public function sendmailnew($email, $subject, $message = false, $single = true, $param = false, $templet = false) {
        if ($single == true) {
            $this->load->library('email');
        }

        $this->config->load('email');
        $this->email->from($this->config->item('from'), $this->config->item('from_name'));
        $this->email->reply_to($this->config->item('repy_to'), $this->config->item('reply_to_name'));
        $this->email->to($email);
        $this->email->subject($subject);
        if ($param && $templet) {
            $body = $this->load->view('mail/' . $templet, $param, TRUE);
            $this->email->message($body);
        } else {
            $this->email->message($message);
        }
        return $this->email->send() ? true : false;
    }

    public function randomstring($length) {
        return $a = mt_rand(1000, 9999);
    }

    //to validate email
    public function validate_email($e) {
        return (bool) preg_match("`^[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_\`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$`i", trim($e));
    }

    /**
     * @function        getUserInfoByTable
     * @author          Pramod
     * @description     get user details
     * @param           $where
     * @data            18-11-2016
     * @return          boolean
     */
    public function getUserInfoByTable($table, $Id, $idColumn) {
        $this->db->select('t.userId,t.fullName,t.email,u.deviceType,u.deviceToken,u.notificationSetting')
                ->from($table . ' as t')
                ->join('user as u', 'u.userId = t.userId', 'LEFT');


        if ($Id) {
            $this->db->where('t.' . $idColumn, $Id);
        }

        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * @name  fetch_using_join
     * @description fetch data from join
     * @param string $select
     * @param string $from
     * @param string $joinCondition
     * @param string $joinType
     * @param string $where
     * @return arrray
     */
    public function fetch_using_join($select, $from, $join, $where, $asArray = NULL, $offset = NULL, $orderBy = NULL) {

        $this->db->select($select, FALSE);
        $this->db->from($from);
        for ($i = 0; $i < count($join); $i++) {
            $this->db->join($join[$i]["table"], $join[$i]["condition"], $join[$i]["type"]);
        }
        $this->db->where($where);
        if (isset($orderBy['order']) && $orderBy !== NULL) {
            $this->db->order_by($orderBy["order"], $orderBy["sort"]);
        }

        if ($offset !== NULL) {
            $this->db->limit(PAGINATION_LIMIT, $offset);
        }
        $query = $this->db->get();
        return ($asArray !== NULL) ? $query->row() : $query->result_array();
    }

    /**
     * @name rawquery
     * @access public
     * @description  Performs raw query. Optionally gives in array or object format
     * @return array/object
     */
    public function rawquery($data, $resultArray = NULL) {
        $query = $this->db->query($data);
        return ($resultArray !== NULL) ? $query->result_array() : $query->row();
    }

    /**
     * @name uploadfile
     * @param type $filename
     * @param type $filearr
     * @param type $restype
     * @param type $foldername
     * @return boolean
     */
    public function uploadfile($filename = '', $filearr, $restype = 'name', $foldername = '', $allowedType = NULL) {

        if (!is_dir(COMMON_UPLOAD_PATH . '/' . $foldername)) {
            mkdir(COMMON_UPLOAD_PATH . '/' . $foldername);
            chmod(COMMON_UPLOAD_PATH . '/' . $foldername, 0755);
        }

        if ($filearr[$filename]['name'] != '') {
            $config['upload_path'] = COMMON_UPLOAD_PATH . $foldername;
            if (!empty($allowedType)) {
                $config['allowed_types'] = $allowedType;
            } else {
                $config['allowed_types'] = '*';
            }
            $new_name            = date('Y/m/d') . '_' . time() . '_' . $filearr[$filename]['name'];
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload($filename)) {
                $res = $this->upload->data();
                if ($restype == 'name') {
                    unset($foldername);
                    return $res['file_name'];
                } elseif ($restype == 'url') {
                    return COMMON_FILE_URL . $foldername . '/' . $res['file_name'];
                }
            } else {
                return false;
            }
        }
    }

    /**
     * @name  insertAll
     * @description function for insert_batch
     * @param string $table
     * @param array $data
     * @return boolean
     */
    public function insertAll($table, $data) {

        return $this->db->insert_batch($table, $data);
    }

    public function removeSpace($str) {

        return str_replace(' ', '', $str);
    }

}
