<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

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

// POST値を取得
$item_id = get_post('item_id');
$amount = get_post('amount');

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$login_user = get_login_user($db);

// カートの商品の数量変更処理を行い実行結果の成否を取得
$result = change_cart_amount($db, $login_user['user_id'], $item_id, $amount);
// 数量変更処理が正常に実行されたら
if($result) {
    // 結果のメッセージをセット
    set_message('購入数を変更しました。');
// 条件外なら
} else {
    // エラーメッセージをセット
    set_error('購入数の変更に失敗しました。');
}

// カートページにリダイレクト
redirect_to(CART_URL);