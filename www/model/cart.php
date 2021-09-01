<?php 

// DBからカート内の全商品データを取得する関数
function get_user_carts($db, $user_id) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_items.item_id,
            tb_items.name,
            tb_items.price,
            tb_stocks.stock,
            tb_items.status,
            tb_items.image,
            tb_carts.cart_id,
            tb_carts.user_id,
            tb_carts.amount
        FROM
            tb_carts
        JOIN
            tb_items
        ON
            tb_carts.item_id = tb_items.item_id
        JOIN
            tb_stocks
        ON
            tb_carts.item_id = tb_stocks.item_id
        WHERE
            tb_carts.user_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id);
    // SQL文を実行して取得した結果を返す
    return fetch_all_query($db, $sql, $params);
}

// DBからカート内の指定の$item_idの商品データを取得する関数
function get_user_cart($db, $user_id, $item_id) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_items.item_id,
            tb_items.name,
            tb_items.price,
            tb_stocks.stock,
            tb_items.status,
            tb_items.image,
            tb_carts.cart_id,
            tb_carts.user_id,
            tb_carts.amount
        FROM
            tb_carts
        JOIN
            tb_items
        ON
            tb_carts.item_id = tb_items.item_id
        JOIN
            tb_stocks
        ON
            tb_carts.item_id = tb_stocks.item_id
        WHERE
            tb_carts.user_id = ?
        AND
            tb_items.item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $item_id);
    // SQL文を実行して取得した結果を返す
    return fetch_query($db, $sql, $params);
}

// カートに商品を追加する関数
function add_cart($db, $user_id, $item_id, $add_amount) {
    // DBから指定のユーザーのカートの商品データを取得
    $cart = get_user_cart($db, $user_id, $item_id);
    // カート内に商品がなければ
    if($cart === false) {
        // カートに新しく商品を追加
        return insert_cart($db, $user_id, $item_id, $add_amount);
    }
    // カート内の商品数量を1増加
    return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + $add_amount);
}

function change_cart_amount($db, $user_id, $item_id, $change_amount) {
        // DBから指定のユーザーのカートの商品データを取得
    $cart = get_user_cart($db, $user_id, $item_id);
    // カート内に商品がなければ
    if($cart === false) {
        // falseを返す
        return false;
    }
    // カート内の商品数量を変更
    return update_cart_amount($db, $cart['cart_id'], $change_amount);
}

// カートに新しく商品を追加する関数
function insert_cart($db, $user_id, $item_id, $amount = 1) {
    // 数量変更の入力値が不正であれば
    if(is_valid_cart_amount($amount) === false) {
        // falseを返す
        return false;
    }
    
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_carts (user_id, item_id, amount, create_date, update_date)
        VALUES
            (?, ?, ?, ?, ?)
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $item_id, $amount, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// カート内の商品数量を変更する関数
function update_cart_amount($db, $cart_id, $amount) {
    // 数量変更の入力値が不正であれば
    if(is_valid_cart_amount($amount) === false) {
        // falseを返す
        return false;
    }
    
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        UPDATE
            tb_carts
        SET
            amount = ?,
            update_date = ?
        WHERE
            cart_id = ?
        LIMIT 1
    ";
    // プレースホルダにバインドする値の配列
    $params = array($amount, $now_date, $cart_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// カートテーブルの商品を削除する関数
function delete_item_cart($db, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_carts
        WHERE
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// カート内の商品データを削除する関数
function delete_user_cart($db, $user_id, $item_id) {
    // DBから指定のユーザーのカートの商品データを取得
    $cart = get_user_cart($db, $user_id, $item_id);
    // カート内に商品がなければ
    if($cart === false) {
        // falseを返す
        return false;
    }
    
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_carts
        WHERE
            cart_id = ?
        LIMIT 1
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($cart['cart_id']);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// カート内の商品の購入処理を行う関数
function purchase_carts($db, $user_id, $carts) {
    // カート内の商品が購入できない状態であれば
    if(validate_cart_purchase($carts) === false) {
        // falseを返す
        return false;
    }
    
    // 購入履歴を追加できなければ
    if(insert_order($db, $user_id) === false) {
        // falseを返す
        return false;
    }
    
    // 追加した購入履歴の注文番号を取得
    $order_id = $db->lastInsertId();
    
    // カート内の商品データを順次参照
    foreach($carts as $cart) {
        // 商品の購入処理が失敗したら
        if(purchase_carts_transaction($db, $order_id, $cart) === false) {
            // エラーメッセージをセット
            set_error($cart['name'] . 'の購入に失敗しました。');
        }
    }
    
    // カート内の全商品データを削除
    delete_user_carts($db, $carts[0]['user_id']);
}

// カート内の商品の購入処理を行う関数（トランザクション部分）
function purchase_carts_transaction($db, $order_id, $cart) {
    // トランザクション開始
    $db->beginTransaction();
    // 商品の在庫数を変更して購入明細を追加できたら
    if(update_item_stock($db, $cart['item_id'], $cart['stock'] - $cart['amount'])
    && insert_order_detail($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount'])) {
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

// カート内の全商品データを削除する関数
function delete_user_carts($db, $user_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_carts
        WHERE
            user_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id);
    // SQL文を実行
    execute_query($db, $sql, $params);
}

// カート内商品の合計金額を取得する関数
function get_total_cart_price($carts) {
    // 合計金額の初期化
    $total_price = 0;
    // カート内の商品データを順次参照
    foreach($carts as $cart) {
        // 商品毎の小計金額を合計金額に足す
        $total_price += $cart['price'] * $cart['amount'];
    }
    // 合計金額を返す
    return $total_price;
}

// カート内商品の合計数量を取得する関数
function get_total_cart_amount($carts) {
    // 合計数量の初期化
    $total_amount = 0;
    // カート内の商品データを順次参照
    foreach($carts as $cart) {
        // 商品ごとの数量を合計数量い足す
        $total_amount += $cart['amount'];
    }
    // 合計数量を返す
    return $total_amount;
}

// カート内の商品が正常に購入できるか検証する関数
function validate_cart_purchase($carts) {
    // カート内の商品数が0であれば
    if(count($carts) === 0) {
        // falseを返す
        return false;
    }
    
    // カート内の商品データを順次参照
    foreach($carts as $cart) {
        // 商品が非公開になっていれば
        if(is_open($cart) === false) {
            // エラーメッセージをセット
            set_error($cart['name'] . 'は現在購入できません。');
        }
        // 商品の購入数量より在庫数が少なければ
        if($cart['stock'] - $cart['amount'] < 0) {
            // エラーメッセージをセット
            set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
        }
    }
    
    // エラーメッセージがあれば
    if(has_error() === true) {
        // falseを返す
        return false;
    }
    // trueを返す
    return true;
}

// カート数量の整合性をチェックする関数
function is_valid_cart_amount($amount) {
    // 結果用変数の初期化
    $is_valid = true;
    
    // 数量がMIN_AMOUNT以上の整数でなければ
    if((is_positive_integer($amount) && MIN_AMOUNT <= $amount) === false) {
        // エラーメッセージをセット
        set_error('数量の値が不正です。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    // 数量がMAX_AMOUNT以下でなければ
    } else if(MAX_AMOUNT < $amount) {
        // エラーメッセージをセット
        set_error('一度に購入できる数量は99点までです。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    
    // 整合性チェックの結果を返す
    return $is_valid;
}