<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 商品詳細</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper">
                <section class="contents_section item_detail">
                    <h2>商品詳細</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                    <div class="detail_item">
                        <div class="detail_item_img_bg">
                            <img src="<?=h(ITEM_IMAGE_PATH . $item['image'])?>" alt="商品">
                        </div>
                        <div class="detail_item_description">
                            <p class="detail_item_name"><?=h($item['name'])?></p>
                            <p><span class="font_price"><?=h(number_format($item['price']))?>円</span>(税込)</p>
                            <form method="post" action="./item_change_favorite.php">
<?php if ($is_favorite) : ?>
                                <button class="favorite_true" type="submit"><span class="font_star">★</span>&nbsp;お気に入り済&nbsp;<span class="font_star">★</span></button>
<?php else : ?>
                                <button class="favorite_false" type="submit">お気に入り登録</button>
<?php endif ?>
                                <input type="hidden" name="item_id" value="<?=h($item['item_id'])?>">
                                <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            </form>
                            <p>カテゴリ：<?=h(array_keys(PERMITTED_TOY_CATEGORIES, $item['category'])[0])?></p>
                            <p class="detail_comment">商品説明：<?=h($item['comment'])?></p>
                            <form method="post" action="./item_add_cart.php">
                                <p>
                                    数量：
                                    <select class="amount_box" name="add_amount">
<?php for ($i = MIN_AMOUNT; $i <= MAX_AMOUNT; $i++) : ?>
                                        <option value="<?=h($i)?>"><?=h($i)?></option>
<?php endfor ?>
                                    </select>
                                    （在庫数：<?=h($item['stock'])?>)
                                </p>
                                <button class="insert_button" type="submit">カートに追加</button>
                                <input type="hidden" name="item_id" value="<?=h($item['item_id'])?>">
                                <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            </form>
                        </div>
                    </div>
                    <p class="right"><a href="./index.php">商品一覧へ戻る</a></p>
                </section>
            </div>
        </main>
    </body>
</html>