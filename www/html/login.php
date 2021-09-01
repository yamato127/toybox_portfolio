<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';

// セッション開始
session_start();

// CSRFトークンの生成
$csrf_token = get_csrf_token();

// 既にログインしていれば
if(is_logined() === true){
    // ホームページにリダイレクト
    redirect_to(HOME_URL);
}

// Cookie情報を取得
$check_omit = get_cookie('check_omit');
$user_name = get_cookie('user_name');

// ビューを読み込み
include_once VIEW_PATH . 'login_view.php';