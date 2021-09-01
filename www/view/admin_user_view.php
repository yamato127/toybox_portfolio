<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - ユーザ管理</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_admin.css')?>">
    </head>
    <body>
        <h1 class="page_title">ユーザ管理ページ</h1>
        <p><a href="./logout.php">ログアウト</a></p>
        <p><a href="./admin_item.php">商品管理ページ</a></p>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
        <section>
            <h2 class="section_title">ユーザ情報一覧</h2>
            <table>
                <thead>
                    <tr>
                        <th>ユーザID</th>
                        <th>性別(子)</th>
                        <th>年齢(子)</th>
                        <th>登録日時</th>
                    </tr>
                </thead>
<?php if (count($users) > 0) : ?>
                <tbody>
<?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?=h($user['user_name'])?></td>
                        <td><?=h(array_keys(PERMITTED_SEXES, $user['sex'])[0])?></td>
                        <td><?=h(array_keys(PERMITTED_AGES, $user['age'])[0])?></td>
                        <td><?=h($user['create_date'])?></td>
                    </tr>
<?php endforeach ?>
                </tbody>
<?php endif ?>
            </table>
        </section>
    </body>
</html>