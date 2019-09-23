<?php use common\helpers\Html; ?>
<?php use common\helpers\System; ?>
<html>
    <head>
 
        <title>微交所</title>
    </head>
			<div align="center">
             <img  alt="" src="/test/123.png" style="width:229px;height:220px;">
        </div>
    <body bgcolor="#cccccc" style="filter:progid:DXImageTransform.microsoft.gradient(gradienttype=1,startColorStr=blue,endColorStr=skyblue)">
 
        <div id="myDiv" style="text-align:center;margin:50px auto;">欢迎您的到来!!</div>
        <script>
 
            function start1() {
 
                document.getElementById("myDiv").style.color = "red";
 
                setTimeout("start2()", 100);//延时100毫秒执行下一个
 
            }
 
            function start2() {
 
                document.getElementById("myDiv").style.color = "blue";
 
                setTimeout("start3()", 100);
 
            }
 
            function start3() {
 
                document.getElementById("myDiv").style.color = "#BFAD09";
 
                setTimeout("start4()", 100);
 
            }
 
            function start4() {
 
                document.getElementById("myDiv").style.color = "#009688";
 
                setTimeout("start1()", 100);
 
            }
 
            start1();//执行一遍函数start1进入循环
 
        </script>
    </body>
</html>