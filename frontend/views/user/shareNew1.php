<?= $this->render('../site/_head') ?><!--引入公共头部-->
<?php common\components\View::regCss('yanzheng.css') ?>
<body style="">
    
    <!--    首页      -->
            <ul class="index-head flex col-w">
                    <li class="le"></li>
                    <li class="mid">我的推广</li>
                    <li class="ri"><?php if(u()->is_manager==-1):?>
                    <a href="<?=url('manager/register')?>" class="col-w" style="display:none">申请</a>
                    <?php else:?>
                    <a href="<?=url('/user/myOffline')?>" class="col-w">用户</a>
                    <?php endif?></li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>



        <!--        推广内容            -->
        <?php if(u()->is_manager==1):?>
        <div class="promote col-2 flex-c2">
            <span class="head flex2"><em><p><i class="iconfont col-2"></i>可提佣金</p><p id="money" class="col-up"><?=$manager==null?"0":floor($manager->rebate_account/100)*100?></p></em><em><button id="extract" class="btn-1 fb">提取</button></em></span>
        </div>
		
		<style>
		.newa{
			border: none;
			font-size: .14rem;
			padding: .05rem .17rem;
			border-radius: 4px;
			margin-right: .125rem;
			margin-top: 0.15rem;
			color: white !important;
			display: block;
		}
		</style>
		<div class="promote col-2 flex-c2">
            <span class="head flex2"><em><p><i class="iconfont col-2"></i>下级用户明细</p></em><em><a id="" class="btn-1 fb newa" href="<?=url('/user/myOffline')?>">查看详情</a></em></span>
        </div>
		
		<div class="promote col-2 flex-c2">
            <span class="head flex2"><em><p><i class="iconfont col-2"></i>下级充值明细</p></em><em><a id="" class="btn-1 fb newa" href="<?=url('/user/outMoney1')?>">查看详情</a></em></span>
        </div>
		<div class="promote col-2 flex-c2">
            <span class="head flex2"><em><p><i class="iconfont col-2"></i>下级提现明细</p></em><em><a id="" class="btn-1 fb newa" href="<?=url('/user/insideMoney1')?>">查看详情</a></em></span>
        </div>
		<div class="promote col-2 flex-c2">
            <span class="head flex2"><em><p><i class="iconfont col-2"></i>下级订单明细</p></em><em><a id="" class="btn-1 fb newa" href="<?=url('/user/transDetail1')?>">查看详情</a></em></span>
        </div>
		
		
		
		
        <div class="promote-content flex-c">
            <div>
                <ul class="head flex">
                    <li><p class="col-2">佣金比例</p><em class="col-1"><?=$manager==null?"0":$manager->point ?>%</em></li>
                    <li><p class="col-2">我的用户</p><em class="col-1"><?=$mpnum ?></em></li>
                    <li><p class="col-2">交易手数</p><em class="col-1"><?=$mdnum ?></em></li>
                </ul>
            </div>
        </div>
		
		<div class="promote-content flex-c">
            <div>
                <ul class="head flex">
                    <li><p class="col-2">总充值</p><em class="col-1"><?=$totalrecharge ?></em></li>
                    <li><p class="col-2">总提现</p><em class="col-1"><?=$totalwithdraw ?></em></li>
                    <li><p class="col-2">总盈亏</p><em class="col-1"><?=$totalyk ?></em></li>
					<li><p class="col-2">总手续费</p><em class="col-1"><?=$totalfee ?></em></li>
                </ul>
            </div>
        </div>
        <div class="line"></div>
        
        
        
        <div class="promote-img">
            <img src="<?=$img?>" width="100%">
            <p class="cent col-2">长按保存二维码，发送给好友</p>
        </div>
        
        <div class="promote-copy">
            <input id="foo" value="<?=$url?>" readonly="">
            <button class="copyBtn" data-clipboard-target="#foo">复制</button><span class="col-2">点击按钮分享你的专属链接</span>
        </div>
        <?php endif?>
        <?php if(u()->is_manager==-1):?>
           <!-- <div class="head flex"><a href="<?=url('manager/register')?>" style="font-size:15px;color:blue">点击申请代理</a></div>-->
            <?php endif?>
            <br>
            <div class="content" style="font-size:15px">
                <ul>
                    <li class="flex"><span>1.</span><em>获得最新行情信息</em></li>
                    <br>
                    <li class="flex"><span>2.</span><em>可以推广客户</em></li>
                    <br>
                    <li class="flex"><span>3.</span><em>交易获得佣金</em></li>
                <ul style="font-size:15px;display:none">
                    <li class="flex"><span>1.</span><em>获得最新行情信息</em></li>
                    <br>
                    <li class="flex"><span>2.</span><em>可以推广客户</em></li>
                    <br>
                    <li class="flex"><span>3.</span><em>交易获得佣金</em></li>
                    
                </ul>
            </div>
        </div>
         <div class="promote-way pr">
            <dl>
            <dt>推广渠道</dt>
            <dd class="d1">朋友推广</dd>
            <dd class="d2">微信微博</dd>
            <dd class="d3">QQ空间</dd>
            <dd class="d4">博客论坛</dd>
            <dd class="d5">自主网站</dd>
            </dl><div style="clear:both;"></div>
        </div>
    <?php if(u()->is_manager!=1):?>
    <?php if(u()->apply_state == 1 || u()->apply_state == -1):?>

            <div style="width: 100%;display: flex;justify-content: center;height: 20px;"></div>
            <button class="toma" style="margin-left: 40%;padding: 5px 5px;" href="">一键申请成为经纪人</button>
    <?php else:?>
            <div style="width: 100%;display: flex;justify-content: center;height: 20px;"></div>
            <button disabled class="toma" style="margin-left: 40%;padding: 5px 5px;" href="">申请审核中...</button>
        <?php endif;?>

    <?php endif;?>
    <script>
        $('.toma').click(function () {
               $.post("/user/manager",{},function (res) {
                   if (res.state === true){
                       alert('申请成功');
                       location.reload()
                   }
               })
        })
    </script>
<?= $this->render('../site/_foot') ?><!--引入公共尾部-->

        <script>
            $(function(){
                //init
                var clipboard = new Clipboard(".copyBtn");
                //优雅降级:safari 版本号>=10,提示复制成功;否则提示需在文字选中后，手动选择“拷贝”进行复制
                clipboard.on('success', function(e) {
                     layer.open({
                        content: '复制成功'
                        ,skin: 'msg'
                        ,time: 1.5 //2秒后自动关闭
                      });
                    e.clearSelection();
                });
                clipboard.on('error', function(e) {
                    layer.open({
                        content: '请选择“拷贝”进行复制!'
                        ,skin: 'msg'
                        ,time: 1.5 //2秒后自动关闭
                      });
                });
                
                var money = $('#money').text();
                $('#extract').on('click', function(){
                    if(money < 100){
                        layer.open({
                            content: '只能提取100的整数倍！'
                            ,skin: 'msg'
                            ,time: 1.5 //2秒后自动关闭
                          });
                        return false;
                    }
                    $.ajax({
                        type: 'post',
                        url: 'ex-with-draw',
                        data: {
                            money: money
                        },
                        success: function(data){
                            layer.open({
                                content: data.info,
                                btn: '确定',
                                yes: function(index){
                                    layer.close(index)
                                }
                            })
                        }
                    })
                })
                
                
            })
                
        
        </script>
    

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