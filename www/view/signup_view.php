<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - ユーザ登録</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header.php'; ?>
        <main>
            <div class="wrapper">
                <section class="contents_section register_form">
                    <h1 class="register_title center">新規ユーザ登録</h1>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                    <form method="post" action="./signup_process.php">
                        <h2 class="register_sub_title">ユーザ名・パスワード</h2>
                        <div class="register_set">
                            <div class="register_item">
                                <p><span class="font_weight_bold">ユーザ名</span></p>
                                <p><span class="register_required">必須</span></p>
                            </div>
                            <input type="text" class="input_box" name="user_name">
                            <p>半角英数字6〜20文字以内</p>
                        </div>
                        <div class="register_set">
                            <div class="register_item">
                                <p><span class="font_weight_bold">パスワード</span></p>
                                <p><span class="register_required">必須</span></p>
                            </div>
                            <input type="password" class="input_box" name="password">
                            <p>半角英数字6〜20文字以内</p>
                        </div>
                        <div class="register_set">
                            <div class="register_item">
                                <p><span class="font_weight_bold">パスワード確認用</span></p>
                                <p><span class="register_required">必須</span></p>
                            </div>
                            <input type="password" class="input_box" name="password_confirmation">
                        </div>
                        
                        <h2 class="register_sub_title">お子様情報</h2>
                        <div class="register_set">
                            <div class="register_item">
                                <p><span class="font_weight_bold">性別</span></p>
                                <p><span class="register_optional">任意</span></p>
                            </div>
                            <div class="select_sex">
<?php foreach(array_keys(PERMITTED_SEXES) as $sex) : ?>
                                <label><input type="radio" name="sex" value="<?=h($sex)?>"<?php if($sex === array_keys(PERMITTED_SEXES)[0]) echo 'checked'; ?>><?=h($sex)?></label>
<?php endforeach ?>
                            </div>
                        </div>
                        <div class="register_set">
                            <div class="register_item">
                                <p><span class="font_weight_bold">年齢</span></p>
                                <p><span class="register_optional">任意</span></p>
                            </div>
                            <select class="input_box" name="age">
<?php foreach(array_keys(PERMITTED_AGES) as $age) : ?>
                                <option value="<?=h($age)?>"><?=h($age)?></option>
<?php endforeach ?>
                            </select>
                        </div>
                        <button type="submit">登録</button>
                        <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                    </form>
                    <div class="center"><a href="./login.php">ログインページへ戻る</a></div>
                </section>
            </div>
        </main>
    </body>
</html>