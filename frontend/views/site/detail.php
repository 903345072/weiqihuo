<?= $this->render('_head') ?><!--引入公共头部-->
  <body>
    <style>
      .nav-img{
        width:30px;
        height:30px;
        background:#136aab;
        border-radius:50%;
        font-size: .35rem;
    display: inline-block;
        margin-top:.06rem;
      }
      .nav-img img{
         width:20px;
        display:block;
        margin:5px;
      }
      .scroll-msg, .scroll-msges{
         height:35px;
        overflow:hidden;
        background:white;
        margin-top:10px;
      }
      .scroll-msg ul li, .scroll-msges ul li{
         margin-top:8px;
         margin-left:10px;
         font-size:14px
      }
      .register-box{
        width:50%;
        height:40px;
        line-height:40px;
        background:black;
        opacity:.7;
        position:fixed;
        bottom:.54rem;
      }
     .register-box span{
        color:white;
      }
      .reg-msg{
        margin-left:5%;
      }
      .reg-button{
        margin-right:5%;
        background:#6e8dea;
        height:30px;
        line-height:30px;
        border-radius:3px;
        padding:0 10px;
        margin-top:4px;
        position:fixed;
        bottom:64px;
        right:-2px;
        z-index:999;
        
      }
      .reg-button a{
        color:white;
      }
      .close-reg{
         border-radius:50%;
        width:15px;
        height:15px;
        display:inline-block;
        background:#6b6b6b;
        text-align:center;
        float:left;
        margin-top:12px;
        line-height:14px;
        margin-left:5%;
      }
      .flex2 .col-3{
        font-size:.08rem!important;
      }
      .index-bot-btns{
        clear:both;
      }
      .index-hot li{
        height:auto!important;
      }
      #pro li{
        width:33%;
        height:106px;
        float:left;
        overflow: hidden;
        white-space: nowrap;
        max-width: 100%;
        text-overflow: ellipsis;
        background:white;
        border-right:1px solid #efeff6;
        border-bottom:1px solid #e0e5e5;
        padding:0;
        text-align:center;
      }
      #pro li .flex2{
       display:block;
      }
      #pro span p{
        display:block;
        text-align:center;
      }
      #pro span p+p{

      }
      #pro .col-3{
        height:18px;
        overflow: hidden;
   	    white-space: nowrap;
        max-width: 100%;
        text-overflow: ellipsis;
      }
      #pro{
        background:white;
        overflow:hidden;
      }
      .index-hot .ri p{
       margin-top:0!important;
        text-align:center;
      }
      #pro li+li+li+li+li+li+li{

      }
        .new-left{
    width:62%;
    float:left;
  }
  .new-right{
    width:110px;
    float:right;
  }
  .new-right img{
    width:100%;
  }
  .new-list li{
    padding:9px 10px;
    border-bottom:1px solid #efeff4;
    overflow:hidden;
    background:white;
    height:auto;
  }
  .new-list .new-msg{
    font-size:16px;
    display: -webkit-box ;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-all;
    -webkit-box-orient: vertical;
    -webkit-line-clamp:2;
    margin-bottom:7px;
    color:#000;
  }
  .new-left p{
   color:#666;
  }
      .new-show{
      overflow:hidden;
        border-bottom:1px solid #efeff4;
        padding:10px 20px;
        border-top:7px solid #efeff4;
        background:white;
      }
      .new-deita{
       position:fixed;
       left:0;
        top:0;
        right:0;
        bottom:0;
        display:none;
        background:white;
        z-index:9999;
        overflow:scroll;
      }
      .new-deita h3{
        font-size:20px;
      }
      .new-cen{
        padding:0 15px;
        height:auto;
      }
      .new-deita p{
       margin:5px 0;
      }
      .new-centents{
        font-size:14px;
        text-indent:20px;
      }
      .box-none{
        display:none;
      }

    </style>
 
    <!--    首页      -->
    <!--固定头部-->
            <ul class="index-head flex col-w">
                    <li class="le" style="width:1.2rem"></li>
                    <li class="mid" style="color:#d2dc4e;"><?=config('web_name')?></li>
                    <li class="ri" style="width:1.2rem">
                                <a class="col-w  f-ri" style="padding-right:.1rem" href="/user"><?=u()->nickname ?></a>
                    </li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>


    
       

        <!--            首页热门        -->
        <div class="index-hot" style="box-shadow: 2px 5px 5px #888888;font-size: 14px">
          <div class="hot-show" style="color:red;overflow:hidden;border-bottom:1px solid #efeff4;padding:10px 3%;display: flex;justify-content: space-around">
            <div class="hot-show_msg" >热门交易</div>
              <div  class="hot-show_msg" >最新价</div>
              <div class="hot-show_msg" >涨幅</div>
