<?php

// DBから$item_idの商品データを取得する関数
function get_item($db, $item_id, $is_open = false) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_items.item_id, 
            name,
            price,
            comment,
            category,
            stock,
            image,
            status
        FROM
            tb_items
        JOIN
            tb_stocks
        ON
            tb_items.item_id = tb_stocks.item_id
        WHERE
            tb_items.item_id = ?
    ";
    // 引数にtrueが指定されていれば
    if($is_open === true) {
        // ステータスが公開の商品を条件に追加
        $sql .= "
            AND
                status = 1
        ";
    }
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文を実行して取得した結果を返す
    return fetch_query($db, $sql, $params);
}

// DBから全商品データを取得する関数
function get_all_items($db) {
    // SQL文を作成
    $sql = "
        SELECT
            DISTINCT tb_items.item_id, 
            name,
            price,
            comment,
            category,
            stock,
            image,
            status,
            tb_items.create_date,
            count(user_id) as favorite_count
        FROM
            tb_items
        JOIN
            tb_stocks
        ON
            tb_items.item_id = tb_stocks.item_id
        LEFT OUTER JOIN
            tb_favorites
        ON
            tb_items.item_id = tb_favorites.item_id
        GROUP BY
            item_id
    ";
    
    // SQL文を実行して取得した結果を返す
    return fetch_all_query($db, $sql);
}

// DBから条件指定で全商品データを取得する関数
function get_items($db, $user_id, $sort_type, $keyword, $category, $is_favorite) {
    $sort_sql = PERMITTED_SORT_TYPES[$sort_type];
    if($category !== '') $category_value = PERMITTED_TOY_CATEGORIES[$category];
    
    // SQL文を作成
    $sql = "
        SELECT
            DISTINCT tb_items.item_id, 
            name,
            price,
            comment,
            category,
            stock,
            image,
            status,
            tb_items.create_date,
            count(user_id) as favorite_count
        FROM
            tb_items
        JOIN
            tb_stocks
        ON
            tb_items.item_id = tb_stocks.item_id
        LEFT OUTER JOIN
            tb_favorites
        ON
            tb_items.item_id = tb_favorites.item_id
        WHERE
            status = 1
        AND
            name like ?
    ";
    
    if($category !== '') {
        $sql .= "
            AND
                category = ?
        ";
    }
    
    if($is_favorite) {
        $sql .= "
            AND
                user_id = ?
        ";
    }
    
    $sql .= "
        GROUP BY
            item_id
    " . $sort_sql;
    
    
    $params = array('%' . $keyword . '%');
    if($category !== '') $params[] = $category_value;
    if($is_favorite) $params[] = $user_id;
    
    // SQL文を実行して取得した結果を返す
    return fetch_all_query($db, $sql, $params);
}

// DBから公開されている全商品データを取得する関数
function get_search_items($db, $user_id, $sort_type, $keyword, $category, $favorite) {
    if(array_key_exists($sort_type, PERMITTED_SORT_TYPES) === false) {
        $sort_type = array_keys(PERMITTED_SORT_TYPES)[0];
    }
    
    $keyword = trim_space($keyword);
    
    if(array_key_exists($category, PERMITTED_TOY_CATEGORIES) === false) {
        $category = '';
    }
    
    if($favorite === '1') {
        $is_favorite = true;
    } else {
        $is_favorite = false;
    }
    
    // DBから取得したデータを返す
    return get_items($db, $user_id, $sort_type, $keyword, $category, $is_favorite);
}

// お気に入りの状態を取得する関数
function is_favorite($db, $user_id, $item_id) {
    // SQL文を作成
    $sql = "
        SELECT
            favorite_id,
            user_id,
            item_id
        FROM
            tb_favorites
        WHERE
            user_id = ?
        AND
            item_id = ?
    ";
        
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $item_id);
    
    // SQL文を実行してレコードが取得できていれば
    if(fetch_query($db, $sql, $params) !== false) {
        // trueを返す
        return true;
    }
    
    // falseを返す
    return false;
}

// 商品登録を行う関数
function regist_item($db, $name, $price, $comment, $category, $stock, $status, $image) {
    // 新しいファイル名を取得
    $filename = get_upload_filename($image);
    // 入力値が正しくなければ
    if(validate_item($name, $price, $comment, $category, $stock, $filename, $status) === false) {
        // falseを返す
        return false;
    }
    
    // DBに商品データを追加し結果の成否を返す
    return regist_item_transaction($db, $name, $price, $comment, $category, $stock, $status, $image, $filename);
}

