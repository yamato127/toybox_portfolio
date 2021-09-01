<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

// セッション開始
session_start();

// ログインしていなければ
if(is_logined() === false){
    // ログインページにリダイレクト
    redirect_to(LOGIN_URL);
}

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$login_user = get_login_user($db);

// ログインユーザーが管理者でなければ
if(is_admin($login_user) === false) {
    // ログインページにリダイレクト
    redirect_to(LOGIN_URL);
}

// DBから全ユーザーデータを取得
$users = get_all_users($db);

// ビューの読み込み
include_once VIEW_PATH . '/admin_user_view.php';