<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
        <title>TOY☆BOX - 商品一覧</title>
        <link rel="stylesheet" href="<?=h(STYLESHEET_PATH . 'style_main.css')?>">
    </head>
    <body>
<?php include VIEW_PATH . 'templates/header_logined.php'; ?>
        <main>
            <div class="wrapper item_contents">
                <div class="left_item_contents">
                    <section class="contents_section search_form">
                        <form method="get" action="./index.php">
                            <h2 class="search_title center">商品検索</h2>
<?php foreach($_GET as $key => $value) : ?>
<?php if($key !== 'keyword' && $key !== 'category' && $key !== 'favorite') : ?>
                            <input type="hidden" name="<?=h($key)?>" value="<?=h($value)?>">
<?php endif ?>
<?php endforeach ?>
                            <div class="search_set right">
                                <input type="search" class="input_box" name="keyword" value="<?=h($keyword)?>" placeholder="キーワード">
                                <button type="submit" class="box_button">検索</button>
                            </div>
                            <p>条件を指定する</p>
                            <div class="search_set">
                                <p>カテゴリ：</p>
                                <select class="input_box" name="category">
                                    <option value="">すべて</option>
<?php foreach(array_keys(PERMITTED_TOY_CATEGORIES) as $category): ?>
                                    <option value="<?=h($category)?>"<?php if($category === $selected_category) echo ' selected'; ?>><?=h($category)?></option>
<?php endforeach ?>
                                </select>
                            </div>
                            <div class="search_set">
                                <label>
                                    <input type="hidden" name="favorite" value="0">
                                    <input type="checkbox" name="favorite" value="1" <?php if ($favorite === '1') echo 'checked'; ?>>
                                    お気に入りのみ
                                </label>
                            </div>
                        </form>
                    </section>
                </div>
                <div class="right_item_contents">
                    <section class="contents_section item_list">
                        <h2>商品一覧</h2>
<?php include VIEW_PATH . 'templates/messages.php'; ?>
                        <form class="sort_item" method="get" action="./index.php">
                            <span>並べ替え：</span>
<?php foreach($_GET as $key => $value) : ?>
<?php if($key !== 'sort_type') : ?>
                            <input type="hidden" name="<?=h($key)?>" value="<?=h($value)?>">
<?php endif ?>
<?php endforeach ?>
                            <select class="input_box" name="sort_type" onchange="submit(this.form);">
<?php foreach (array_keys(PERMITTED_SORT_TYPES) as $sort_type) : ?>
                                <option value="<?=h($sort_type)?>"<?php if($sort_type === $selected_sort_type) echo ' selected'; ?>><?=h($sort_type)?></option>
<?php endforeach ?>
                            </select>
                        </form>
                        <div class="grid">
<?php if (count($items) === 0) : ?>
                            <p>商品が見つかりません</p>
<?php endif ?>
<?php for ($i = 0; $i < count($items); $i++) : ?>
                            <div class="item">
                                <form name="item_link" action="./item.php">
                                    <a href="javascript:item_link<?php if (count($items) > 1) echo '[' . $i . ']'; ?>.submit()">
                                        <div class="item_img_bg"><img src="<?=h(ITEM_IMAGE_PATH . $items[$i]['image'])?>" alt="商品"></div>
                                    </a>
                                    <p class="item_name"><a href="javascript:item_link<?php if (count($items) > 1) echo  '[' . $i . ']'; ?>.submit()"><?=h($items[$i]['name'])?></a></p>
                                    <input type="hidden" name="item_id" value="<?=h($items[$i]['item_id'])?>">
                                </form>
                                <p><span class="font_price"><?=h(number_format($items[$i]['price']))?>円</span>(税込)</p>
<?php if ($items[$i]['stock'] > 0) : ?>
                                <form class="add_cart" method="post" action="./index_add_cart.php">
<?php foreach($_GET as $key => $value) : ?>
                                <input type="hidden" name="<?=h($key)?>" value="<?=h($value)?>">
<?php endforeach ?>
                                    <button type="submit">カートに追加</button>
                                    <input type="hidden" name="item_id" value="<?=h($items[$i]['item_id'])?>">
                                    <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                                </form>
<?php else : ?>
                                <p class="sold_out">売り切れ</p>
<?php endif ?>
                            </div>
<?php endfor ?>
                        </div>
                    </section>
                    <section class="contents_section item_ranking">
                        <h2>売れ筋ランキング</h2>
                        <ul class="grid">
<?php for ($i=0; $i<3; $i++) : ?>
                            <li>
                                <img src="<?=h(IMAGE_PATH . 'ranking' . ($i + 1) .'.png')?>" alt="">
                                <div class="item">
                                    <form name="ranking_link" action="./item.php">
                                        <a href="javascript:ranking_link<?php if (count($ranking_all) > 1) echo '[' . $i . ']'; ?>.submit()">
                                            <div class="item_img_bg"><img src="<?=h(ITEM_IMAGE_PATH . $ranking_all[$i]['image'])?>" alt="商品"></div>
                                        </a>
                                        <p class="item_name"><a href="javascript:ranking_link<?php if (count($ranking_all) > 1) echo  '[' . $i . ']'; ?>.submit()"><?=h($ranking_all[$i]['name'])?></a></p>
                                        <input type="hidden" name="item_id" value="<?=h($ranking_all[$i]['item_id'])?>">
                                    </form>
                                    <p><span class="font_price"><?=h(number_format($ranking_all[$i]['price']))?>円</span>(税込)</p>
<?php if ($ranking_all[$i]['stock'] > 0) : ?>
                                    <form class="add_cart" method="post" action="./index_add_cart.php">
<?php foreach($_GET as $key => $value) : ?>
                                        <input type="hidden" name="<?=h($key)?>" value="<?=h($value)?>">
<?php endforeach ?>
                                        <button type="submit">カートに追加</button>
                                        <input type="hidden" name="item_id" value="<?=h($ranking_all[$i]['item_id'])?>">
                                        <input type="hidden" name="csrf_token" value="<?=h($csrf_token)?>">
                                    </form>
<?php else : ?>
                                    <p class="sold_out">売り切れ</p>
<?php endif ?>
                                </div>
                            </li>
<?php endfor ?>
                        </ul>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>