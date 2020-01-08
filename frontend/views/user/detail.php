<?php common\components\View::regCss('login.css') ?>
<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<?php use frontend\models\Order;?>
<script type="text/javascript">
    //iOS Web APP中点击链接跳转到Safari 浏览器新标签页的问题 devework.com
    //stanislav.it/how-to-prevent-ios-standalone-mode-web-apps-from-opening-links-in-safari
    if(("standalone" in window.navigator) && window.navigator.standalone){
        var noddy, remotes = false;
        document.addEventListener('click', function(event) {
            noddy = event.target;
            while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
                noddy = noddy.parentNode;
            }
            if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes))
            {
                event.preventDefault();
                document.location.href = noddy.href;
            }
        },false);
    }
</script>
<style type="text/css">body{background: #191919;font-size: 1.6rem;}</style>
<div class="container">
    <div class="liq_box">
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p>
                        <font class="liq_font">订单号：</font>BX000<?= $order->id ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓时间：</font>
                        <span><?= $order->created_at ?></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">建仓金额:</font>
                        <span id="make"><?= $order->deposit ?></span>￥ </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border">
                        <font class="liq_font">入仓价:</font>
                        <span><?= $order->price ?></span>
                    </div>
                    <div class="col-xs-6">
                        <font class="liq_font">平仓价格：</font>
                        <span><?= $order->sell_price ?></span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left">手续费(每手)：</font>
                        <span class="styled-select">
                    <?= $order->fee / $order->hand ?>￥
                  </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">方向：</font>
                        <?php $style='color:green';$string='跌↓';if ($order->rise_fall == Order::RISE) { $style = 'color:red';$string='涨↑';} ?>
                        <span class="styled-select"><span style="<?= $style ?>"><?= $string ?></span> </span>
                    </div>
                </div>
                <div class="liq_box_hx text-left">
                    <div class="col-xs-6 liq_border liq_borders">
                        <font class="liq_font pull-left">止盈：</font>
                        <span class="styled-select">
                    <?= $order->stop_profit_point ?>%
                  </span>
                    </div>
                    <div class="col-xs-6  liq_borders">
                        <font class="liq_font">止损：</font>
                        <span class="styled-select">
                  <?= $order->stop_loss_point ?>%
                  </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">手续费:</font><?= $order->fee ?>￥
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        <font class="liq_font">盈亏金额:</font>
                        <?php $style='color:green';if ($order->profit > 0) { $style = 'color:red';} ?>
                        <span style="<?= $style ?>"><?= $order->profit ?>￥</span> </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="liq_main">
                <div class="col-xs-12 liq_box_hx">
                    <p class="liq_span">
                        本单盈余:
                        <font class="liq_font" id="endcash" style="<?= $style ?>"><?= $order->sell_deposit ?>￥</font>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('plusready', function() {
    var webview = plus.webview.currentWebview();
    plus.key.addEventListener('backbutton', function() {
        webview.canBack(function(e) {
            if(e.canBack) {
                webview.back();
            } else {
                webview.close(); //hide,quit
                //plus.runtime.quit();
            }
        })
    });
});
</script>
