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
$password_confirmation = get_post('password_confirmation');
$sex = get_post('sex');
$age = get_post('age');

// DBに接続（PDOを取得）
$db = get_db_connect();

// 例外処理
try {
    // ユーザー登録処理を行い実行結果の成否を取得
    $result = regist_user($db, $user_name, $password, $password_confirmation, $sex, $age);
    // ユーザー登録が正常にできなかったら
    if($result === false) {
        // エラーメッセージをセット
        set_error('ユーザー登録に失敗しました。');
        // サインアップページにリダイレクト
        redirect_to(SIGNUP_URL);
    }
// 例外が発生したなら
} catch(PDOException $e) {
    // エラーメッセージをセット
    set_error('ユーザー登録に失敗しました。');
    // サインアップページにリダイレクト
    redirect_to(SIGNUP_URL);
}

// 結果のメッセージをセット
set_message('ユーザー登録が完了しました。');
// 登録したユーザーでログイン状態にする
login_as($db, $user_name, $password);
// ホームページにリダイレクト
redirect_to(HOME_URL);