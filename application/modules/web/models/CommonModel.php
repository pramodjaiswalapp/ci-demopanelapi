<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CommonModel extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->load->library("session");
        $this->load->helper("cookie");
    }

    /**
     * Echos JSON encoded string and exits
     * @param array $data
     *
     */
    public function response($data) {
        echo json_encode($data);
        exit;
    }

    /**
     * Validates given Cookie
     * @param string $cookieName
     * @param string $tableName
     * @param array $sessionFields Session fields
     * @param array $dataFields DB data field
     * @param string $dataFields DB data field
     *
     * @return array|bool returns array if valid cookie
     */
    public function validateCookie($cookieName, $tableName, $sessionFields, $dataFields, $hashingField = "create_date") {
        $loginCookie     = get_cookie($cookieName);
        $tableFields     = implode(",", $dataFields);
        $additionalField = !empty($hashingField) ? "," . $hashingField : "";
        $tableFields     = $tableFields . ",cookie_validator" . $additionalField;
        $cookieData      = $this->getDataFromCookie($loginCookie, $tableName, $tableFields);

        if (!$cookieData) {
            return false;
        }

        unset($cookieData[$hashingField]);

        $userData = array_combine($sessionFields, $cookieData);

        return $userData;
    }

    /**
     * Gets user data from given cookie
     * @access public
     * @param string $cookie
     * @param string $tableName
     * @param string $fields
     * @return bool|array
     */
    public function getDataFromCookie($cookie, $tableName, $fields) {
        if (!isset($cookie) || empty($cookie)) {
            return false;
        }

        $loginCookie = $cookie;
        $loginCookie = explode(":", $loginCookie);

        if (is_array($loginCookie) && count($loginCookie) != 2) {
            return false;
        }

        $cookieData = $this->selectQuery($fields, $tableName, [
            "where" => [
                "cookie_selector" => $loginCookie[0]
            ]
        ]);

        if (!$cookieData) {
            return false;
        }

        $cookieData = $cookieData[0];

        $validator             = $cookieData["cookie_validator"];
        $hashedCookieValidator = hash("sha256", $loginCookie[1] . $cookieData["registered_date"]);

        if ($validator !== $hashedCookieValidator) {
            return false;
        }

        unset($cookieData["cookie_validator"]);

        return $cookieData;
    }

    /**
     * Runs Select query with given options
     *
     * @param mixed $field accept array or string
     * @param string $tableName table name
     * @param array $options other options
     *
     * @return array Data in multidimensional array
     */
    public function selectQuery( $fields, $tableName = "", $options = [] ) {
        if ( is_array( $fields ) ) {
            $this->db->select( implode( ",", $fields ) );
        }
        else {
            $this->db->select( $fields );
        }
        if ( !empty( $tableName ) ) {
            $this->db->from( $tableName );
        }
        $this->optionHandler( $options );

        $query = $this->db->get();
        if ( !$query ) {
            print_r( $this->db->last_query() );
            die;
        }
        // print_r($this->db->last_query());die;
        $resultSet = [];

        $resultSet = $query->result_array();
        if ( count( $resultSet ) > 0 ) {
            return $resultSet;
        }
        else {
            return false;
        }

    }



    private function optionHandler( $options ) {
        $arrayFlag = true;
        if ( count( $options ) === 0 || empty( $options ) || null === $options ) {
            $arrayFlag = false;
        }

        if ( !$arrayFlag ) {
            return false;
        }

        if ( isset( $options["where"] ) && !empty( $options["where"] ) ) {
            if ( is_array( $options["where"] ) ) {
                foreach ( $options["where"] as $key => $value ) {
                    $this->db->where( $key, $value );
                }
            }
            else {
                $this->db->where( $options["where"] );
            }
        }

        if ( isset( $options["join"] ) && !empty( $options["join"] ) ) {
            foreach ( $options["join"] as $key => $value ) {
                $this->db->join( $key, $value );
            }
        }

        if ( isset( $options["left_join"] ) && !empty( $options["left_join"] ) ) {
            foreach ( $options["left_join"] as $key => $value ) {
                $this->db->join( $key, $value, 'LEFT' );
            }
        }

        if ( isset( $options["sort"] ) && !empty( $options["sort"] ) ) {
            if ( is_array( $options["sort"] ) ) {
                foreach ( $options["sort"] as $key => $value ) {
                    $this->db->order_by( $key, $value );
                }
            }
            else {
                $this->db->order_by( $options["sort"], "ASC" );
            }
        }

        if ( isset( $options["limit"] ) && !empty( $options["limit"] ) ) {
            if ( !is_array( $options["limit"] ) ) {
                $this->db->limit( $options["limit"] );
            }
            else if ( count( $options["limit"] ) === 1 ) {
                $this->db->limit( $options["limit"][0] );
            }
            else if ( count( $options["limit"] ) === 2 ) {
                $this->db->limit( $options["limit"][0], $options["limit"][1] );
            }
            else {
                return false;
            }
        }

        if ( isset( $options["group_by"] ) && !empty( $options["group_by"] ) ) {
            if ( is_array( $options["group_by"] ) ) {
                foreach ( $options["group_by"] as $value ) {
                    $this->db->group_by( $value );
                }
            }
            else {
                $this->db->group_by( $options["group_by"] );
            }
        }

        if ( isset( $options["order_by"] ) && !empty( $options["order_by"] ) ) {
            if ( is_array( $options["order_by"] ) ) {
                foreach ( $options["order_by"] as $value ) {
                    $this->db->order_by( $value );
                }
            }
            else {
                $this->db->order_by( $options["order_by"] );
            }
        }

    }
}
