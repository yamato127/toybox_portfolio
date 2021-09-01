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

// CSRFトークンの生成
$csrf_token = get_csrf_token();

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
$total_amount = get_total_cart_amount($carts);
// カート内商品の合計金額を取得
$total_price = get_total_cart_price($carts);

// ビューの読み込み
include_once VIEW_PATH . 'cart_view.php';