// 商品登録を行う関数（トランザクション部分）
function regist_item_transaction($db, $name, $price, $comment, $category, $stock, $status, $image, $filename) {
    // 現在日時を取得
    $now_date = get_now_date();
    
    // トランザクション開始
    $db->beginTransaction();
    // DBに商品データを追加してディレクトリに画像ファイルを保存できたら
    if(insert_item($db, $name, $price, $comment, $category, $filename, $status, $now_date)
    && insert_stock($db, $db->lastInsertId('item_id'), $stock, $now_date)
    && save_image($image, $filename)) {
        // コミット処理
        $db->commit();
        // trueを返す
        return true;
    }
    // ロールバック処理
    $db->rollback();
    
    // falseを返す
    return false;
}

// お気に入り状態を変更する関数
function change_favorite($db, $user_id, $item_id) {
    // 現在のお気に入り状態を取得
    $is_favorite = is_favorite($db, $user_id, $item_id);
    
    // お気に入りに登録済みであれば
    if($is_favorite) {
        // お気に入りを解除する
        if(unregist_favorite($db, $user_id, $item_id)) {
            // 結果のメッセージをセット
            set_message('お気に入りを解除しました');
            // trueを返す
            return true;
        }
    } else {
        // お気に入りに登録する
        if(regist_favorite($db, $user_id, $item_id)) {
            // 結果のメッセージをセット
            set_message('お気に入りに登録しました');
            // trueを返す
            return true;
        }
    }
    
    // falseを返す
    return false;
}

// 公開ステータスを変更する関数
function change_item_status($db, $item_id, $status) {
    // 公開ステータスの入力値が不正であれば
    if(is_valid_item_status($status) === false) {
        // falseを返す
        return false;
    }
    
    // 公開ステータス変更処理を実行し結果を返す
    return update_item_status($db, $item_id, $status);
}

// 在庫数を変更する関数
function change_item_stock($db, $item_id, $stock) {
    // 在庫数の入力値が不正であれば
    if(is_valid_item_stock($stock) === false) {
        // falseを返す
        return false;
    }
    
    // 在庫数変更処理を実行し結果を返す
    return update_item_stock($db, $item_id, $stock);
}

