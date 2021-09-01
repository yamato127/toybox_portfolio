<?php

// DBからユーザーID指定でユーザーデータを取得する関数
function get_user($db, $user_id) {
    // SQL文を作成
    $sql = "
        SELECT
            user_id, 
            user_name,
            password,
            sex,
            age,
            type
        FROM
            tb_users
        WHERE
            user_id = ?
        LIMIT 1
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_id);
    // SQL文を実行した結果を返す
    return fetch_query($db, $sql, $params);
}

// DBからユーザー名指定でユーザーデータを取得する関数
function get_user_by_name($db, $user_name) {
    // SQL文を作成
    $sql = "
        SELECT
            user_id, 
            user_name,
            password,
            sex,
            age,
            type
        FROM
            tb_users
        WHERE
            user_name = ?
        LIMIT 1
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_name);
    // SQL文を実行した結果を返す
    return fetch_query($db, $sql, $params);
}

// DBから全てのユーザーデータを取得する関数
function get_all_users($db) {
    // SQL文を作成
    $sql = "
        SELECT
            user_id, 
            user_name,
            password,
            sex,
            age,
            type,
            create_date
        FROM
            tb_users
    ";
    
    // SQL文を実行した結果を返す
    return fetch_all_query($db, $sql);
}

// 指定のユーザーでログイン状態にする関数
function login_as($db, $user_name, $password) {
    // ユーザーデータを取得
    $user = get_user_by_name($db, $user_name);
    // ユーザーデータが取得できていない、またはパスワードが間違っていれば
    if($user === false || password_verify($password, $user['password']) === false) {
        // falseを返す
        return false;
    }
    // セッション変数に'user_id'を保存
    set_session('user_id', $user['user_id']);
    // ユーザーデータを返す
    return $user;
}

// DBからログインユーザーのユーザーデータを取得する関数
function get_login_user($db) {
    // セッション変数に保存されているユーザーIDを取得
    $login_user_id = get_session('user_id');
    
    // DBから取得したユーザーデータを返す
    return get_user($db, $login_user_id);
}

// ユーザー登録を行う関数
function regist_user($db, $user_name, $password, $password_confirmation, $sex, $age) {
    // 入力値が正しくなければ
    if(is_valid_user($db, $user_name, $password, $password_confirmation, $sex, $age) === false) {
        // falseを返す
        return false;
    }
    
    // パスワードのハッシュ化
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // DBにユーザーデータを追加し結果の成否を返す
    return insert_user($db, $user_name, $password_hash, $sex, $age);
}

// ユーザーが管理者であるかチェックする関数
function is_admin($user) {
    // 管理者であればtrueを返す
    return $user['type'] === USER_TYPE_ADMIN;
}

// ユーザー登録時の入力値の整合性をチェックする関数
function is_valid_user($db, $user_name, $password, $password_confirmation, $sex, $age) {
    // 短絡評価を避けるため一旦代入。
    // 各入力値の整合性チェック
    $is_valid_user_name = is_valid_user_name($db, $user_name);
    $is_valid_password = is_valid_password($password, $password_confirmation);
    $is_valid_sex = is_valid_sex($sex);
    $is_valid_age = is_valid_age($age);
    
    // すべての整合性が取れていればtrueを返す
    return $is_valid_user_name
        && $is_valid_password
        && $is_valid_sex
        && $is_valid_age;
}

