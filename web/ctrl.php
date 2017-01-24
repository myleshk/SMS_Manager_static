<?php
/**
 * Created by PhpStorm.
 * User: myles
 * Date: 24/1/2017
 * Time: 1:46 AM
 */

require __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    send_response(['error' => 'No action']);
}

if (!isset($_SESSION)) session_start();

$MG = new SMS_Manager\Manager();
$user_id = false;

if (isset($_COOKIE['PHPSESSID']) && isset($_SESSION) && isset($_SESSION['user_id'])) {
//    $sid = $_COOKIE['PHPSESSID'];
    if ($_SESSION['user_id']) {
        $user_id = $_SESSION['user_id'];
    }
}

switch ($action) {
    case 'login':
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $result = $MG->login($_POST['email'], $_POST['password']);

            if ($result) {
                $_SESSION['user_id'] = $result;
                send_response(['success' => true]);
            } else {
                // Login attempt failed.
                send_response(['success' => false]);
            }

        } else {
            send_response(['error' => 'Missing email/password', 'success' => false]);
        }
        break;

    case 'my_email':
        if (!$user_id) send_response(['success' => false, 'error' => 'not logged in']);
        $email = $MG->getUserEmail($user_id);
        if ($email) {
            send_response(['success' => true, 'email' => $email]);
        } else {
            send_response(['success' => false]);
        }


    case 'register':
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $success = $MG->register($_POST['email'], $_POST['password']);
            send_response(['success' => $success]);
        } else {
            send_response(['success' => false, 'error' => 'Missing email/password']);
        }
        break;

    case 'validate_code':
        if (!$user_id) send_response(['success' => false, 'error' => 'not logged in']);
        if (isset($_POST['code'])) {
            $code = $_POST['code'];
            $uuid = $MG->getUUID($code);

            if ($uuid) {
                send_response(['success' => $MG->changeUserAssoc($user_id, $uuid), 'uuid' => $uuid]);
            } else {
                send_response(['success' => false]);
            }
        }
        break;

    case 'get_assoc_uuid':
        if (!$user_id) send_response(['success' => false, 'error' => 'not logged in']);

        $uuid = $MG->getAssocUUID($user_id);

        if ($uuid) {
            send_response(['success' => true, 'uuid' => $uuid]);
        } else {
            send_response(['success' => false]);
        }
        break;

    case 'get_message':
        if (!$user_id) send_response(['success' => false, 'error' => 'not logged in']);

        $uuid = $MG->getAssocUUID($user_id);

        if ($uuid) {
            $messages = $MG->getMessages($uuid);
            send_response(['success' => true, 'messages' => $messages]);
        } else {
            send_response(['success' => false]);
        }
        break;

}


function send_response($res)
{
    $json_res = json_encode($res);
    if (!json_last_error()) {
        echo $json_res;
    } else {
        echo '{"error": "Server Error"}';
    }

    exit();
}