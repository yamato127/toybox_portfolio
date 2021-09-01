<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 商品管理</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_admin.css')?>">
    </head>
    <body>
        <h1 class="page_title">商品管理ページ</h1>
        <p><a href="./logout.php">ログアウト</a></p>
        <p><a href="./admin_user.php">ユーザ管理ページ</a></p>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
        <section>
            <h2 class="section_title">新規商品登録</h2>
            <form method="post" action="./admin_item_regist_item.php" enctype="multipart/form-data">
                <div><label>名前：<input type="text" name="name"></label></div>
                <div><label>価格：<input type="text" name="price"></label></div>
                <div><label>コメント：<textarea name="comment"></textarea></label></div>
                <div>
                    <label>カテゴリ：
                        <select name="category">
<?php foreach(array_keys(PERMITTED_TOY_CATEGORIES) as $category) : ?>
                            <option value="<?=h($category)?>"><?=h($category)?></option>
<?php endforeach ?>
                        </select>
                    </label>
                </div>
                <div><label>個数：<input type="text" name="stock"></label></div>
                <div><label>商品画像：<input type="file" name="image"></label></div>
                <div>
                    <label>ステータス：
                        <select name="status">
                            <option value="open">公開</option>
                            <option value="close">非公開</option>
                        </select>
                    </label>
                </div>
                <div>
                    <input type="submit" value="商品を登録">
                    <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                </div>
            </form>
        </section>
        <section>
            <h2 class="section_title">商品情報一覧・変更</h2>
            <table>
                <thead>
                    <tr>
                        <th>商品画像</th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>コメント</th>
                        <th>カテゴリ</th>
                        <th>在庫数</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
<?php if (count($items) > 0) : ?>
                <tbody>
<?php foreach ($items as $item) : ?>
<?php if ($item['status'] === 1) : ?>
                    <tr>
<?php else : ?>
                    <tr class="status_false">
<?php endif ?>
                        <td><img src="<?=h(ITEM_IMAGE_PATH . $item['image'])?>"></td>
                        <td><?=h($item['name'])?></td>
                        <td><?=h($item['price'])?>円</td>
                        <td><?=h($item['comment'])?></td>
                        <td><?=h(array_keys(PERMITTED_TOY_CATEGORIES, $item['category'])[0])?></td>
                        <td>
                            <form method="post" action="./admin_item_change_stock.php">
                                <input type="text" name="stock" value="<?=h($item['stock'])?>">個
                                <input type="submit" value="変更">
                                <input type="hidden" name="item_id" value="<?=h($item['item_id'])?>">
                                <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            </form>
                        </td>
                        <td>
                            <form method="post" action="./admin_item_change_status.php">
<?php if ($item['status'] === PERMITTED_ITEM_STATUSES['open']) : ?>
                                <input type="submit" value="公開→非公開">
                                <input type="hidden" name="status" value="close">
<?php else : ?>
                                <input type="submit" value="非公開→公開">
                                <input type="hidden" name="status" value="open">
<?php endif ?>
                                <input type="hidden" name="item_id" value="<?=h($item['item_id'])?>">
                                <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            </form>
                        </td>
                        <td>
                            <form method="post" action="./admin_item_unregist_item.php">
                                <input type="submit" value="削除">
                                <input type="hidden" name="item_id" value="<?=h($item['item_id'])?>">
                                <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            </form>
                        </td>
                    </tr>
<?php endforeach ?>
                </tbody>
<?php endif ?>
            </table>
        </section>
    </body>
</html>