<?php
session_start();

function db_conn() {
    try {
        $db_name = "shogoritchiito_sakurabase";
        $db_host = "mysql3112.db.sakura.ne.jp";
        $db_id   = "shogoritchiito_sakurabase";
        $db_pw   = "Shogo1393"; 
        return new PDO('mysql:dbname='.$db_name.';charset=utf8;host='.$db_host, $db_id, $db_pw);
    } catch (PDOException $e) {
        exit('DB Connection Error:'.$e->getMessage());
    }
}

function loginCheck() {
    if (!isset($_SESSION["chk_ssid"]) || $_SESSION["chk_ssid"] != session_id()) {
        header("Location: login.php");
        exit();
    } else {
        session_regenerate_id(true);
        $_SESSION["chk_ssid"] = session_id();
    }
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}