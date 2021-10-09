<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'order.php';

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

// 'order_id'のGET値を取得
$order_id = get_get('order_id');

// 指定の注文番号の購入履歴を取得
$order = get_order($db, $order_id, $login_user['user_id'], is_admin($login_user));

// 購入履歴が取得できていなければ
if($order === false) {
    // エラーメッセージを表示してスクリプトを終了
    exit(h("エラーが発生しました"));
}

// 購入明細を取得
$order_details = get_order_details($db, $order_id);

// カート内の全商品データを取得
$carts = get_user_carts($db, $login_user['user_id']);
// カート内商品の合計数量を取得
$total_amount = get_total_cart_amount($carts);

// ビューの読み込み
include_once VIEW_PATH . 'order_detail_view.php';