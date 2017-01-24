<?php
/**
 * Created by PhpStorm.
 * User: myles
 * Date: 8/1/2017
 * Time: 10:23 PM
 */
require __DIR__ . '/../src/SMS_Manager/Manager.php';

$action = "";

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    send_response(['error' => 'no action'], false);
}

$MG = new \SMS_Manager\Manager();

switch ($action) {
    case 'report':
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
            $data_decoded = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {

                $report_result = $MG->saveReport($data_decoded);
                if ($report_result === true) {
                    send_response([], true);
                } else {
                    if (isset($report_result['error'])) {
                        send_response(['error' => $report_result['error']], false);
                    } else {
                        send_response(['error' => 'unknown error'], false);
                    }
                }
            } else {
                send_response(['error' => 'data decode error'], false);
            }
        } else {
            send_response(['error' => 'no data'], false);
        }
        break;

    case 'connect':
        send_response([], true);
        break;

    case 'get_simple_id':
        if (isset($_POST['uuid'])) {
            $uuid = $_POST['uuid'];

            $simple_id = $MG->getSimpleID($uuid);
            if ($simple_id) {
                send_response(['simple_id' => $simple_id], true);
            } else {
                send_response(['error' => 'get simple_id error'], false);
            }

        } else {
            send_response(['error' => 'no device uuid'], false);
        }
        break;

    case 'get_uuid':
        if (isset($_POST['simple_id'])) {
            $simple_id = $_POST['simple_id'];

            $MG = new \SMS_Manager\Manager();
            $uuid = $MG->getUUID($simple_id);
            if ($uuid) {
                send_response(['uuid' => $uuid], true);
            } else {
                send_response(['error' => 'get uuid error'], false);
            }

        } else {
            send_response(['error' => 'no device simple_id'], false);
        }
        break;

    default:
        send_response(['error' => 'unknown action: ' . $action], false);
}


function send_response($res, $success)
{
    header('Content-Type: application/json; charset=UTF-8');
    $res = array_merge($res, ['success' => $success]);
    echo json_encode($res);
    exit();
}
