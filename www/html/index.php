<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

//セッション開始
session_start();

// CSRFトークンの生成
$csrf_token = get_csrf_token();

// ログインしていなければ
if(is_logined() === false){
    // ログインページにリダイレクト
    redirect_to(LOGIN_URL);
}

// GET値を取得
$selected_sort_type = get_get('sort_type');
$keyword = get_get('keyword');
$selected_category = get_get('category');
$favorite = get_get('favorite');
$page = get_get('page');

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$login_user = get_login_user($db);

// 全ページ分の商品データを取得
$all_items = get_search_items($db, $login_user['user_id'], $selected_sort_type, $keyword, $selected_category, $favorite, false);
// 最大ページ数を取得
$max_page = ceil(count($all_items) / MAX_PAGE_RECORD);
// 現在のページの整合性をチェック
$page = validate_page($page, $max_page);

// 指定のページに表示する商品データを取得
$items = get_search_items($db, $login_user['user_id'], $selected_sort_type, $keyword, $selected_category, $favorite, true, $page);

// ランキング表示用の商品データを取得
$ranking_all = get_ranking_all($db);

// カート内の全商品データを取得
$carts = get_user_carts($db, $login_user['user_id']);
// カート内商品の合計数量を取得
$total_amount = get_total_cart_amount($carts);

// 現在の検索用のクエリストリングを取得
$search_query = "&sort_type=" . $selected_sort_type . "&keyword=" . $keyword . "&category=" . $selected_category . "&favorite=" . $favorite;

// ビューを読み込み
include_once VIEW_PATH . 'index_view.php';