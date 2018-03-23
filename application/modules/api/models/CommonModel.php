<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CommonModel extends CI_Model {

    public function __construct() {
        $this->load->database();
        $this->load->library("session");
        $this->load->helper("cookie");
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
        $hashedCookieValidator = hash("sha256", $loginCookie[1] . $cookieData["create_date"]);

        if ($validator !== $hashedCookieValidator) {
            return false;
        }

        unset($cookieData["cookie_validator"]);

        return $cookieData;
    }

}
