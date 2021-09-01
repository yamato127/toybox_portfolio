<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 購入完了</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper_700">
                <section class="contents_section purchase_finish">
                    <div class="center finish_top">
                        <h2>購入結果</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                        <p>商品を購入しました</p>
                        <p>お買い上げありがとうございます</p>
                        <p>合計点数&emsp;<?=h($finish_total_amount)?>点</p>
                        <p>合計金額&emsp;<?=h(number_format($finish_total_price))?>円(税込)</p>
                    </div>
                    <div class="flex_column">
<?php for ($i = 0; $i < count($carts); $i++) : ?>
                        <div class="finish_item">
                            <div class="finish_item_img_bg"><img src="<?=h(ITEM_IMAGE_PATH . $carts[$i]['image'])?>" alt="商品"></div>
                            <div class="finish_item_description">
                                <p class="finish_item_name"><?=h($carts[$i]['name'])?></p>
                                <p><span><?=h(number_format($carts[$i]['price']))?>円</span>(税込)</p>
                                <p>数量：<?=h($carts[$i]['amount'])?></p>
                            </div>
                        </div>
<?php endfor ?>
                    </div>
                    <ul class="link_list">
                        <li><a href="./index.php">商品一覧へ戻る</a></li>
                        <li><a href="./logout.php">ログアウト</a></li>
                    </ul>
                </section>
            </div>
        </main>
    </body>
</html>