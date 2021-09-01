<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';

// セッション開始
session_start();

// CSRFトークンが不正なら
if(valid_csrf_token() !== true) {
    // エラーメッセージを表示してスクリプトを終了
    exit(h('エラーが発生しました'));
}

// 既にログインしていれば
if(is_logined() === true){
    // ホームページにリダイレクト
    redirect_to(HOME_URL);
}

// POST値を取得
$user_name = get_post('user_name');
$password = get_post('password');
$check_omit = get_post('check_omit');

// DBに接続（PDOを取得）
$db = get_db_connect();

// DBから名前とパスワードが一致するユーザーデータを取得し、ログイン状態にする
$login_user = login_as($db, $user_name, $password);
// 一致するユーザーがなければ
if($login_user === false) {
    set_error('ログインに失敗しました。');
    // エラーメッセージをセット
    redirect_to(LOGIN_URL);
}

// 次回のログイン情報入力の省略設定
set_login_cookie($user_name, $check_omit);

// ログインユーザーが管理者なら
if ($login_user['type'] === USER_TYPE_ADMIN) {
    // 管理ページにリダイレクト
    redirect_to(ADMIN_ITEM_URL);
}

// ホームページにリダイレクト
redirect_to(HOME_URL);