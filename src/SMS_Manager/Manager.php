<?php
/**
 * Created by PhpStorm.
 * User: myles
 * Date: 17/1/2017
 * Time: 10:58 PM
 */

namespace SMS_Manager;

require_once 'MySQLi.php';


class Manager
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new \SMS_Manager\MySQLi();
        $this->conn = $this->db->getConn();
    }


    public function login($email, $password)
    {
        if (empty($email) || empty($password)) return false;

        $sql = "SELECT `id` FROM `SMS_Manager`.`auth_user` WHERE `email`='" . $this->db->escape($email) . "' AND `password`='" .
            $this->db->escape(self::hash_256($password)) . "';";


        $result = $this->db->query($sql);
        if (empty($result)) {
            // not exist
            return false;
        } else {
            return @$result[0]['id'];
        }
    }

    public function register($email, $password)
    {
        if (empty($email) || empty($password)) return false;

        $sql = "INSERT INTO `SMS_Manager`.`auth_user` (`email`,`password`) VALUES ('"
            . $this->db->escape($email) . "', '" . $this->db->escape(self::hash_256($password)) . "');";

        return $this->db->insert($sql) ? true : false;
    }

    public static function hash_256($string)
    {
        return hash('sha256', $string, false);
    }

    public function getSimpleID($uuid)
    {
        if (empty($uuid)) return false;
        // check if code already exists
        $sql = "SELECT * FROM `SMS_Manager`.`simple_id` WHERE `uuid`='" . $this->db->escape($uuid) . "' AND " .
            "`last_update` >= NOW() - INTERVAL 1 MINUTE;";
        $result = $this->db->query($sql);
        if (empty($result)) {
            // not exist
            $simple_id = $this->createNewSimpleID($uuid);
        } else {
            $simple_id = @$result[0]['simple_id'];
        }

        return $simple_id;
    }

    /**
     * @param $uuid
     * @return bool|int
     */
    private function createNewSimpleID($uuid)
    {
        $digits = 4; // Number of digits
        $min = pow(10, $digits - 1);
        $max = pow(10, $digits) - 1;
        $new_simple_id = rand($min, $max);
        if (empty($this->getUUID($new_simple_id))) {
            // save new simple id
            $sql = "INSERT INTO `SMS_Manager`.`simple_id` (`simple_id`, `uuid`) "
                . "VALUES ('" . $this->db->escape($new_simple_id) . "', '" . $this->db->escape($uuid) . "') "
                . "ON DUPLICATE KEY UPDATE `simple_id`='" . $this->db->escape($new_simple_id) . "', `last_update` = NOW();";
            if ($this->db->insert($sql)) {
                return "$new_simple_id";
            } else {
                return false;
            }
        } else {
            return $this->createNewSimpleID($uuid);
        }
    }

    public function getUUID($simple_id)
    {
        if (empty($simple_id)) return false;
        // check if code already exists
        $sql = "SELECT * FROM `SMS_Manager`.`simple_id` WHERE `simple_id`='" . $this->db->escape($simple_id) . "' AND "
            . "`last_update` >= NOW() - INTERVAL 1 MINUTE;";
        $result = $this->db->query($sql);
        if (empty($result)) {
            return false;
        } else {
            return @$result[0]['uuid'];
        }
    }


    public function changeUserAssoc($user_id, $uuid)
    {
        if (empty($uuid) || empty($user_id)) return false;

        $sql = "DELETE FROM `SMS_Manager`.`receiver_auth` WHERE `user_id`='" . $this->db->escape($user_id) . "';";

        $this->db->delete($sql);
        $sql = "INSERT INTO `SMS_Manager`.`receiver_auth` (`uuid`, `user_id`) VALUES ('" . $this->db->escape($uuid)
            . "', '" . $this->db->escape($user_id) . "');";
        return $this->db->insert($sql);
    }


    public function getAssocUUID($user_id)
    {
        if (empty($user_id)) return false;

        $sql = "SELECT `uuid` FROM `SMS_Manager`.`receiver_auth` WHERE `user_id`='" . $this->db->escape($user_id) . "';";

        $result = $this->db->query($sql);

        if (empty($result)) {
            return false;
        } else {
            return @$result[0]['uuid'];
        }


    }


    public function saveReport($data)
    {
        $sender = @$data['sender'];
        $body = @$data['body'];
        $slot = @$data['slot'];
        $uuid = @$data['uuid'];
        $timestamp = @$data['timestamp'];

        // check missing
        $missing = [];
        if ($sender !== '0' && empty($sender)) $missing[] = "sender";
        if ($body !== '0' && empty($body)) $missing[] = "body";
        if ($slot !== '0' && empty($slot)) $missing[] = "slot";
        if ($uuid !== '0' && empty($uuid)) $missing[] = "uuid";
        if ($timestamp !== '0' && empty($timestamp)) $missing[] = "timestamp";

        if (empty($missing)) {
            $sql = "INSERT INTO `SMS_Manager`.`message` (`uuid`, `sender`, `message_body`, `slot`, `timestamp`) "
                . "VALUES ('" . $this->db->escape($uuid) . "', '" . $this->db->escape($sender)
                . "', '" . $this->db->escape($body) . "', '" . $this->db->escape($slot) . "', '"
                . $this->db->escape($timestamp) . "');";

            if ($this->db->insert($sql)) {
                return true;
            } else {
                return ['error' => 'DB error'];
            }
        } else {
            return ['error' => 'incomplete data: ' . implode(",", $missing)];
        }
    }


    public function getMessages($uuid)
    {
        if (empty($uuid)) return false;

        $sql = "SELECT * FROM `SMS_Manager`.`message` WHERE `uuid`='" . $this->db->escape($uuid) . "';";
        $result = $this->db->query($sql);

        return @$result;
    }
}