<!--            <div class="hot-more" style="float:right">-->
<!--              <a href="/site/list">-->
<!--              查看更多<i class="iconfont"></i>-->
<!--              </a>-->
<!--            </div>-->
          </div>
            <!-- 商品列表 -->
            <ul id="pro">

                <?php foreach ($productArr as $key => $value): ?>
                        <li style="width: 100%;padding: 8.45% 20px;position: relative;" class="ddata" data-pro-no="<?= $value['table_name'] ?>">
                            <a style="display: flex;align-items: center" class="flex2" href="/site/detail?pid=<?=$value['id'] ?>">
                              <?php $class='active';if ($value['price'] > $value['close']){ $class = '';}?>
                              <p style="font-size:17px;position: absolute;left: 12%;top: 18%;" class="col-1 status"><?= $value['name'] ?></p>

                            <!--  <?= $value['table_name'] ?>-->
                                <p style="color:#ccc;position: absolute;left: 12%;top:50%;font-size: 19px;"><?= $value['table_name'] ?></p>
                              <span style="display: flex;align-items: center;flex: 1" class="ri flex2 <?=$class ?>">
                                    <p style="position: absolute;left: 48%" class="t_data"><?=number_format($value['price'],2,".","") ?></p>
                                    <p   style="font-size:15px;position: absolute;right:7%;height:25px;width:15%;line-height:25px;background-color: <?php if($value['diff_rate']>0){echo '#f34b60';} ?>"><?=$value['diff_rate'] ?>%</p>
                              </span>
                            </a>

                        </li>

                <?php endforeach ?>
            </ul>
        </div>
        <?= $this->render('_foot') ?><!--引入公共底部-->
