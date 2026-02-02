<?php
require_once("funcs.php");
$lid = $_POST["lid"];
$lpw = $_POST["lpw"];

$pdo = db_conn();
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :lid");
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->execute();
$val = $stmt->fetch();

if ($val && password_verify($lpw, $val["password"])) {
    $_SESSION["chk_ssid"] = session_id();
    $_SESSION["username"] = $val['username'];
    header("Location: index.php");
} else {
    header("Location: login.php");
}
exit();