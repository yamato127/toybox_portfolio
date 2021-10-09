<?php

// DBから指定の購入履歴を取得する関数
function get_order($db, $order_id, $user_id, $admin) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_orders.order_id,
            tb_orders.user_id,
            tb_orders.create_date as order_date,
            sum(tb_order_details.order_price * tb_order_details.amount) as total_price
        FROM
            tb_orders
        JOIN
            tb_order_details
        ON
            tb_orders.order_id = tb_order_details.order_id
        WHERE
            tb_orders.order_id = ?
    ";
    // 管理者でなければユーザーIDを条件に追加
    if($admin === false) {
        $sql .= "
            AND
                tb_orders.user_id = ?
        ";
    }
    $sql .= "
        GROUP BY
            tb_orders.order_id
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($order_id);
    // 管理者でなければユーザーIDを追加
    if($admin === false) {
        $params[] = $user_id;
    }
    // SQL文を実行して取得した結果を返す
    return fetch_query($db, $sql, $params);
}

// DBから購入履歴一覧を取得する関数
function get_orders($db, $user_id, $admin) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_orders.order_id,
            tb_orders.user_id,
            tb_orders.create_date as order_date,
            sum(tb_order_details.order_price * tb_order_details.amount) as total_price
        FROM
            tb_orders
        JOIN
            tb_order_details
        ON
            tb_orders.order_id = tb_order_details.order_id
    ";
    // 管理者でなければユーザーIDを条件に追加
    if($admin === false) {
        $sql .= "
            WHERE
                tb_orders.user_id = ?
        ";
    }
    $sql .= "
        GROUP BY
            tb_orders.order_id
        ORDER BY
            order_date DESC
    ";
    // プレースホルダにバインドする値の配列
    $params = array();
    // 管理者でなければユーザーIDを追加
    if($admin === false) {
        $params[] = $user_id;
    }
    // SQL文を実行して取得した結果を返す
    return fetch_all_query($db, $sql, $params);
}

// DBから購入明細を取得する関数
function get_order_details($db, $order_id) {
    // SQL文を作成
    $sql = "
        SELECT
            tb_items.item_id,
            tb_items.name,
            tb_items.image,
            tb_order_details.order_price,
            tb_order_details.amount,
            tb_order_details.order_price * tb_order_details.amount as subtotal_price
        FROM
            tb_order_details
        JOIN
            tb_items
        ON
            tb_order_details.item_id = tb_items.item_id
        WHERE
            order_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($order_id);
    // SQL文を実行して取得した結果を返す
    return fetch_all_query($db, $sql, $params);
}


// DBに購入履歴を追加する関数
function insert_order($db, $user_id) {
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_orders (user_id, create_date, update_date)
        VALUES
            (?, ?, ?)
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// DBに購入明細を追加する関数
function insert_order_detail($db, $order_id, $item_id, $price, $amount) {
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_order_details (order_id, item_id, order_price, amount, create_date, update_date)
        VALUES
            (?, ?, ?, ?, ?, ?)
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($order_id, $item_id, $price, $amount, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// 購入明細テーブルの商品を削除する関数
function delete_item_order($db, $item_id) {
    // SQL文を作成
    $sql = "
        DELETE FROM
            tb_order_details
        WHERE
            item_id = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($item_id);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}