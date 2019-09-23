<?php $this->regCss('yanzheng.css') ?>
<style>
.mewbtn{
	color: white;
    border: 0;
    padding: 0.5rem;
    border-radius: .3rem;
    line-height: 1.41rem;
    margin-left: 1.54rem;
    width: 8rem;
}
.btn-org{    background-color: #f79e30;}
</style>
<?= $this->render('_head') ?><!--引入公共头部-->
<ul class="index-head flex col-w">
                <li class="le"></li>
                <li class="mid">修改手机号</li>
                <li class="ri"></li>
        </ul>
        <div style="height: .45rem; width: 100%;"></div>
        <user-top>
<div align="center">
<img  alt="" src="/test/123.png" style="width:229px;height:220px;">
</div>
<div class="forget-box">

    <?php $form = self::beginForm(['showLabel' => false]) ?>
    <div class="title">修改手机号</div>
    <div class="content-wrap">
        <?= $form->field($model, 'mobile')->textInput(['placeholder' => '请输入您的手机号码', 'class' => 'textvalue regTel'])  ?>
		<div class="textcode" id="verifyCodeBtn" class="mewbtn "  data-action="<?= url(['site/verifyCode1']) ?>">获取验证码</div>
        <!--<div class="textcode" id="verifyCodeBtn" data-action="<?= url(['site/verifyCode1']) ?>"  class="textvalue yzbtn">获取验证码</div>-->
        <?= $form->field($model, 'verifyCode')->textInput(['placeholder' => '请输入手机验证码', 'class' => 'textvalue regCode'])  ?>
        <p id="errorMsg"></p>
        <a class="btn-sure disabled" id="submitBtn">确定</a>
    </div>
    <?php self::endForm() ?>
</div>

<script>
$(function () {
    var $inputs = $('.regCode');
    $inputs.keyup(function() {
        if ($inputs.val().length > 3) {
            $('#submitBtn').removeClass('disabled');
        } else {
            $('#submitBtn').addClass('disabled');
        }
    });
	var mobileV = /^1[34578]\d{9}$/;
	$('#user-mobile').on('blur', function(){
		var me = $(this);
		if(!mobileV.test($(this).val())){
			
			layer.open({
						content: '手机号格式错误，请重新输入！',
						btn: '确定',
						yes: function(index){
							me.val('');
							me.focus();
							layer.close(index)
						}
					})
		}else{
			verificationData.mobile = true;
			verification()
		}
	})
	$('#user-mobile').on('keyup', function(){
		if(!mobileV.test($(this).val())){
			verificationData.mobile = false;
		}else{
			verificationData.mobile = true;
		}
		verification()
	})
	
	
	var verificationData = {
					mobile: false,	
			};
			
			
			
			
	function verification(){
		var flag = true;
		if(verificationData.mobile){
			$('#verifyCodeBtn').addClass('btn-org');
		}else{
			$('#verifyCodeBtn').removeClass('btn-org');
		}
		
	}
	
	
	
	
	
	
	
	
	
    //倒计时
    var wait = 60;
    function time(obj) {
        if (wait == 0) {
            obj.removeClass('disabled');           
            obj.html('重新获取验证码');
            wait = 60;
        } else {
            obj.addClass('disabled');
            obj.html('重新发送(' + wait + ')');
            wait--;
            setTimeout(function() {
                time(obj);
            },
            1000)
        }
    }
    //提交
    $("#submitBtn").click(function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    window.location.href = msg.info;
                }
            }
        }));
        return false;
    });
    // 验证码
    $("#verifyCodeBtn").click(function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        var mobile = $('.regTel').val();
        var url = $(this).data('action');
        if (mobile.length != 11) {
            $.alert('您输入的不是一个手机号！');
            return false;
        }
		 $.ajax({    
			"type":"POST",    
			"url":url,    
			"data":{mobile: mobile},    
			"success":function(msga){
				if (msga == 1) {
                    time($('#verifyCodeBtn'));
                } else {
                    $.alert('发送失败');
                }
			},    
			
		}); 
return false;		
        /*$.post(url, {mobile: mobile}, function(msg) {
			alert(msg);
			console.log(msg);
                if (msg.state) {
					console.log(1111);
                    time($('#verifyCodeBtn'));
                } else {
                    $.alert(msg.info);
                }
        }, 'json');*/
    });
});
</script>
<?= $this->render('../site/_foot') ?><!--引入公共尾部-->
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