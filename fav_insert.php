<?php
require_once("funcs.php");
loginCheck();
$theme = $_GET["theme"];
$username = $_SESSION["username"];

$pdo = db_conn();

// ユーザーIDの取得
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch();

// 保存
$stmt = $pdo->prepare("INSERT INTO favorites(user_id, theme_name, created_at) VALUES(:uid, :theme, sysdate())");
$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
$stmt->bindValue(':theme', $theme, PDO::PARAM_STR);
$stmt->execute();
echo "ok";


