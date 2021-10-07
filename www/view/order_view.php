<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 購入履歴</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper_600">
                <section class="contents_section order_list">
                    <div class="center">
                        <h2>購入履歴</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                    </div>
                    <div class="flex_column">
<?php for ($i = 0; $i < count($orders); $i++) : ?>
                        <div class="order">
                            <form method="get" action="order_detail.php">
                                <p><?=h($orders[$i]['order_date'])?></p>
                                <p>
                                    注文番号：<?=h($orders[$i]['order_id'])?>&emsp;
                                    <input type="submit" value="注文詳細を表示">
                                    <input type="hidden" name="order_id" value="<?=h($orders[$i]['order_id'])?>">
                                </p>
                                <p><?=h(number_format($orders[$i]['total_price']))?>円(税込)</p>
                            </form>
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