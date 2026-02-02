<?php
require_once("funcs.php");

$username = $_POST["username"];
$lpw      = $_POST["lpw"];
$theme    = $_POST["favorite_theme"];

if(empty($username) || empty($lpw)){
    exit("Username/Password empty");
}

$hlpw = password_hash($lpw, PASSWORD_DEFAULT);
$pdo = db_conn();

$sql = "INSERT INTO users(username, password, favorite_theme, indate) VALUES(:username, :lpw, :theme, sysdate())";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $hlpw, PDO::PARAM_STR);
$stmt->bindValue(':theme', $theme, PDO::PARAM_STR);
$status = $stmt->execute();

if($status == false){
    $error = $stmt->errorInfo();
    exit("ErrorQuery:".$error[2]);
} else {
    header("Location: login.php");
    exit();
}