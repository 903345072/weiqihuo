<?= $this->render('../site/_head') ?><!--引入公共头部-->
<body style="">
    <style>
	.tit{
		    height: 100%;
    color: #fff;
    font-size: 15px;
    line-height: 50px;
	}
	</style>
    <!--    首页      -->
            <ul class="index-head flex col-w">
                    <li class="le col-xs-3 tit"><a style="color:#fff" href="/user/my-offline">返回</a></li>
                    <li class="mid">详情</li>
                    <li class="ri"></li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>
        
        <!--        推广内容            -->
		
		<div class="promote-content flex-c">
            <div>
                <ul class="head flex">
                    <li><p class="col-2">昵称</p><em class="col-1"><?=$user->nickname ?></em></li>
                    <li><p class="col-2">手机号码</p><em class="col-1"><?=$user->mobile ?></em></li>
                    
                </ul>
            </div>
        </div>
		
		
        <div class="promote-content flex-c">
            <div>
                <ul class="head flex">
                    <li><p class="col-2">总余额</p><em class="col-1"><?=$user->account ?></em></li>
                    <li><p class="col-2">总充值</p><em class="col-1"><?=$totalrecharge ?></em></li>
					<li><p class="col-2">总提现</p><em class="col-1"><?=$totalwithdraw ?></em></li>
                    
                </ul>
            </div>
        </div>
		
		<div class="promote-content flex-c">
            <div>
                <ul class="head flex">
                    
                    <li><p class="col-2">交易手数</p><em class="col-1"><?=$mdnum ?></em></li>
                    <li><p class="col-2">总盈亏</p><em class="col-1"><?=$user->profit_account + $user->loss_account ?></em></li>
					<li><p class="col-2">总手续费</p><em class="col-1"><?=$totalfee ?></em></li>
                </ul>
            </div>
        </div>
        <div class="line"></div>
        
        </div>

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