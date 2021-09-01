<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - ショッピングカート</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper cart_contents">
                <div class="left_cart_contents">
                    <section class="contents_section cart_item_list">
                        <h2>ショッピングカートの商品</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
<?php if (count($carts) === 0) : ?>
                        <p>カートに商品が入っていません</p>
<?php else : ?>
                        <div class="flex_column">
<?php for ($i = 0; $i < count($carts); $i++) : ?>
                            <div class="cart_item">
                                <form name="item_img_link" action="./item.php">
                                    <a href="javascript:item_img_link<?php if (count($carts) > 1) echo '[' . $i . ']'; ?>.submit()">
                                        <div class="cart_item_img_bg"><img src="<?=h(ITEM_IMAGE_PATH . $carts[$i]['image'])?>" alt="商品"></div>
                                    </a>
                                     <input type="hidden" name="item_id" value="<?=h($carts[$i]['item_id'])?>">
                                </form>
                                <div class="cart_item_description">
                                    <form name="item_name_link" action="./item.php">
                                        <p class="cart_item_name"><a href="javascript:item_name_link<?php if (count($carts) > 1) echo  '[' . $i . ']'; ?>.submit()"><?=h($carts[$i]['name'])?></a></p>
                                        <input type="hidden" name="item_id" value="<?=h($carts[$i]['item_id'])?>">
                                    </form>
                                    <p><span class="font_price"><?=h(number_format($carts[$i]['price']))?>円</span>(税込)</p>
                                    <form class="cart_amount_set" method="post" action="./cart_change_amount.php">
                                        数量：
                                        <select class="amount_box<?php if($carts[$i]['amount'] > $carts[$i]['stock']) echo ' box_err'; ?>" name="amount" onchange="submit(this.form);">
<?php for ($j = 1; $j <= MAX_AMOUNT; $j++) : ?>
                                            <option value="<?=h($j)?>" <?php if ($j === intval($carts[$i]['amount'])) echo 'selected'; ?>><?=h($j)?></option>
<?php endfor ?>
                                        </select>
                                        （在庫数：<?=h($carts[$i]['stock'])?>)
                                        <input type="hidden" name="item_id" value="<?=h($carts[$i]['item_id'])?>">
                                        <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                                    </form>
                                    <form name="delete_cart_item" method="post" action="./cart_delete_cart.php">
                                        <p><a href="javascript:delete_cart_item<?php if (count($carts) > 1) echo '[' . $i . ']'; ?>.submit()">削除する</a></p>
                                        <input type="hidden" name="item_id" value="<?=h($carts[$i]['item_id'])?>">
                                        <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                                    </form>
                                </div>
                            </div>
<?php endfor ?>
                        </div>
<?php endif ?>
                    </section>
                </div>
                <div class="right_cart_contents">
                    <section class="contents_section cart_item_total">
                        <form method="post" action="./finish.php">
                            <button type="submit">商品を購入</button>
                            <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                            <div><span class="cart_total_title">合計点数</span><span class="cart_total_amount"><?=h($total_amount)?> 点</span></div>
                            <div><span class="cart_total_title">合計金額</span><span class="cart_total_price"><?=h(number_format($total_price))?> 円(税込)</span></div>
                        </form>
                        <a href="./index.php">商品一覧へ戻る</a>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>