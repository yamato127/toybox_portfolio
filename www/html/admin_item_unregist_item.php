<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'order.php';

// セッション開始
session_start();

// CSRFトークンが不正なら
if(valid_csrf_token() !== true) {
    // エラーメッセージを表示してスクリプトを終了
    exit(h('エラーが発生しました'));
}

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

// POST値を取得
$item_id = get_post('item_id');

// 商品情報を取得
$item = get_item($db, $item_id);

// 商品情報を取得できていなければ
if($item === false) {
    // 商品管理ページにリダイレクト
    redirect_to(ADMIN_ITEM_URL);
}

// 商品の登録処理を行い実行結果の成否を取得
$result = unregist_item($db, $item);
// 登録処理が正常に実行されたら
if($result) {
    // 結果のメッセージをセット
    set_message('商品を削除しました。');
// 条件外なら
}else {
    // エラーメッセージをセット
    set_error('商品の削除に失敗しました。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_ITEM_URL);