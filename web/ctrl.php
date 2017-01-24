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

if(!isset($_SESSION)) session_start();

$MG = new SMS_Manager\Manager();

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

    case 'register':
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $success = $MG->register($_POST['email'], $_POST['password']);
            send_response(['success' => $success]);
        } else {
            send_response(['success' => false, 'error' => 'Missing email/password']);
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