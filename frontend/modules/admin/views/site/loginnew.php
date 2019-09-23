<?php admin\assets\LoginAsset::register($this) ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>管理系统</title>
    <script src="/loginadmin/js/jquery.js"></script>
    <script src="/loginadmin/js/common.js"></script>
    <script src="/loginadmin/layer/layer.js"></script>
    <script src="/loginadmin/js/jquery.form.js"></script>
    <link href="/loginadmin/css/bootstrap.min.css" rel="stylesheet">
    <link href="/loginadmin/css/login.css" rel="stylesheet">
    <script src="/loginadmin/js/bootstrap.min.js"></script>
    <style>
   label{
    display: none;
   }
   .formControls{
    width:100%;
   }
</style>
</head>
<body>

<img src="/images/login.png" width="100%" height="100%" style="z-index:-100;position:absolute;left:0;top:0">
			<div align="center">
             <img  alt="" src="/test/123.png" style="width:229px;height:220px;">
        </div>
    <div class="container main">
        <div class="container content">
            <div class="title">
                <h1 class="text-center">管理系统</h1>
            </div>
            <div id="output2"></div>
            <div class="container-fluid formed">
                 <?php $form = self::beginForm(['class' => ['text-center']]) ?>
                    <?= $form->field($model, 'username')->textInput(['placeholder' => '用户名'])?>
                     <?= $form->field($model, 'password')->textInput(['type'=>'password','placeholder' => '密码'])?>
                 <?= $form->submit('登 录') ?>
                 <?php self::endForm() ?>
            </div>
        </div>
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

</body>
</html>

