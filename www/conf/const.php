<?php

define('MODEL_PATH', '../model/');
define('VIEW_PATH', '../view/');

define('IMAGE_PATH', './assets/image/');
define('ITEM_IMAGE_PATH', './assets/image/item/');
define('STYLESHEET_PATH', './assets/css/');
define('SCRIPT_PATH', './assets/JavaScript/');

define('SIGNUP_URL', './signup.php');
define('LOGIN_URL', './login.php');
define('LOGOUT_URL', './logout.php');
define('HOME_URL', './index.php');
define('CART_URL', './cart.php');
define('FINISH_URL', './finish.php');
define('ITEM_URL', './item.php');
define('ORDER_URL', './order.php');
define('ADMIN_ITEM_URL', './admin_item.php');
define('ADMIN_USER_URL', './admin_user.php');

// データベースの接続情報
define('DB_HOST', 'mysql');  // MySQLのHOST名
define('DB_NAME', 'sample'); // MySQLのDB名
define('DB_USER', 'testuser');      // MySQLのユーザ名
define('DB_PASS', 'password');    // MySQLのパスワード
define('DB_CHARSET', 'utf8');  // MySQLのcharset
 
define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング

// 正規表現
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');

// 各入力項目の最大値を設定
define('MIN_USER_NAME_LENGTH', 6);
define('MAX_USER_NAME_LENGTH', 20);
define('MIN_PASSWORD_LENGTH', 6);
define('MAX_PASSWORD_LENGTH', 20);

define('MIN_ITEM_NAME_LENGTH', 1);
define('MAX_ITEM_NAME_LENGTH', 30);
define('MIN_COMMENT_LENGTH', 1);
define('MAX_COMMENT_LENGTH', 200);
define('MAX_PRICE', 9999999);
define('MAX_STOCK', 99999);
define('MIN_AMOUNT', 1);
define('MAX_AMOUNT', 99);

define('MAX_PAGE_RECORD', 8);

// ユーザー判別
define('USER_TYPE_ADMIN', 1);
define('USER_TYPE_NORMAL', 2);

const PERMITTED_SEXES = array(
    '選択しない' => 0,
    '男の子' => 1,
    '女の子' => 2
);

const PERMITTED_AGES = array(
    '選択しない' => 0,
    '0ヶ月〜' => 1,
    '3ヶ月〜' => 2,
    '6ヶ月〜' => 3,
    '9ヶ月〜' => 4,
    '1才〜' => 5,
    '1.5才〜' => 6,
    '2才〜' => 7,
    '3才〜' => 8,
    '4才〜' => 9,
    '5才〜' => 10,
    '6才〜' => 11
);

const PERMITTED_ITEM_STATUSES = array(
    'open' => 1,
    'close' => 0
);

const PERMITTED_IMAGE_TYPES = array(
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG => 'png'
);

const PERMITTED_SORT_TYPES = array(
    '新着順' => 'ORDER BY create_date DESC',
    '価格が高い順' => 'ORDER BY price DESC',
    '価格が低い順' => 'ORDER BY price ASC',
    'お気に入り数順' => 'ORDER BY favorite_count DESC'
);

const PERMITTED_TOY_CATEGORIES = array(
    '男の子向け' => 1,
    '女の子向け' => 2,
    '知育・教育' => 3,
    'スポーツ' => 4,
    'パーティ' => 5,
    'その他' => 99
);