<script src="/test/swiper.min.js"></script>
<script src="/test/jquery.cookie.js"></script>
<script>
   //判断是否停市
  setInterval(function() {
			var lis = document.getElementsByClassName('ddata')
			for(var k = 0; k < lis.length; k++) {
				if($('.ddata').eq(k).find('a').find('span').find('.t_data').css('color') == 'rgb(41, 206, 146)' || $('.ddata').eq(k).find('a').find('span').find('.t_data').css('color') == 'rgb(243, 75, 96)') {
					$('.box-none').css({
						'display': 'none'
					})
					$('.box-block').css({
						'display': 'block'
					})
                    console.log(1)
					break;
				} else {
					$('.box-none').css({
						'display': 'block'
					})
					$('.box-block').css({
						'display': 'none'
					})
					console.log(2)
				}
			}

		}, 1000)




 // 新闻动画
   $('.new-deita').css({
     'margin-top':$('.new-deita').height()
   })
   $('.le i').click(function(){
     $('.new-deita').css({
      'margin-top':$('.new-deita').height(),
       'display':'none'
     })
   })
    $('.new-list li').click(function(){
       $('.new-deita').eq($(this).index()).css('display','block').animate({'margin-top':'0'},800)
    })



       $('.close-reg').click(function(){
           $('.reg-button').css('display','none')
           $('.register-box').css('display','none')
          $('.new-deita').css({
     'margin-top':$('.new-deita').height()
   })
       })

        // 向上滚动信息
       function scrollmsg(){
       $('.scroll-msg ul').animate({
       	marginTop:-28
       },1000,function(){
       	$('.scroll-msg ul').css({marginTop:0})
        $('.scroll-msg ul').append($('.scroll-msg ul li').first())
       })
    }
	 setInterval(scrollmsg,3000)

   function scrollmsges(){
       $('.scroll-msges ul').animate({
       	marginTop:-28
       },1000,function(){
       	$('.scroll-msges ul').css({marginTop:0})
        $('.scroll-msges ul').append($('.scroll-msges ul li').first())
       })
    }
	 setInterval(scrollmsges,3000)



            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                autoplay: 2800,
                autoplayDisableOnInteraction: false,
            });


            $("#sim").click(function(){
                layer.open({
                className:'index-msg',
                content: "暂未开放",
                btn: ['确定']
            })
            })

    $(function(){
        var proNos = '';
        var msg = '';
        if(msg != ''){
            layer.open({
                className:'index-msg',
                content: msg,
                btn: ['确定']
            })
        }
        $('#pro li').each(function(i){
            proNos += (i == 0 ? '' : ',') + $(this).data('proNo')
        })
        $.ajax({
            url: '<?= url('site/proCloseList')?>',//ProCloseList
            data: {
                proNo: proNos
            },
            success: function(data){
                var obj = data.data;
                for(var prop in obj){
                    $('[data-pro-no=' + prop + ']').data('preClose', obj[prop]);
                }
                setInterval(queryIndices, 1000);
                queryIndices();
            }
        })


        function queryIndices(){
            $.ajax({
                url: '<?= url('site/proPriceList')?>',
                data: {
                    proNo: proNos
                },
                success: function(data){
                    var obj = data.data;
                    for(var prop in obj){
                        var indices = obj[prop];
                        var preClose = $('[data-pro-no=' + prop + ']').data('preClose');
                        var p = $('[data-pro-no=' + prop + '] span p')
                        var s = $('[data-pro-no=' + prop + '] p')

                        if(preClose != undefined && preClose != null && preClose != 0 && indices != null){
                            s.eq(0).find('span').html('')
                            s.eq(0).append('<span class="status" style="background: #e3a345;color: white;font-size: 10px;">交易中</span>')
                            if(indices < preClose){
                                p.parent().addClass('active');
                            }
                            p.eq(0).text(parseFloat(indices));
                            if(((indices / preClose - 1) * 100).toFixed(2) > 0){
                                p.eq(0).css('color','#f34b60')
                             p.eq(1).css('background','#f34b60')
                            }else{
                                p.eq(0).css('color','#29ce92')
                                p.eq(1).css('background','#29ce92')
                            }
                         // s.eq(0).find('p').after('<p><span class="status" style="background: #e3a345;color: white;font-size: 14px">交易中</span></p>')
                            ((indices / preClose - 1) * 100).toFixed(2) > 0 ? p.eq(1).text("+"+((indices / preClose - 1) * 100).toFixed(2) + '%') : p.eq(1).text(((indices / preClose - 1) * 100).toFixed(2) + '%')
                        }else{
                            s.eq(0).find('span').html('')
                            s.eq(0).append('<span class="status" style="background: #ccc;color: white;font-size: 10px;">停市</span>')
                            p.parent().addClass('active3');
                            p.eq(0).text("- -.- -");
                            p.eq(1).text("停市");

                        }
                    }
                }
            })
        }
    })


    function getQueryString(name)
    {
         var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
         var r = window.location.search.substr(1).match(reg);
         if(r!=null)return  unescape(r[2]); return null;
    }
    var rid = getQueryString("rid");
    var COOKIE_NAME = 'yht_rid';
    if(rid != null && rid != ''){
        $.cookie(COOKIE_NAME, rid, {path:'/', expires:3});
    }
        </script>

</body></html>
