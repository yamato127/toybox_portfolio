<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
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

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$login_user = get_login_user($db);

// カートの削除処理を行い実行結果の成否を取得
$result = delete_user_cart($db, $login_user['user_id'], $item_id);
// 削除処理が正常に実行されたら
if($result) {
    // 結果のメッセージをセット
    set_message('カートから商品を削除しました。');
// 条件外なら
} else {
    // エラーメッセージをセット
    set_error('カートの商品の削除に失敗しました。');
}

// カートページにリダイレクト
redirect_to(CART_URL);