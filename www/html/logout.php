<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';

// セッション開始
session_start();

// セッション変数を削除
$_SESSION = array();

// セッションCookieのパラメータを取得
$params = session_get_cookie_params();

// セッションCookieを削除
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
// セッションIDを無効化
session_destroy();

// ログインページにリダイレクト
redirect_to(LOGIN_URL);

// loginページへ移動
header('Location: login.php');