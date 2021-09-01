<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - ログイン</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header.php'; ?>
        <main>
            <div class="wrapper">
                <section class="contents_section login_form">
                    <h1 class="login_title center">ログイン</h1>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                    <form method="post" action="./login_process.php">
                        <div class="login_set">
                            <p><span class="font_weight_bold">ユーザ名</span></p>
                            <input type="text" class="input_box" name="user_name" value="<?=$user_name?>">
                        </div>
                        <div class="login_set">
                            <p><span class="font_weight_bold">パスワード</span></p>
                            <input type="password" class="input_box" name="password">
                        </div>
                        <div class="omit_name_set"><label><input type="checkbox" name="check_omit" value="checked" <?=h($check_omit)?>>次回のユーザ名の入力を省略する</label></div>
                        <button type="submit">ログイン</button>
                        <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                    </form>
                    <div class="center"><a href="./signup.php">新規登録</a></div>
                </section>
            </div>
        </main>
    </body>
</html>