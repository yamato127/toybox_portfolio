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

// カート内の全商品データを取得
$carts = get_user_carts($db, $login_user['user_id']);
// カート内商品の合計数量を取得
$finish_total_amount = get_total_cart_amount($carts);
// カート内商品の合計金額を取得
$finish_total_price = get_total_cart_price($carts);

// 購入処理を行い実行結果の成否を取得
$result = purchase_carts($db, $login_user['user_id'], $carts);
// 購入処理が正常に実行されたら
if($result === false) {
    // エラーメッセージをセット
    set_error('商品が購入できませんでした。');
    // カートページにリダイレクト
    redirect_to(CART_URL);
}

// ヘッダー表示用
$total_amount = 0;

// ビューの読み込み
include_once VIEW_PATH . 'finish_view.php';
