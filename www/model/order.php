<?php

// DBからカート内の全商品データを取得する関数
function get_order($db, $order_id, $user_id, $admin) {
    // SQL文を作成
    $sql = "
        SELECT
            orders.order_id,
            orders.user_id,
            orders.created as order_date,
            sum(order_details.order_price * order_details.amount) as total_price
        FROM
            orders
        JOIN
            order_details
        ON
            orders.order_id = order_details.order_id
        WHERE
            orders.order_id = ?
    ";
    // 管理者でなければユーザーIDを条件に追加
    if($admin === false) {
        $sql .= "
            AND
                orders.user_id = ?
        ";
    }
    $sql .= "
        GROUP BY
            orders.order_id
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

// DBからカート内の全商品データを取得する関数
function get_orders($db, $user_id, $admin) {
    // SQL文を作成
    $sql = "
        SELECT
            orders.order_id,
            orders.user_id,
            orders.created as order_date,
            sum(order_details.order_price * order_details.amount) as total_price
        FROM
            orders
        JOIN
            order_details
        ON
            orders.order_id = order_details.order_id
    ";
    // 管理者でなければユーザーIDを条件に追加
    if($admin === false) {
        $sql .= "
            WHERE
                orders.user_id = ?
        ";
    }
    $sql .= "
        GROUP BY
            orders.order_id
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

function get_order_details($db, $order_id) {
    // SQL文を作成
    $sql = "
        SELECT
            items.name,
            order_details.order_price,
            order_details.amount,
            order_details.order_price * order_details.amount as subtotal_price
        FROM
            order_details
        JOIN
            items
        ON
            order_details.item_id = items.item_id
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