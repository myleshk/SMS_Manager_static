<?php
/**
 * Created by PhpStorm.
 * User: myles
 * Date: 8/1/2017
 * Time: 10:23 PM
 */

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    file_put_contents('../data.log', $data . PHP_EOL, FILE_APPEND | LOCK_EX);

    send_response(['success' => true]);
} else {
    send_response(['success' => false, 'error' => 'no data']);
}


function send_response($res)
{
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($res);
    exit();
}