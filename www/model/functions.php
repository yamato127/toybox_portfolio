<?php

// 変数の情報を表示してスクリプトを終了する関数
function dd($var) {
    // 変数に関する情報を表示
    var_dump($var);
    // 現在のスクリプトを終了
    exit();
}

// 指定のURLにリダイレクトする関数
function redirect_to($url) {
    // $ureにリダイレクト
    header('Location: ' . $url);
    // 現在のスクリプトを終了
    exit;
}

// GET値を取得する関数
function get_get($name) {
    // $nameのGET値が送信されていれば
    if(isset($_GET[$name]) === true) {
        // GET値を返す
        return $_GET[$name];
    };
    // 空文字を返す
    return '';
}

// POST値を取得する関数
function get_post($name) {
    // $nameのPOST値が送信されていれば
    if(isset($_POST[$name]) === true) {
        // POST値を返す
        return $_POST[$name];
    };
    // 空文字を返す
    return '';
}

// アップロードされたファイルの値を取得する関数
function get_file($name) {
    // ファイルがアップロードされていれば
    if(isset($_FILES[$name]) === true) {
        // アップロードされた値を返す
        return $_FILES[$name];
    };
    // 空の配列を返す
    return array();
}

// クッキーの値を取得する関数
function get_cookie($name) {
    // $nameのセッションが存在すれば
    if(isset($_COOKIE[$name]) === true) {
        // セッションの値を返す
        return $_COOKIE[$name];
    };
    // 空文字を返す
    return '';
}

// セッションの値を取得する関数
function get_session($name) {
    // $nameのセッションが存在すれば
    if(isset($_SESSION[$name]) === true) {
        // セッションの値を返す
        return $_SESSION[$name];
    };
    // 空文字を返す
    return '';
}

// セッションを設定する関数
function set_session($name, $value) {
    // セッション変数に$nameを保存
    $_SESSION[$name] = $value;
}

// エラーメッセージを追加する関数
function set_error($error) {
    // セッション変数にエラーメッセージを追加
    $_SESSION['__errors'][] = $error;
}

// エラーメッセージを取得する関数
function get_errors() {
    // セッション変数に保存されているエラーメッセージを取得
    $errors = get_session('__errors');
    // エラーメッセージがなければ
    if($errors === '') {
        // 空の配列を返す
        return array();
    }
    // セッション変数のエラーメッセージを削除
    set_session('__errors',  array());
    // エラーメッセージを返す
    return $errors;
}

