<?php use common\helpers\Html; 
use admin\models\AdminUser;?>
<?php admin\assets\MainAsset::register($this) ?>
<?php self::offEvent(['debug']) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl">
            <a class="logo navbar-logo f-l mr-10 hidden-xs" href="">管理系统</a>
            <a class="logo navbar-logo-m f-l mr-10 visible-xs" href="">管理系统</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs"></span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>

            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li><?= current(admin\models\AdminUser::roles(u('id'))) ?></li>
                    <li class="dropDown dropDown_hover">
                        <a href="javascript:;" class="dropDown_A"><?= u()->realname ?> <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" data-title="修改密码" onclick="Hui_admin_tab(this)" _href="<?= url(['site/profile']) ?>">修改密码</a></li>
                            <?php if (u()->power <= AdminUser::POWER_ADMIN): ?>
                            <li><a href="javascript:;" data-title="个人信息" onclick="Hui_admin_tab(this)" _href="<?= url(['site/userInfo']) ?>">个人信息</a></li>
                            <?php endif ?>
                            <li><a href="<?= url(['site/login']) ?>">切换账户</a></li>
                            <li><a href="<?= url(['site/logout']) ?>">退出</a></li>
                        </ul>
                    </li>

                </ul>
            </nav>
        </div>
    </div>
</header>
<aside class="Hui-aside">
    <input runat="server" id="divScrollValue" type="hidden" value="" />
    <div class="menu_dropdown bk_2 menuList">
    <?php $menuData = admin\models\AdminMenu::showMenu() ?>
    
    <?php foreach ($menuData as $parent): ?>
        <?php if ($parent['pid'] == 0): ?>
            <?php
                $html = '';
                foreach ($menuData as $child) {
                    if ($child['pid'] == $parent['id'] && u()->can($child['url'])) {
                        $html .= '<li><a _href="' . url($child['url']) . '" data-title="' . $child['name'] . '" href="javascript:;">' . $child['name'] . '</a></li>';
                    }
                }
                if (!$html) {
                    continue;
                }
            ?>
        <dl>
            <dt><?= $parent['icon'] ?> <span><?= $parent['name'] ?></span><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul><?= $html ?></ul>
            </dd>
        </dl>
        <?php endif ?>
    <?php endforeach ?>
    </div>
</aside>

<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:;" onClick="displaynavbar(this)"></a></div>

<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active"><span title="交易所" data-href="<?= url(['welcome']) ?>">我的面板</span><em></em></li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group">
            <a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;">
                <i class="Hui-iconfont">&#xe6d4;</i>
            </a>
            <a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;">
                <i class="Hui-iconfont">&#xe6d7;</i>
            </a>
        </div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div style="display:none" class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="<?= url(['welcome']) ?>" data-maintitle="交易所" data-subtitle="我的面板"></iframe>
        </div>
    </div>
</section>
<script>
$(function () {
    // var timer;
    // $('.menuList dt').hover(function () {
    //     var $this = $(this);
    //     timer = setTimeout(function () {
    //         if ($this.next().css('display') === 'none') {
    //             $this.trigger('click');
    //         }
    //     }, 500);
    // }, function () {
    //     clearTimeout(timer);
    // });
});
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>