// ユーザー名の整合性をチェックする関数
function is_valid_user_name($db, $user_name) {
    // 結果用変数の初期化
    $is_valid = true;
    
    // ユーザー名が既に登録されているなら
    if(exists_user_name($db, $user_name) === true) {
        // エラーメッセージをセット
        set_error('既に登録されているユーザー名です。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // ユーザー名の文字数が指定の範囲外ならば
    if(is_valid_length($user_name, MIN_USER_NAME_LENGTH, MAX_USER_NAME_LENGTH) === false) {
        // エラーメッセージをセット
        set_error('ユーザー名は'. MIN_USER_NAME_LENGTH . '文字以上、' . MAX_USER_NAME_LENGTH . '文字以内にしてください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // ユーザー名に半角英数字以外の文字が入っていれば
    if(is_alphanumeric($user_name) === false) {
        // エラーメッセージをセット
        set_error('ユーザー名は半角英数字で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// パスワードの整合性をチェックする関数
function is_valid_password($password, $password_confirmation) {
    // 結果用変数の初期化
    $is_valid = true;
    // パスワードの文字数が指定の範囲外ならば
    if(is_valid_length($password, MIN_PASSWORD_LENGTH, MAX_PASSWORD_LENGTH) === false) {
        // エラーメッセージをセット
        set_error('パスワードは'. MIN_PASSWORD_LENGTH . '文字以上、' . MAX_PASSWORD_LENGTH . '文字以内にしてください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // パスワードに半角英数字以外の文字が入っていれば
    if(is_alphanumeric($password) === false) {
        // エラーメッセージをセット
        set_error('パスワードは半角英数字で入力してください。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 再確認用のパスワードと一致しなければ
    if($password !== $password_confirmation) {
        // エラーメッセージをセット
        set_error('パスワードがパスワード確認用と一致しません。');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 性別の整合性をチェックする関数
function is_valid_sex($sex) {
    // 結果用変数の初期化
    $is_valid = true;
    //
    if(array_key_exists($sex, PERMITTED_SEXES) === false) {
        // エラーメッセージをセット
        set_error('性別が不正です');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// 年齢の整合性をチェックする関数
function is_valid_age($age) {
    // 結果用変数の初期化
    $is_valid = true;
    //
    if(array_key_exists($age, PERMITTED_AGES) === false) {
        // エラーメッセージをセット
        set_error('年齢が不正です');
        // 結果用変数をfalseに変更
        $is_valid = false;
    }
    // 整合性チェックの結果を返す
    return $is_valid;
}

// DBにユーザーデータを追加する関数
function insert_user($db, $user_name, $password, $sex, $age) {
    // 性別を数値で取得
    $sex_value = PERMITTED_SEXES[$sex];
    // 年齢ステータスを数値で取得
    $age_value = PERMITTED_AGES[$age];
    // 現在日時を取得
    $now_date = get_now_date();
    
    // SQL文を作成
    $sql = "
        INSERT INTO
            tb_users(user_name, password, sex, age, create_date, update_date)
        VALUES
            (?, ?, ?, ?, ?, ?);
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_name, $password, $sex_value, $age_value, $now_date, $now_date);
    // SQL文実行の成否を返す
    return execute_query($db, $sql, $params);
}

// ユーザ名の重複チェック
function exists_user_name($db, $user_name) {
    // ユーザ名が既に存在する場合はTRUE
    $exist_flag = false;
    
    // SQL文を作成
    $sql = "
        SELECT
            user_name
        FROM
            tb_users
        WHERE
            user_name = ?
    ";
    
    // プレースホルダにバインドする値の配列
    $params = array($user_name);
    
    // SQL文を実行しレコードが取得できていれば
    if(fetch_query($db, $sql, $params) !== false) {
        // フラグをTRUEにする
        $exist_flag = true;
    }
    
    // チェックの結果を返す
    return $exist_flag;
}

// 次回のログイン情報入力の省略を設定する関数
function set_login_cookie($user_name, $check_omit) {
    // 現在時刻を取得
    $now = time();
    // ユーザ名の入力を省略のチェックがONの場合、Cookieを利用する。OFFの場合、Cookieを削除する
    if ($check_omit === 'checked') {
        // Cookieへ保存する
        setcookie('user_name', $user_name, $now + 60 * 60 * 24 * 365);
        setcookie('check_omit', $check_omit, $now + 60 * 60 * 24 * 365);
    } else {
        // Cookieを削除する
        setcookie('user_name', '', $now - 3600);
        setcookie('check_omit', '', $now - 3600);
    }
}
