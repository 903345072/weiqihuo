<?= $this->render('../site/_head') ?><!--引入公共头部-->
  <body>

<ul class="index-head flex col-w">
                <li class="le"></li>
                <li class="mid">个人中心</li>
                <li class="ri"></li>
        </ul>
        <div style="height: .45rem; width: 100%;"></div>
        <user-top>
        <div class="user-bg pr flex-c">
            <div class="user-box">
                <span><img src=<?= u()->face==''?'/test/123.png':u()->face ?> width="100%/"></span>
                <div class="user-name"><?= u()->nickname ?></div>
            </div>
            <div class="user-box2 flex-c">
			    <!--p><em>余额：￥<?= $user->account ?></em></p-->
                <p><em>可用余额：￥<?=$user->account-$user->blocked_account?></em></p>
				<!--li class="col-1">可用资金 <span id="money" class="col-1"><！?=$user->account-$user->blocked_account?></span></li-->
				<div class="user-content">
				<marquee behavior="scroll" contenteditable="true" style="width: 95%;/*background-color: whitesmoke;*/font-size: 12px;" onstart="this.firstChild.innerHTML+=this.firstChild.innerHTML;" scrollamount="3" onmouseover="this.stop();" onmouseout="this.start();"> 
				<?=config('notice')?>
				</marquee>
				</div>
                <!--p><em>保证金：<?= $user->account ?></em></p-->
                <div class="btns">
                    <a href="<?= url(['user/withDraw']) ?>"><button><i class="icon iconfont"></i>提现</button></a>
                    <a href="<?= url(['user/recharge', 'user_id' => u()->id]) ?>"><button><i class="icon iconfont"></i>充值</button></a>
                </div>
            </div>
        </div>
        <!--    user-content     -->
        <div class="user-content">
            <ul>
                <li><a class="col-1" href="<?= url(['user/outMoney']) ?>"><i class="iconfont co5"></i><span>入金明细</span><i class="iconfont"></i></a></li>
                <li><a class="col-1" href="<?= url(['user/insideMoney']) ?>"><i class="iconfont co5"></i><span>出金明细</span><i class="iconfont"></i></a></li>
                <li><a class="col-1" href="<?= url(['user/transDetail']) ?>"><i class="iconfont co4"></i><span>交易记录</span><i class="iconfont"></i></a></li>
                <!--li><a class="col-1" href="#"><i class="iconfont co3"></i><span>优惠券</span><i class="iconfont"></i></a></li-->
            </ul>
            <ul>
                <li><a class="col-1" href="<?= url(['user/share']) ?>"><i class="iconfont co1"></i><span>我的推广</span><i class="iconfont"></i></a></li>
                <li><a class="col-1" href="<?= url(['user/setting']) ?>"><i class="iconfont  co2"></i><span>信息设置</span><i class="iconfont"></i></a></li>
                <li><a class="col-1" href="<?= url(['site/userguide']) ?>"><i class="iconfont  co6"></i><span>交易规则</span><i class="iconfont "></i></a></li>
                <li><a class="col-1" href="<?= url(['user/password']) ?>"><i class="iconfont  co2"></i><span>修改密码</span><i class="iconfont "></i></a></li>
                <li><a class="col-1" href="<?= url(['site/logout']) ?>"><i class="iconfont  co8">&#xe699;</i><span>退出系统</span><i class="iconfont ">&#xe65e;</i></a></li>
            </ul>
			<div class="index-bot-btns" style="padding: 0 0;">
            <div>
                <p><font color="red">交易由纽约商品交易所，香港交易所，新加坡交易所等提供实盘对接</font></p>

            </div>
            <p><font color="red">投资有风险，入市须谨慎</font></p>

            <!--p><font color="red">任何疑问，联系客服</font><?=config('tel')?></p--

        </div>
        </div>

     bottom       -->
                <style>
                    .flex li{
                        border-bottom: none;
                    }
                    .col-3 div{
                        line-height: 0;
                    }
                    .bottom a i{
                        margin-top: -.2rem;
                    }
                    .user-content li{
                        line-height: 45px;
                    }
                </style>
        <div style="height:.54rem;"></div>
                <div style="height:.54rem;"></div>
                <ul id="bottom" class="bottom flex" style="margin-bottom: 0;">
                    <li>
                        <a href="/site/index" class="col-3"><i class="iconfont col-3"></i>
                            <div>首页</div>
                        </a>
                    </li>
                    <li>
                        <a href="/site/list" class="col-3"><i class="iconfont col-3"></i>
                            <div>交易</div>
                        </a>
                    </li>
                    <!--li>
                        <a href="/user/share" class="col-3"><i class="iconfont col-3"></i>
                            <div>推广</div>
                        </a>
                    </li-->
                    <li>
                        <a href="/user/news" class="col-3"><i class="iconfont col-3"></i>
                            <div>资讯</div>
                        </a>
                    </li>
                    <li>
                        <a href="/user/index" class="col-3"><i class="iconfont col-3"></i>
                            <div>我的</div>
                        </a>
                    </li>
                </ul>

  </body></html>
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