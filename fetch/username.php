<?php
/**
 * In this file, which is queried on signup, will tell the user in real time if
 * their username is available or not. Queried by AJAX
 * 
 * This is not verification or signup, just a useful tool
 **/
session_start();
include getcwd().'/../api.php';

$link = connectToDatabase();
$username = mysqli_real_escape_string($link, $_REQUEST['name']);

$sql = $link->prepare("SELECT * FROM users WHERE username=?;");
$sql->bind_param("s", $username);
$sql->execute();
$result = $sql->get_result();
$sql->close();

$row = getFirstRow($result);

if($row==null && strlen($username)<255) {
    echo 'available';
    die();
} else {
    echo 'taken';
    die();
}
?>