// エラーメッセージの有無を返す関数
function has_error() {
    // セッション変数'__errors'が存在し、エラーメッセージが一つ以上あればtrueを返す
    return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// 処理結果のメッセージを追加する関数
function set_message($message) {
    // セッション変数にメッセージを追加
    $_SESSION['__messages'][] = $message;
}

// 処理結果のメッセージを取得する関数
function get_messages() {
    // セッション変数に保存されているメッセージを取得
    $messages = get_session('__messages');
    // メッセージがなければ
    if($messages === '') {
        // 空の配列を返す
        return array();
    }
    // セッション変数のメッセージを削除
    set_session('__messages',  array());
    // 処理結果のメッセージを返す
    return $messages;
}

// ログイン済かどうかチェックする関数
function is_logined() {
    // セッション変数'user_id'が存在すればtrueを返す
    return get_session('user_id') !== '';
}

// 新しいファイル名を取得する関数
function get_upload_filename($file) {
    // アップロードした画像ファイルが正しくなければ
    if(is_valid_upload_image($file) === false) {
        // 空文字を返す
        return '';
    }
    // imagetype定数を取得
    $mimetype = exif_imagetype($file['tmp_name']);
    // ファイル形式を取得
    $ext = PERMITTED_IMAGE_TYPES[$mimetype];
    // 一意のファイル名を作成して返す
    return get_random_string(40) . '.' . $ext;
}

// 指定した長さのランダムな文字列を取得
function get_random_string($length = 20) {
    // ランダムな文字列を返す
    return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// アップロードした画像ファイルを保存する関数
function save_image($image, $filename) {
    // アップロードされたファイルを指定のディレクトリに移動して保存（成否を返す）
    return move_uploaded_file($image['tmp_name'], ITEM_IMAGE_PATH . $filename);
}

// 指定のディレイクトリから画像ファイルを削除する関数
function delete_image($filename) {
    // 指定のディレクトリに画像ファイルが存在すれば
    if(file_exists(ITEM_IMAGE_PATH . $filename) === true) {
        // 画像ファイルを削除
        unlink(ITEM_IMAGE_PATH . $filename);
        // trueを返す
        return true;
    }
    // falseを返す
    return false;
  
}

// 文字数をチェックする関数
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX) {
    // 文字数を取得
    $length = mb_strlen($string);
    // 文字数が指定の範囲内ならtrueを返す
    return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// 文字列が半角英数字のみかチェックする関数
function is_alphanumeric($string) {
    // 文字列が半角英数字のみならtrueを返す
    return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// 文字列が0以上の整数かチェックする関数
function is_positive_integer($string) {
    // 文字列が0以上の整数ならtrueを返す
    return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// 文字列のフォーマットをチェックする関数
function is_valid_format($string, $format) {
    // バリデーションの結果を返す
    return preg_match($format, $string) === 1;
}

// アップロードした画像ファイルの整合性をチェックする関数
function is_valid_upload_image($image) {
    // HTTP POSTで画像ファイルがアップロードされていなければ
    if(is_uploaded_file($image['tmp_name']) === false) {
        // エラーメッセージをセット
        set_error('ファイル形式が不正です。');
        // falseを返す
        return false;
    }
    // imagetype定数を取得
    $mimetype = exif_imagetype($image['tmp_name']) ;
    // ファイル形式がPERMITTED_IMAGE_TYPESになければ
    if( array_key_exists($mimetype, PERMITTED_IMAGE_TYPES) === false) {
        // エラーメッセージをセット
        set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
        // falseを返す
        return false;
    }
    // trueを返す
    return true;
}

// 文字列を特殊文字に変換する関数
function h($str) {
    // htmlエスケープを施した値を返す
    return htmlspecialchars($str,ENT_QUOTES,HTML_CHARACTER_SET);
}

// トークンの生成を行う関数
function get_csrf_token() {
    // ランダムな30文字の文字列を所得
    $token = get_random_string(30);
    // セッション変数にトークンを保存
    set_session('csrf_token', $token);
    // トークンを返す
    return $token;
}

// トークンのチェックを行う関数
function valid_csrf_token() {
    // POSTで送られてきたトークンを取得
    $post_token = get_post('csrf_token');
    // セッションに保存されているトークンを取得
    $session_token = get_session('csrf_token');
    
    // トークンが取得できていなければ
    if($post_token === '') {
        // falseを返す
        return false;
    }
    // トークンが一致していればtrueを返す
    return $post_token === $session_token;
}









// カートに商品を追加
function add_cart_item($dbh, $user_id, $item_id, $add) {
    try {
        // カートに商品が存在するかチェック
        // SQL文を作成
        $sql = 'select *
                from tb_carts
                where user_id = ? and item_id = ?';
        // SQL文を実行する準備(PDOStatementオブジェクトを生成)
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        
        // 現在日時を取得
        $now_date = get_now_date();
        
        if (count($rows) > 0) {
            $amount = $rows[0]['amount'] + $add;
            // SQL文を作成
            $sql = 'update tb_carts
                    set amount = ?, update_date = ?
                    where user_id = ? and item_id = ?';
            // SQL文を実行する準備(PDOStatementオブジェクトを生成)
            $stmt = $dbh->prepare($sql);
            // SQL文のプレースホルダに値をバインド
            $stmt->bindValue(1, $amount, PDO::PARAM_INT);
            $stmt->bindValue(2, $now_date, PDO::PARAM_STR);
            $stmt->bindValue(3, $user_id, PDO::PARAM_INT);
            $stmt->bindValue(4, $item_id, PDO::PARAM_INT);
            // SQLを実行
            $stmt->execute();
            
        } else {
            $amount = $add;
            // SQL文を作成
            $sql = 'insert into tb_carts
                    (user_id, item_id, amount, create_date, update_date)
                    values
                    (?, ?, ?, ?, ?)';
            // SQL文を実行する準備(PDOStatementオブジェクトを生成)
            $stmt = $dbh->prepare($sql);
            // SQL文のプレースホルダに値をバインド
            $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
            $stmt->bindValue(3, $amount, PDO::PARAM_INT);
            $stmt->bindValue(4, $now_date, PDO::PARAM_STR);
            $stmt->bindValue(5, $now_date, PDO::PARAM_STR);
            // SQLを実行
            $stmt->execute();
            
        }
        
    } catch (PDOException $e) {
        throw $e;
    }
}

// 一回の最大購入点数を超えていないかチェック
function check_over_max_amount($dbh, $user_id, $item_id, $add) {
    $over_flag = FALSE;
    
    try {
        // カートに商品が存在するかチェック
        // SQL文を作成
        $sql = 'select *
                from tb_carts
                where user_id = ? and item_id = ?';
        // SQL文を実行する準備(PDOStatementオブジェクトを生成)
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $item_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        
        // 現在日時を取得
        $now_date = get_now_date();
        
        if (count($rows) > 0) {
            $amount = $rows[0]['amount'] + $add;
        } else {
            $amount = $add;
        }
        
        if ($amount > MAX_AMOUNT) {
            $over_flag = TRUE;
        }
        
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $over_flag;
}

// 値が半角英数字かチェック
function check_alpha_number($str) {
    return preg_match('/\A[a-zA-Z\d]+\z/', $str);
}

// 値が半角数字かチェック
function check_number($str) {
    return preg_match('/\A\d+\z/', $str);
}

function check_stock_shortage($items) {
    $shortage_flag = FALSE;
    
    foreach ($items as $item) {
        if ($item['amount'] > $item['stock']) {
            $shortage_flag = TRUE;
            break;
        }
    }
    
    return $shortage_flag;
}

// item_idがDBに存在し公開になっているかチェック
function exist_item_id($dbh, $item_id) {
    
    // item_idが存在する場合はTRUE
    $exist_flag = FALSE;
    
    try {
        // SQL文を作成
        $sql = 'select *
                from tb_items
                where item_id = ? and status = 1';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        
        if (count($rows) > 0) {
            $exist_flag = TRUE;
        }
        
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $exist_flag;
}

// クエリの実行結果を配列で取得
function get_as_array($dbh, $sql) {
    try {
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $rows;
}

// カート内の商品情報の配列を取得
function get_cart_item_info_array($dbh, $user_id) {
    try {
        // SQL文を作成
        $sql = 'select b.item_id, b.img, b.name, b.price, a.amount, c.stock
                from tb_carts as a
                inner join tb_items as b on a.item_id = b.item_id
                inner join tb_stocks as c on a.item_id = c.item_id
                where b.status =  1 and user_id = ?';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // レコードを取得
        $rows = $stmt->fetchAll();
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $rows;        
}

// 商品情報の配列を取得
function get_item_info_array($dbh) {
    // SQL文を作成
    $sql = 'select a.item_id, name, price, comment, category, stock, status, img
            from tb_items as a
            inner join tb_stocks as b
            on a.item_id = b.item_id';
    // クエリ実行
    return get_as_array($dbh, $sql);
}

// カート内の商品点数を取得
function get_total_amount_cart_item($items) {
    $total_amount = 0;
    
    foreach ($items as $item) {
        $total_amount += $item['amount'];
    }
    
    return $total_amount;
}

// カート内の商品点数を取得
function get_total_price_cart_item($items) {
    $total_price = 0;
    
    foreach ($items as $item) {
        $total_price += $item['price'] * $item['amount'];
    }
    
    return $total_price;
}

// 現在日時を取得
function get_now_date() {
    return date('Y-m-d H:i:s');
}

// 指定のユーザIDのユーザ情報を取得
function get_user_info($dbh, $user_id) {
    $user_info = array();
    
    try {
        // SQL文を作成
        $sql = 'select user_name, sex, age
                from tb_users
                where user_id = ?';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        // レコードの取得
        $rows = $stmt->fetchAll();
        
        if (count($rows) > 0) {
            $user_info = $rows[0];
        }
        
    } catch (PDOException $e) {
        throw $e;
    }
    
    return $user_info;
}

// 文字列の先頭、末尾の空白を削除
function trim_space($str) {
    // \x00 = NULL, \s = 空白文字
    return  preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $str);
}