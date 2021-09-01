<header>
    <div class="wrapper header_content">
        <div class="header_left_content">
            <h1 class="logo"><a href="<?=h(HOME_URL)?>"><img src="<?=h(IMAGE_PATH . 'toybox_logo.png')?>" alt="TOYBOX"></a></h1>
            <div class="welcome_message">
                <p>ようこそ、<?=$login_user['user_name']?>さん</p>
                <a href="<?=h(LOGOUT_URL)?>">ログアウト</a>
            </div>
        </div>
        <a href="<?=h(CART_URL)?>">
            <div class="link_cart">
                    <img src="<?=h(IMAGE_PATH . 'icon_cart.svg')?>" height="40px" width="40px"></img>
                    <div class="circle_cart"><?=$total_amount?></div>
            </div>
        </a>
    </div><!-- /.wrapper -->
</header>