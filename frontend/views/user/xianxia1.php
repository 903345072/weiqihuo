<?php $this->regCss('geren.css') ?>

<div class="personal" >
    <p class="charge-header" style="background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>充值</span></p>

     <div style="display: flex;flex-direction: column;padding: 20px;20px;line-height: 50px;" class="ewm">
             <div style="font-size: 25px;">收款信息</div>
             <div class="ws" style="font-size: 20px;">收款名：邓渝</div>
             <div class="ws"  style="font-size: 20px;">开户行：中国农业银行</div>
             <div class="ws"  style="font-size: 20px;">卡号：6228481389069422570</div>
     </div>



        <div style="background: #b1bdbd;width: 90%;margin-left: 5%;margin-top: 10%; box-shadow:2px 2px 5px #333333;color: white;border-radius: 10px;">
             <p class="info" style="font-size:20px;padding-top: 15px;padding-bottom: 20px;">中国农业银行</p>
             <p class="info" style="padding-bottom: 20px;font-size: 25px;">6228481389069422570</p>
             <p class="info" style="font-size: 20px;" >姓名</p>
             <p class="info" style="padding-bottom: 20px;font-size: 25px;">邓渝</p>
        </div>



    <style>
        .onpay{
            border: 1px solid red;
        }
        .info{
            margin-left: 10px;

        }
        .ws{
            color: #3b4249;
        }
    </style>
</div>
<?= $this->render('../site/_head') ?><!--引入公共头部-->
<?= $this->render('../site/_foot') ?><!--引入公共尾部-->


