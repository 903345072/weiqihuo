<?php $this->regCss('geren.css') ?>

<div class="personal" >
    <p class="charge-header" style="background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>充值</span></p>

     <div style="display: flex;justify-content: center;" class="ewm">
         <?php if($type=='wx'  ):?>
         <img style="width: 80%;height:80%;margin-top: 5%;margin-bottom: 10px;" src="/images/rengong.png" alt="">
<!--         --><?php //else:?>
<!--         <img style="width: 80%;height:80%;margin-top: 5%;margin-bottom: 10px;" src="/images/alipay.jpg" alt="">-->
         <?php endif;?>
     </div>
    <?php if($type=='wx'):?>
        <h3 style="padding-left: 10%">1、截图保存二维码到手机</h3>
    <?php elseif($type=='zz'):?>
        <h3 style="padding-left: 10%">1、收款卡号：6228481389069422570</h3>
        <h3 style="padding-left: 10%">1、收款人姓名：邓渝</h3>
    <?php endif;?>



            <?php if($type=='wx'):?>
                <h3 style="padding-left: 10%">2、微信扫描二维码加客服好友</h3>
<!--            --><?php //else:?>
<!--                <h3 style="padding-left: 10%">2、支付宝扫描二维码支付</h3>-->
            <?php endif;?>
<!--            <h3 style="padding-left: 10%">3、备注个人账号信息</h3>-->
<!--            <h3 style="padding-left: 10%">4、若没有到账及时联系客服(qq:--><?//=config('qq')?><!--)</h3>-->

    <style>
        .onpay{
            border: 1px solid red;
        }
    </style>
</div>
<?= $this->render('../site/_head') ?><!--引入公共头部-->
<?= $this->render('../site/_foot') ?><!--引入公共尾部-->


<script type="text/javascript">
    $(".btn_money").click(function(){
        $('.clearfloat .btn_money').removeClass("active");
        $('#amount').val($(this).html());
        $(this).addClass("active");
    });

    $('#payBtn').on('click', function(){
        $("#payform").submit();
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