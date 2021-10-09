<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 購入明細</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper_700">
                <section class="contents_section order_detail">
                    <div class="center">
                        <h2>購入明細</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                    </div>
                    <div>
                        <p><?=h($order['order_date'])?></p>
                        <p>注文番号：<?=h($order['order_id'])?></p>
                        <p>合計金額：<?=h(number_format($order['total_price']))?>円(税込)</p>
                    </div>
                    <div class="flex_column">
<?php for ($i = 0; $i < count($order_details); $i++) : ?>
                        <div class="order_item">
                            <form name="item_img_link" action="./item.php">
                                <a href="javascript:item_img_link<?php if (count($order_details) > 1) echo '[' . $i . ']'; ?>.submit()">
                                    <div class="order_item_img_bg"><img src="<?=h(ITEM_IMAGE_PATH . $order_details[$i]['image'])?>" alt="商品"></div>
                                </a>
                                    <input type="hidden" name="item_id" value="<?=h($order_details[$i]['item_id'])?>">
                            </form>
                            <div class="order_item_description">
                                <form name="item_name_link" action="./item.php">
                                    <p class="order_item_name"><a href="javascript:item_name_link<?php if (count($order_details) > 1) echo  '[' . $i . ']'; ?>.submit()"><?=h($order_details[$i]['name'])?></a></p>
                                    <input type="hidden" name="item_id" value="<?=h($order_details[$i]['item_id'])?>">
                                </form>
                                <p>単価：<?=h(number_format($order_details[$i]['order_price']))?>円(税込)</p>
                                <p>数量：<?=h($order_details[$i]['amount'])?></p>
                                <p>小計：<?=h(number_format($order_details[$i]['subtotal_price']))?>円(税込)</p>
                            </div>
                        </div>
<?php endfor ?>
                    </div>
                    <ul class="link_list">
                        <li><a href="./index.php">商品一覧へ戻る</a></li>
                    </ul>
                </section>
            </div>
        </main>
    </body>
</html>