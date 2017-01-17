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
        $sql = "select * from `SMS_Manager`.`simple_id` where `simple_id`='" . $this->db->escape($simple_id) . "' AND "
            . "`last_update` >= NOW() - INTERVAL 1 MINUTE;";
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
}