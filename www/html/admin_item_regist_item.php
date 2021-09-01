<?php

// 設定ファイル読み込み
require_once '../conf/const.php';
// 関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

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
$name = get_post('name');
$price = get_post('price');
$comment = get_post('comment');
$category = get_post('category');
$stock = get_post('stock');
$status = get_post('status');

// アップロードされた'image'の値を取得
$image = get_file('image');

// 商品の登録処理を行い実行結果の成否を取得
$result = regist_item($db, $name, $price, $comment, $category, $stock, $status, $image);
// 登録処理が正常に実行されたら
if($result) {
    // 結果のメッセージをセット
    set_message('商品を登録しました。');
// 条件外なら
}else {
    // エラーメッセージをセット
    set_error('商品の登録に失敗しました。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_ITEM_URL);