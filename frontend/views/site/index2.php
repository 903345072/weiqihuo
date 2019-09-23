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
        width:100%;
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
        border-bottom:1px solid #efeff6;
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
       background:transparent!important;
        color:#000;
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
        display:none;
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
                    <li class="mid"><?=config('web_name')?></li>
                    <li class="ri" style="width:1.2rem">
                                <a class="col-w  f-ri" style="padding-right:.1rem" href="/user"><?=u()->nickname ?></a>
                    </li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>
            
            <!--        滚动图片        -->
        <div class="swiper-container swiper-container-horizontal">
            <div class="swiper-wrapper" style="/*transform: translate3d(-5709px, 0px, 0px);*/ transition-duration: 300ms;">
                        <div class="swiper-slide" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103129278.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-prev" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103200761.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-active" style="width: 1903px;">
                            <a href="#"><img src="/test/20170421103217491.jpg" width="100%"></a>
                        </div>
                        <div class="swiper-slide swiper-slide-next" style="width: 1903px;">
                            <a href="#"><img src="/test/2017042110323524.jpg" width="100%"></a>
                        </div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span></div>
        </div>

        <!--        头部菜单                   -->
       <!-- <ul class="index-tab flex"style="display:;">
            <!--li>
                <a href="/site/detail?type=sim"><i class="iconfont col-4"></i>
                    <p class="col-1">模拟</p>
                </a>
            </li-->
            <!--<li>
                <a href="/site/userguide"><i class="iconfont col-4"></i>
                    <p class="col-1">交易规则</p>
                </a>
            </li>
            <li>
                <a href="/zx"><i class="iconfont col-4"></i>
                    <p class="col-1">交易咨询</p>
                </a>
            </li>
            <li>
                <a href="/site/wx"><i class="iconfont col-4"></i>
                    <p class="col-1">APP下载</p>
                </a>
            </li>
        </ul>
		
		
       <!--滚动信息-->
        <div class="scroll-msg box-block">
          <ul>
            <li>
               欢迎来到平台测试和参考学习！
            </li>
        </div>
        <div style="height: .1rem"></div>
        <!--            首页热门        -->
        <div class="index-hot">
            <h3 class="col-2  flex2"><span><font color="red">热门交易</font></span><span class="fr flex2"><em><font color="red">最新价</font></em><em><font color="red">涨幅</font></em></span></h3>
            <!-- 商品列表 -->
            <ul id="pro">
                <?php foreach ($productArr as $key => $value): ?>
                        <li data-pro-no="<?= $value['table_name'] ?>">
                            <a class="flex2" href="/site/detail?pid=<?=$value['id'] ?>">
                            
                            <?php $class='active';if ($value['price'] > $value['close']){ $class = '';}?>
                            <em><p class="col-1"><?= $value['name'] ?></p><!--<p class="col-3" style="font-size:.12rem"><?= $value['code'] ?></p> --></em>
                            <span class="ri flex2 <?=$class ?>">
                                <p><?=number_format($value['price'],0,".","") ?></p>
                                <p><?=$value['diff_rate'] ?>%</p>
                            </span>
                            </a>
                        </li>
                <?php endforeach ?>
                        
            </ul>
        </div>
        
            <!-- 新闻资讯-->
		          <div class="new-show">
            <div class="new-show_msg" style="float:left;">新闻资讯</div>
            <div class="new-more" style="float:right">
              <a href="http://www.jinshishujuwang.com/">
              <i class="iconfont"></i>
              </a>
            </div>
          </div>
     <div class="new-list">
    <ul>
      <li>
        <div class="new-left">
          <p class="new-msg">【证券业纾困民企成效渐显 资管计划陆续落地】</p>
          <p>微交所 01-09 12.36</p>
        </div>
        <div class="new-right">
          <img src="/test/zhongtong.jpg"/>
        </div>
      </li>
      <li>
        <div class="new-left">
          <p class="new-msg">巴西矿业和能源部长：将不会再有对燃料价格的政治干预。</p>
          <p>微交所 01-08 14.36</p>
        </div>
        <div class="new-right">
          <img src="/test/yumi.jpg"/>
        </div>
      </li>
      <li>
        <div class="new-left">
          <p class="new-msg">惠誉：发达市场的宏观经济环境和行业细分的杠杆水平与2008年经济衰退相比有着显著差异。</p>
          <p>微交所 1-09 18.05</p>
        </div>
        <div class="new-right">
          <img src="/test/zm.jpg"/>
        </div>
      </li>
      <li>
        <div class="new-left">
          <p class="new-msg">芝加哥期权交易所Cboe 5月外汇交易量创下历史新</p>
          <p>微交所 1-02 2.35</p>
        </div>
        <div class="new-right">
          <img src="/test/zjg.jpg"/>
        </div>
      </li>
    </ul>
  </div>
            <!-- 新闻资讯-->
    
        <!--                底部按钮                    -->
        <div class="index-bot-btns">
            <div>
                <p><font color="red">交易由纽约商品交易所，香港交易所，新加坡交易所等提供实盘对接</font></p>
                
            </div>
            <p><font color="red">投资有风险，入市须谨慎</font></p>
            <p><?=config('tel')?></p>
        </div>
      <!-- 底部浮动的注册-->
     <span class="reg-button">
        <a href="/site/detail">
           马上交易
        </a>
      </span>
    <div class="register-box">
      <span class="close-reg">x</span>
      <span class="reg-msg">聪明的人在这里赚到第一桶金</span>
    </div>
      <!-- 底部浮动的注册-->
        <?= $this->render('_foot') ?><!--引入公共底部-->

<script src="/test/swiper.min.js"></script>
<script src="/test/jquery.cookie.js"></script>
<script>
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
						
						//console.log(indices+'\\'+preClose);
						
                        var p = $('[data-pro-no=' + prop + '] span p')
                        if(preClose != undefined && preClose != null && preClose != 0 && indices != null){
                            
                            if(indices < preClose){
                                p.parent().addClass('active');
                            }

                            p.eq(0).text(parseFloat(indices));
                            ((indices / preClose - 1) * 100).toFixed(2) > 0 ? p.eq(1).text("+"+((indices / preClose - 1) * 100).toFixed(2) + '%') : p.eq(1).text(((indices / preClose - 1) * 100).toFixed(2) + '%')
                            
                            
                        }else{
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