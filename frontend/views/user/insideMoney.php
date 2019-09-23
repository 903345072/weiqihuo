<?php $this->regCss('jilu.css') ?>
<?php $this->regCss('manager.css') ?>
<?= $this->render('_head') ?><!--引入公共头部-->
<p class="charge-header" style="background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>出金明细</span></p>

<div class="outMoney">
<?= $this->render('_insideMoney', compact('data')) ?>
</div>

<?php if ($pageCount < 2): ?>
    <div class="deta_more" id="deta_more_div">没有更多了</div>
<?php else: ?>
    <div class="addMany" style="text-align: center;margin-top: 60px;">
        <a style="" type="button" value="加载更多" id="loadMore" data-count="<?= $pageCount ?>" data-page="1">加载更多</a>
    </div>
<?php endif ?>
<link rel="stylesheet" href="/test/base1.css?r=20170520">
<link rel="stylesheet" href="/test/main.css?r=20170520">
<link rel="stylesheet" href="/test/main-blue.css?r=20170520">
<link href="/test/layer.css?2.0" type="text/css" rel="styleSheet" id="layermcss">
<?= $this->render('../site/_foot') ?><!--引入公共尾部--> 

<script type="text/javascript">
$(".addMany").on('click', '#loadMore', function() {
    var $this = $(this),
        page = parseInt($this.data('page')) + 1;

    $.get('', {p:page}, function(msg) {
        $(".outMoney").append(msg);
        $this.data('page', page);
        if (page >= parseInt($this.data('count'))) {
            $('.addMany').hide();
        }
    });
});
</script>


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