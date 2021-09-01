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

// CSRFトークンの生成
$csrf_token = get_csrf_token();

// ログインしていなければ
if(is_logined() === false){
    // ログインページにリダイレクト
    redirect_to(LOGIN_URL);
}

// GET値を取得
$item_id = get_get('item_id');

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$login_user = get_login_user($db);

// 商品情報を取得
$item = get_item($db, $item_id, true);

// 商品情報を取得できていなければ
if($item === false) {
    // ホームページにリダイレクト
    redirect_to(HOME_URL);
}

// 商品のお気に入り状態を取得
$is_favorite = is_favorite($db, $login_user['user_id'], $item_id);

// カート内の全商品データを取得
$carts = get_user_carts($db, $login_user['user_id']);
// カート内商品の合計数量を取得
$total_amount = get_total_cart_amount($carts);

// ビューの読み込み
include_once VIEW_PATH . 'item_view.php';