// お気に入りに登録する関数
function regist_favorite($db, $user_id, $item_id) {
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_favorites (user_id, item_id, create_date, update_date)
        VALUES
            (?, ?, ?, ?)
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $item_id, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// お気に入りに解除する関数
function unregist_favorite($db, $user_id, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_favorites
        WHERE
            user_id = ?
        AND
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// DBに商品データを追加する関数
function insert_item($db, $name, $price, $comment, $category, $filename, $status, $now_date) {
    // 商品カテゴリーを数値で取得
    $category_value = PERMITTED_TOY_CATEGORIES[$category];
    // 公開ステータスを数値で取得
    $status_value = PERMITTED_ITEM_STATUSES[$status];
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_items (name, price, comment, category, image, status, create_date, update_date)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($name, $price, $comment, $category_value, $filename, $status_value, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// DBに商品データを追加する関数
function insert_stock($db, $item_id, $stock, $now_date) {
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_stocks (item_id, stock, create_date, update_date)
        VALUES
            (?, ?, ?, ?)
    ";

    // プレースホルダにバインドする値の配列
    $params = array($item_id, $stock, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 商品の公開ステータスを変更する関数
function update_item_status($db, $item_id, $status) {
    // 公開ステータスを数値で取得
    $status_value = PERMITTED_ITEM_STATUSES[$status];
    
    // SQL文を作成
    $sql = "
        UPDATE
            tb_items
        SET
            status = ?
        WHERE
            item_id = ?
        LIMIT 1
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($status_value, $item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 商品の在庫数を変更する関数
function update_item_stock($db, $item_id, $stock) {
    // SQL文を作成
    $sql = "
        UPDATE
            tb_stocks
        SET
            stock = ?
        WHERE
            item_id = ?
        LIMIT 1
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($stock, $item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 商品に関するデータを全て削除する関数
function unregist_item($db, $item) {
    // トランザクション開始
    $db->beginTransaction();
    // DBの商品関連のデータを削除し、ディレクトリの画像ファイルを削除できたら
    if(delete_item($db, $item['item_id'])
    && delete_item_stock($db, $item['item_id'])
    && delete_item_cart($db, $item['item_id'])
    && delete_item_order($db, $item['item_id'])
    && delete_item_favorite($db, $item['item_id'])
    && delete_image($item['image'])) {
        // コミット処理
        $db->commit();
        // trueを返す
        return true;
    }
    // ロールバック処理
    $db->rollback();
    // falseを返す
    return false;
}

// 商品テーブルの商品を削除する関数
function delete_item($db, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_items
        WHERE
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 在庫数テーブルの商品を削除する関数
function delete_item_stock($db, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_stocks
        WHERE
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// お気に入りテーブルの商品を削除する関数
function delete_item_favorite($db, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_favorites
        WHERE
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 商品が公開になっているチェックする関数
function is_open($item) {
    // 公開ならtrueを返す
    return $item['status'] === 1;
}

// 商品登録時の入力値の整合性をチェックする関数
function validate_item($name, $price, $comment, $category, $stock, $filename, $status) {
    // 商品名の整合性チェック
    $is_valid_item_name = is_valid_item_name($name);
    // 価格の整合性チェック
    $is_valid_item_price = is_valid_item_price($price);
    // コメントの整合性チェック
    $is_valid_item_comment = is_valid_item_comment($comment);
    // カテゴリーの整合性チェック
    $is_valid_item_category = is_valid_item_category($category);
    // 在庫数の整合性チェック
    $is_valid_item_stock = is_valid_item_stock($stock);
    // 画像ファイル名の整合性チェック
    $is_valid_item_filename = is_valid_item_filename($filename);
    // 公開ステータスの整合性チェック
    $is_valid_item_status = is_valid_item_status($status);
    
    // すべての整合性が取れていればtrueを返す
    return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_comment
    && $is_valid_item_category
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// 商品名の整合性をチェックする関数
function is_valid_item_name($name) {
    // 結果用変数の初期化
    $is_valid = true;
    // 商品名の文字数が指定の範囲外ならば
    if(is_valid_length($name, MIN_ITEM_NAME_LENGTH, MAX_ITEM_NAME_LENGTH) === false) {
        // エラーメッセージをセット
        set_error('商品名は'. MIN_ITEM_NAME_LENGTH . '文字以上、' . MAX_ITEM_NAME_LENGTH . '文字以内にしてください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 価格の整合性をチェックする関数
function is_valid_item_price($price) {
    // 結果用変数の初期化
    $is_valid = true;
    // 価格が0以上の整数でなければ
    if(is_positive_integer($price) === false) {
        // エラーメッセージをセット
        set_error('価格は0以上の整数で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    } else if(MAX_PRICE < $price) {
        // エラーメッセージをセット
        set_error('価格は' . MAX_PRICE . '円以下で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// コメントの整合性をチェックする関数
function is_valid_item_comment($comment) {
    // 結果用変数の初期化
    $is_valid = true;
    // コメントの文字数が指定の範囲外ならば
    if(is_valid_length($comment, MIN_COMMENT_LENGTH, MAX_COMMENT_LENGTH) === false) {
        // エラーメッセージをセット
        set_error('コメントは'. MIN_COMMENT_LENGTH . '文字以上、' . MAX_COMMENT_LENGTH . '文字以内にしてください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// カテゴリーの整合性をチェックする関数
function is_valid_item_category($category) {
    // 結果用変数の初期化
    $is_valid = true;
    // カテゴリー以外の値が入力されていたら
    if(array_key_exists($category, PERMITTED_TOY_CATEGORIES) === false) {
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 在庫数の整合性をチェックする関数
function is_valid_item_stock($stock) {
    // 結果用変数の初期化
    $is_valid = true;
    // 在庫数が0以上の整数でなければ
    if(is_positive_integer($stock) === false) {
        // エラーメッセージをセット
        set_error('在庫数は0以上の整数で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    } else if(MAX_STOCK < $stock) {
        // エラーメッセージをセット
        set_error('在庫数は' . MAX_STOCK . '個以下で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 画像ファイル名の整合性をチェックする関数
function is_valid_item_filename($filename) {
    // 結果用変数の初期化
    $is_valid = true;
    // 画像ファイル名が空文字なら
    if($filename === '') {
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 公開ステータスの整合性をチェックする関数
function is_valid_item_status($status) {
    // 結果用変数の初期化
    $is_valid = true;
    // 公開・非公開以外の値が入力されていたら
    if(array_key_exists($status, PERMITTED_ITEM_STATUSES) === false) {
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}