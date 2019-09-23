 <!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
        <meta name="keywords" content="股指期货,外汇,手机版软件,期货,贵金属,财经,白手理财,投资">
        <meta name="description" content="<?=config('web_name')?>,纵览行情，明确策略，实时专家互动，知晓天下财经">
        <title><?=config('web_name')?></title>
        <link rel="stylesheet" href="/test/base.css?r=20170520">
        <link rel="stylesheet" href="/test/main.css?r=20170520">
        <link rel="stylesheet" href="/test/main-blue.css?r=20170520">
        <script src="/test/jquery.js"></script>
        <script src="/test/public.js"></script>
        <script src="/test/clipboard.min.js"></script>
        <script src="/test/fastclick.js"></script>
        <script src="/loginadmin/js/common.js"></script>
    
    <!--layer-->
    <script src="/loginadmin/layer/layer.js"></script>
    <script src="/loginadmin/js/jquery.form.js"></script>
        <script>
            $(function() {  
                FastClick.attach(document.body);  
            });  
        </script>
</head>
<img src="/images/login.png" width="100%" height="100%" style="z-index:-100;position:absolute;left:0;top:0">
	<body style="max-width:640px;margin:auto">
		<style>
			.ul-box li input{
				padding-left:2%;
				width:100%;
				height:30px;
				line-height:30px;
			}
		</style>
		<div class="register-all">
			<ul class="index-head flex col-w" style="display: none;">
					<li class="le"></li>
					<li class="mid">登录</li>
					<li class="ri"><a class="col-w" href="<?=url('/site/reg')?>"></a></li>
			</ul>
			<div style="height: .45rem; width: 100%;"></div>
			<div class="row">
                <div class="col-xs-12">
                    <div class="logo_img" style="padding: 20px 10px 70px 10px;width: 40%;margin: auto;">
                        <img style="width:100%" src="<?= config('web_logo') ?>" alt="<?= config('web_name') ?>">
                    </div>
                </div>
            </div>
            <?php $form = self::beginForm(['showLabel' => false]) ?>
			<ul class="ul-box first sign">
				<li>
					
                    <?= $form->field($model, 'username')->textInput(['placeholder' => '手机号']) ?>
				<!--<input id="mobile" class="col-1" type="tel" maxlength="30" placeholder="请输入手机号" style="width:80%">-->
				</li>
				<li>
				
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码']) ?>
				<!--<input id="password" class="col-1" type="password" maxlength="30" placeholder="请输入密码" style="width:80%">-->
				</li>
			</ul>
			<div class="cent">
				<button id="login" class="confi-btn2 btn-3">登录</button>
				<p style="margin-top: .2rem;"><a class="confi-btn btn-0" href="<?=url('/site/reg')?>">注册开户</a></p>
				<p style="margin-top: .2rem;"><a class="confi-btn btn-0" href="<?=url('/site/passForGet')?>">忘记密码</a></p>
			</div>
            <?php self::endForm() ?>
		</div>
		
	
	
	<script>
		 $(document).ready(function() { 
    var options = { 
        dataType: "json",
        success: function (data) {

            $.alert(data.info);
        
        } 
    }; 
 
    
    $('#loginForm').submit(function() { 
      
        $(this).ajaxSubmit(options); 

        return false; 
    }); 
}); 
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