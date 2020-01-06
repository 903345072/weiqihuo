<?php

namespace frontend\controllers;

use frontend\models\OrderDetail;
use Yii;
use common\helpers\Curl;
use frontend\models\User;
use frontend\models\UserCoupon;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use common\helpers\FileHelper;
use common\helpers\Json;
use yii\log\FileTarget;
use common\models\BalanceAccount;

class SiteController extends \frontend\components\Controller
{
    public $newuser;



    public function beforeAction($action)
    {
        //var_dump(user()->isGuest);
        //exit;
            $actions = [
            'ajax-update-status',
            'wxtoken', 'wxcode',
            'test', 'rule', 'captcha',
            'notify', 'ztfnotify',
            'sdpaynotify','hx-weixin',
            'zynotify', 'update-user',
            'update', 'tynotify','login',
            'reg','verify-code',
             'starnotify','ylnotify',
            'ajax-reg','ajax-forget',
            'pass-for-get','gatherdata','data','balance-account','o2o-zf'];
         //        var_dump(user()->isGuest);
         //        var_dump(!in_array($this->action->id, $actions));
         // exit;
            if(user()->isGuest && !in_array($this->action->id, $actions))
            {
                $this->redirect(['/site/login']);
                return false;
            }
            else{
                return true;
            }


    }

    public function actionO2oZf()
    {



        $token = "BJ4NIX3N36DQYM3TPD3T0PO9IYX8T3KD";
        //回调过来的post值
        $bill_no = $_POST["bill_no"];                  //一个24位字符串，是此订单在020ZF服务器上的唯一编号
        $orderid = $_POST["orderid"];                  //是您在发起付款接口传入的您的自定义订单号
        $price = $_POST["price"];                      //单位：分。是您在发起付款接口传入的订单价格
        $actual_price = $_POST["actual_price"];        //单位：分。一定存在。表示用户实际支付的金额。
        $orderuid = $_POST["orderuid"];                //如果您在发起付款接口带入此参数，我们会原封不动传回。
        $key = $_POST["key"];
        $notify_key = md5($actual_price.$bill_no.$orderid.$orderuid.$price.$token);
        $log = new FileTarget();
        $log->logFile = Yii::getAlias('@givemoney/recharge.log');

        $log->messages[] = ['订单:'.$_POST["orderid"].'充值:'.$_POST["actual_price"].'签名:'.$key.'校验签名:'.$notify_key,8,'application',time()];
        $log->export();
        if($key == $notify_key) {

            $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $orderid])->one();
            if (!empty($userCharge)) {
                //充值状态：1待付款，2成功，-1失败
                if ($userCharge->charge_state == 1) {
                    //找到这个用户
                    $user = User::findOne($userCharge->user_id);
                    //给用户加钱
                    $user->account += $userCharge->amount;
                    if ($user->save()) {
                        //更新充值状态---成功
                        $userCharge->charge_state = 2;
                    }
                }
                //更新充值记录表
                $userCharge->update();
                echo "success";       //请不要修改或删除
                exit();
            }
        }
    }

    public function actionYlnotify(){

        $log = new FileTarget();
        $log->logFile = Yii::getAlias('@givemoney/recharge.log');
        $data[code] = get('code');
        $data[ordercode] = get('ordercode');
        $data[amount] = get('amount');
        $data[trade_no]  = get('trade_no');  //平台订单号
        $data[receipt_amount] = get('receipt_amount');
        $data[receipt_paytime]  = get('receipt_paytime');
        $data[key] = '27CD4517298EEC5E245BBC3B740FDC38';
        $sign = get('sign');
        $check_sign = md5(http_build_query($data));
        $log->messages[] = ['订单:'.$data[ordercode].'充值:'.$data[receipt_amount].'签名:'.$sign.'校验签名:'.$check_sign,8,'application',time()];
        $log->export();
        if($check_sign == $sign){
            if ($data['code'] == 'success') {
                $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $data[ordercode]])->one();
                if (!empty($userCharge)) {
                    //充值状态：1待付款，2成功，-1失败
                    if ($userCharge->charge_state == 1) {
                        //找到这个用户
                        $user = User::findOne($userCharge->user_id);
                        //给用户加钱
                        $user->account += $userCharge->amount;
                        if ($user->save()) {
                            //更新充值状态---成功
                            $userCharge->charge_state = 2;
                        }
                    }
                    //更新充值记录表
                    $userCharge->update();
                    echo "success";
                    exit();
                }
            }
        }
    }



public function actionStarnotify(){

    $log = new FileTarget();
    $log->logFile = Yii::getAlias('@givemoney/recharge.log');
   $customerAmount = post('customerAmount');
   $customerAmountCny = post('customerAmountCny');
   $outOrderId = post('outOrderId');
   $orderId = post('orderId');  //平台订单号
   $signType = post('signType');
   $status = post('status');
   $sign = post('sign');
   $check_sign = md5($customerAmount.$customerAmountCny.$outOrderId.$orderId.$signType.$status.'4d503bfd8b0c14ff0e02992315da73fe');
    $log->messages[] = ['订单:'.post('outOrderId').'充值:'.post('customerAmountCny').'签名:'.$sign.'校验签名:'.$check_sign,8,'application',time()];
    $log->export();
   if($check_sign == $sign){
       if ($status == 'success') {
           $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $outOrderId])->one();
           if (!empty($userCharge)) {
               //充值状态：1待付款，2成功，-1失败
               if ($userCharge->charge_state == 1) {
                   //找到这个用户
                   $user = User::findOne($userCharge->user_id);
                   //给用户加钱
                   $user->account += $userCharge->amount;
                   if ($user->save()) {
                       //更新充值状态---成功
                       $userCharge->charge_state = 2;
                   }
               }
               //更新充值记录表
               $userCharge->update();

               echo "success";
               exit();
           }
       }
   }
}

    public function actionData(){
        $fp = fopen(Yii::getAlias('@frontend').'/web/lock.txt', "r");

        if(flock($fp, LOCK_EX | LOCK_NB))
        {
            $this->actionGatherdata();

            sleep(1);
            flock($fp,LOCK_UN);
        } else {
            echo 2;
        }
//关闭文件
        fclose($fp);

    }

    public function actionGatherdata(){
        $products = Product::find()->where(['state' => 1, 'on_sale' => 1, 'source' => 1])->select('table_name, code, trade_time, id,risk')->asArray()->all();
        $shujutypearr = array(
            'fx_seurusd',
            'fx_sgbpusd',
            'fx_saudusd',
            'fx_seurjpy',
            'fx_sgbpusd',
            'fx_scadusd',
            'fx_seurcad',
        );
        foreach($products as $k=>$v){
            if(is_int($k)){
                $start = strtotime(date('Y-m-d 00:00:00', time()));
                if ($v['trade_time'] && $v['code'] != 'fx_sgbpusd') {
                    $timeArr = unserialize($v['trade_time']);
                    $start = strtotime(date('Y-m-d ' . $timeArr[0]['start'] . ':00'));
                    $time = end($timeArr);
                    $end = strtotime(date('Y-m-d ' . $time['end'] . ':00'));
                    if ($start > $end) {
                        if ($start > time() && $end < time()) {
                            continue;
                        }
                    } else {
                        if ($start > time() || $end < time()) {
                            continue;
                        }
                    }
                }
                if($v['code']=='btc'){
                    $url = 'https://www.bitstamp.net/api/v2/ticker/btcusd?time='.time();
                }elseif($v['code']=='ltc'){
                    $url = 'https://www.bitstamp.net/api/v2/ticker/ltcusd?time='.time();
                }elseif ($v['code']=='eth'){
                    $url = 'https://www.bitstamp.net/api/v2/ticker/ethusd?time='.time();
                }elseif ($v['code']=='bch'){
                    $url = 'https://www.bitstamp.net/api/v2/ticker/bchusd?time='.time();
                }
                elseif ($v['code'] == 'sz399300'){
                    $url = "http://web.sqt.gtimg.cn/q=".$v['code']."?r=0.".time()*88;
                }elseif ($v['code'] == 'lls'){
                    $url = "https://m.sojex.net/api.do?rtp=GetQuotesDetail&id=13";
                }else{
                    $url = 'http://hq.sinajs.cn/etag.php?_='.time().'1000&list='.$v['code'];
                }
                if(in_array($v['code'],array('RE','coal','MAF'))){
                    $result = true;
                }else{
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($curl);	curl_close($curl);
                }

                if ($result) {
                    if(in_array($v['code'],array('RE','coal','MAF'))){
                        $resultarr = true;
                    }else{
                        if ($v['code'] == 'sz399300'){
                            $resultarr = explode('~', $result);
                        }elseif ($v['code'] == 'lls'){
                            $resultarr = json_decode($result,1);
                            $resultarr = $resultarr['data']['quotes'];
                        }else{
                            $resultarr = explode(',', $result);
                        }
                        if(sizeof($resultarr) < 3) {
                            break;
                        }
                    }
                    /**************处理数据*****************/
                    if($v['code'] == 'hkHSI' || $v['code'] == 'nf_M0' || $v['code'] == 'nf_NI0'){
                        $price = $resultarr[6];
                        $diff = $resultarr[7];
                        $diff_rate = $resultarr[8];
                        $dtime = strtotime($resultarr[sizeof($resultarr) - 2] ." " .explode('"', $resultarr[sizeof($resultarr) - 1])[0]);
                        $data = [
                            'price' => $price,
                            'open' => $resultarr[2],
                            'high' => $resultarr[4],
                            'low' => $resultarr[5],
                            'close' => $resultarr[3],
                            'diff' => $diff,
                            'diff_rate' => $diff_rate,
                            'time' => date('Y-m-d H:i:s', time())
                        ];
                    }elseif ($v['code'] == 'lls'){
                        $data = [
                            'price' => $resultarr['buy'],
                            'open' => $resultarr['open'],
                            'high' => $resultarr['top'],
                            'low' => $resultarr['low'],
                            'close' => $resultarr['last_close'],
                            'diff' => $resultarr['mp'],
                            'diff_rate' => $resultarr['margin'],
                            'time' =>date('Y-m-d H:i:s', time())
                        ];
                    }elseif ($v['code'] == 'sz399300'){
                        $data = [
                            'price' => $resultarr[3],
                            'open' => $resultarr[4],
                            'high' => $resultarr[41],
                            'low' => $resultarr[42],
                            'close' => $resultarr[5],
                            'diff' => $resultarr[31],
                            'diff_rate' => $resultarr[32],
                            'time' => date('Y-m-d H:i:s')
                        ];

                    }elseif(in_array($v['code'], array('hf_CL','hf_SI', 'hf_GC', 'hf_SI', 'hf_NG','hf_JY', 'hf_EC', 'hf_HG', 'hf_CHA50CFD', 'hf_CAD', 'hf_HSI'))){
                        $price = explode('"', $resultarr[0])[1];
                        $diff = $price - $resultarr[7];
                        if($diff == 0) {
                            $diff_rate = 0.00;
                        } else {
                            $diff_rate = $resultarr[1];
                        }

                        $data = [
                            'price' => $price,
                            'open' => $resultarr[8],
                            'high' => $resultarr[4],
                            'low' => $resultarr[5],
                            'close' => $resultarr[7],
                            'diff' => $diff,
                            'diff_rate' => $diff_rate,
                            'time' => date('Y-m-d H:i:s')
                        ];

                    }elseif(in_array($v['code'], $shujutypearr)) {
                        $price = $resultarr[1];
                        $diff = $price - $resultarr[3];
                        if($diff == 0) {
                            $diff_rate = 0.00;
                        } else {
                            $diff_rate = number_format($diff / $resultarr[3] * 100, 2, ".", "");
                        }
                        $dtime = strtotime(explode('"', $resultarr[sizeof($resultarr) - 1])[0]." " .explode('"', $resultarr[0])[1]);
                        $data = [
                            'price' => $price,
                            'open' => $resultarr[5],
                            'high' => $resultarr[6],
                            'low' => $resultarr[8],
                            'close' => $resultarr[3],
                            'diff' => $diff,
                            'diff_rate' => $diff_rate,
                            'time' => date('Y-m-d H:i:s', $dtime)
                        ];
                    }elseif (in_array($v['code'], array('ltc','eth','btc','bch', 'btc_btcbitstamp', 'btc_btcbitstamp', 'btc_btcbitstamp'))){
                        $arr = json_decode($result, true);
                        $price = $arr['bid'];
                        $diff = $price - $arr['last'];
                        if ($diff == 0) {
                            $diff_rate = 0.00;
                        } else {
                            $diff_rate = number_format($diff / $arr['bid'] * 100, 2, ".", "");
                        }
                        $data = [
                            'price' => $price,
                            'open' => $arr['open'],
                            'high' => $arr['high'],
                            'low' => $arr['low'],
                            'close' => $arr['last'],
                            'diff' => $diff,
                            'diff_rate' => $diff_rate,
                            'time' => date('Y-m-d H:i:s')
                        ];
                    }elseif(in_array($v['code'], array('RE','MAF', 'coal'))){
                        if (empty($price = session('initData' . $v['table_name']))) {
                            $dataAll = DataAll::findOne($v['table_name']);
                            $price = $dataAll->price;
                        }else{
                            $price = session('initData' . $v['table_name']);
                        }
                        if($v['code'] == 'RE'){
                            $rand = mt_rand(-3,3);
                        }elseif($v['code'] == 'MAF'){
                            $rand = mt_rand(-4,4);
                        }elseif($v['code'] == 'coal'){
                            $rand = mt_rand(-2,2);
                        }
                        $price += $rand;
                        session('initData' . $v['table_name'],$price);
                        $data = [];
                        $nowTime = date('Y-m-d 00:00:00', time());
                        $insertOpen = strtotime($nowTime);

                        if ($insertOpen < time() + 30 && $insertOpen > time()) {
                            $open = $price;
                            $productPrice = Product::db('SELECT price FROM data_' . $v['table_name'] . ' WHERE time < "' . $nowTime . '" ORDER BY time DESC LIMIT 1')->queryAll();
                            if (empty($productPrice)) {
                                $close = $price;
                            } else {
                                $close = $productPrice[0]['price'];
                            }
                            $data['open'] = $open;
                            $data['close'] = $close;
                        }
                        $maxPrice = Product::db('SELECT price FROM data_' . $v['table_name'] . ' WHERE time > "' . $nowTime . '" ORDER BY price DESC LIMIT 1')->queryAll();
                        $minPrice = Product::db('SELECT price FROM data_' . $v['table_name'] . ' WHERE time > "' . $nowTime . '" ORDER BY price ASC LIMIT 1')->queryAll();
                        if (empty($maxPrice)) {
                            $data['high'] = $price;
                            $data['low'] = $price;
                        } else {
                            $data['high'] = $maxPrice[0]['price'];
                            $data['low'] = $minPrice[0]['price'];
                        }

                        $data['price'] = $price;
                        $data['time'] = date('Y-m-d H:i:s');
                    }
                    /*if (empty($param = session('initDataParam' . $v['table_name']))) {
                        $productParam = ProductParam::findOne($v['id']);
                        $param = $productParam->attributes;
                        session('initDataParam' . $v['table_name'], $productParam->attributes, 1800);
                    }*/
                    /**************加入风控设置*****************/
                    $data111 = $v['risk']; //滑点控制设置 1.56
                    if( $data111!=0 ){
                        $data222 = explode('.',$v['risk']);     //假如设置1.56
                        if(isset($data222[1])){
                            $aaa = strlen($data222[1]);  //aaa=>2
                        }else{
                            $aaa = 0;
                        }
                        $riskint = $data111*pow(10,$aaa);      //1.56*10^2   156上升156点
                        if($riskint > 0){
                            $data111 = mt_rand(0, $riskint);      //取（0，156）随机数  120
                        }else{
                            $data111 = mt_rand($riskint, 0);
                        }
                        $data111=$data111/pow(10,$aaa);     //120/10^2=>1.2
                        $dataAll11 = DataAll::findOne($v['table_name']);
                        $price = $dataAll11->price + $data111;    //上升1.2个点
                        session('initData' . $v['table_name'], $price);
                        $data['price'] = $price;
                    }

                    $row = self::db("SELECT
			            price,
			            time
			        FROM
			            data_{$v['table_name']}
			        ORDER BY
			            id DESC
			        LIMIT 1")->queryOne();          //查出当当前产品最新的一条记录
                    $is_insert = 0;                 //不插入
                    if($row){
                        if($row['price'] != $data['price'] || strtotime($data['time']) - strtotime($row['time']) >= 10){

                            $is_insert = 1;

                        }
                    }else{

                        $is_insert = 1;

                    }

                    /* if(in_array($v['code'], array('hf_GC', 'hf_HSI'))){
                        var_dump($data['price']);
                        var_dump($data['time']);
                        var_dump($row);
                    }		*/

                    if($is_insert == 1){
                        /**************强制风控*****************/
                        $switchMap = option('risk_product') ?: [];
                        if (($switch = option('risk_switch'))) {
                            $riskProduct = option('risk_product');
                            if (isset($riskProduct) && $riskProduct[$v['table_name']] == 1) {
                                $riseQuery = Order::find()->joinWith('product')->where(['order_state' => Order::ORDER_POSITION, 'product.table_name' => $v['table_name']])->select('SUM(order.deposit) hand');  //当前产品的所有风控订单（比如说有多少个天然气订单）
                                $downQuery = clone $riseQuery;
                                $riseQuery->andWhere(['rise_fall' => Order::RISE]);     //天然气买涨订单
                                $downQuery->andWhere(['rise_fall' => Order::FALL]);     //天然气买跌订单
                                $rise = $riseQuery->one()->hand ?: 0;   //天然气买涨手数
                                $down = $downQuery->one()->hand ?: 0;   //天然气买跌手数
                                if ($rise != $down) {      //如果买涨手数不等于买跌手数
                                    $wave = $rise > $down ? -1 : 1;           //波动标识，买涨手数大于买跌手数 wave=>-1  反之 wave=>1
                                    if (strpos($data['price'], '.') !== false) {   //如果当前价格有小数点
                                        list($int, $point) = explode('.', $data['price']);//int=>当前价格整数,point=>当前价格小数
                                        $point = pow(10, -1 * strlen($point));   //0.31
                                    } else {
                                        $point = 1;
                                    }
                                    // 获取行情信息
                                    $dataInfo = DataAll::findOne($v['table_name']);
                                    $data['price'] = $dataInfo->price;
                                    $data['price'] += $point * $wave * intval(mt_rand(50, 190) / 50);
                                }
                            }
                        }

                        /**************插入数据表*****************/
                        if (self::dbInsert('data_' . $v['table_name'], ['price' => $data['price'], 'time' => $data['time']])) {
                            if(in_array($v['table_name'],array('oil','btc','yb'))){
                                $dataInfo = DataAll::findOne($v['table_name']);
                                $data['diff'] = $data['price'] - $dataInfo->close;
                                $data['diff_rate'] = round($data['diff']*100/$dataInfo->close,2);
                            }
                            $updateMap[$v['table_name']] = $data;
                            $priceJson = @file_get_contents(Yii::getAlias('@frontend/web/price.json')) ?: '{}';
                            $priceJson = json_decode($priceJson, true);
                            self::dbUpdate('data_all', $data, ['name' => $v['table_name']]);
                            $priceJson[$v['table_name']] = $data['price'];
                            file_put_contents(Yii::getAlias('@frontend/web/price.json'), json_encode($priceJson));
                        }
                    }
                    /**************实时更新订单盈亏*****************/
                    self::db('  UPDATE
                         `order` o,
                         product p,
                         data_all a
                     SET
                        sell_price = a.price,
                       profit = IF (
                             o.rise_fall = ' . Order::RISE . ',
                             a.price - o.price,
                             o.price - a.price
                        ) * o.hand * o.one_profit
                    WHERE
                        a.name = p.`table_name`
                     AND o.product_id = p.id
                     AND o.order_state =  ' . Order::ORDER_POSITION . '
                    AND sell_price != a.price')
                        ->execute();
                    /**************遍历订单平仓*****************/
                    $ids = self::db('SELECT * from `order` where (order_state = ' . Order::ORDER_POSITION . ' )')->queryAll();

                    array_walk($ids, function ($value) {
                        $bili = round(abs($value['profit'])*100/$value['deposit'],2);   //盈利比率
                        $is_sell_order = 0;             //初始化买卖状态，不卖
                        if($value['profit'] > 0){        //盈利
                            if($bili*100 >= $value['stop_profit_point']*100){   //订单满足止盈
                                $is_sell_order = 1;   //卖掉此订单
                            }
                        }else{             //亏损
                            if($bili*100 >= $value['stop_loss_point']*100){
                                $is_sell_order = 1;
                            }
                        }
                        if($is_sell_order == 1){       //
                            if($value['sim'] == 1){
                                Order::sellOrderHand($value['id'], true);
                            }else{
                                Order::sellOrderHand($value['id'], true,'sim');
                            }
                        }
                    });
                    if(date('H:i')=='04:55'){
                        $ids = self::db('SELECT id from `order` where (order_state = ' . Order::ORDER_POSITION . ' AND sim=1)')->queryAll();
                        array_walk($ids, function ($value) {
                            Order::sellOrderHand($value['id'], true);//点位平仓操作
                        });
                        $ids = self::db('SELECT id from `order` where (order_state = ' . Order::ORDER_POSITION . ' AND sim=1)')->queryAll();
                        array_walk($ids, function ($value) {
                            Order::sellOrderHand($value['id'], sim);//点位平仓操作
                        });
                    }
                }
            }
        }
    }
    //商城首页
    public function actionShop()
    {
        $this->view->title = '商城';

        return $this->render('shop');
    }

    //商城--商品详情1
    public function actionOne()
    {
        $this->view->title = '商品详情';
        return $this->render('shopDetail1');
    }
    //商城--商品详情2
    public function actionTwo()
    {
        $this->view->title = '商品详情';
        return $this->render('shopDetail2');
    }
    //商城--商品详情3
    public function actionThree()
    {
        $this->view->title = '商品详情';
        return $this->render('shopDetail3');
    }

    public function actionRule()
    {
        $this->view->title = '规则';
        $img = '/images/rule.png';
        return $this->render('rules', compact('img'));
        // return $this->render('rule');
    }

    public function actionTip()
    {
        $this->view->title = '提示消息';
        return $this->render('tip');
    }
     public function actionIndex()
    {

		//$maxOrder = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => 28])->andWhere(['>', 'created_at', date('Y-m-d 00:00:00', time())])->andWhere(['<', 'created_at', date('Y-m-d 00:00:00', strtotime('tomorrow'))])->with('product')->select('SUM(deposit) deposit')->one();

        //var_dump($maxOrder->deposit);exit;


        //var_dump(session('wechat_userinfo'));
        //exit;
        //所有在售商品ON_SALE_YES

        $productArr = Product::getProductAllArray();
        foreach ($productArr as $key => $value) {
            $jsonArr[] = $value['table_name'];
        }
        $json = json_encode($jsonArr);
        reset($productArr);
        $pid = get('pid', key($productArr));
        //这条期货信息
        $product = Product::find()->andWhere(['id' => $pid])->with('dataAll')->one();
        //最新的这条期货数据集
        $newData = DataAll::newProductPrice($product->table_name);
        return $this->renderPartial('index', compact('product', 'newData', 'productArr'));
        //return $this->render('index22', compact('product', 'newData', 'productArr'));
    }
    public function actionWx()
    {
        return $this->renderPartial('wx');
    }
    public function actionBuyMiddle()
    {
        session('buyurl',get('url')."&state=".get('state'),999);
        return $this->renderPartial('buy-middle');
    }



    public function actionList(){

        $productArr = Product::getProductAllArray();
        foreach ($productArr as $key => $value) {
            $jsonArr[] = $value['table_name'];
        }
        $json = json_encode($jsonArr);
        reset($productArr);
        $pid = get('pid', key($productArr));
        //这条期货信息
        $product = Product::find()->andWhere(['id' => $pid])->with('dataAll')->one();
        //最新的这条期货数据集
        $newData = DataAll::newProductPrice($product->table_name);
        return $this->renderPartial('detail', compact('product', 'newData', 'productArr'));
    }


    public function actionDetail()
    {
        if(!get('type')=='sim')
        {
            session('sim_type',null);//清除模拟盘标记
        }
        else
        {
            session('sim_type','sim');
        }
        $user=u();
        $user->id=u()->id;
        $user->account=u()->account;
        $user->blocked_account=u()->blocked_account;
        $type="";
        //如果是模拟盘，就赋值虚拟账户金额和模拟盘标志
        if(session('sim_type')!=null&&session('sim_type')=='sim')
        {
            $user->account=u()->sim_account;
            $user->blocked_account=u()->sim_blocked_account;
            $type='moni';
        }


         $productArr = Product::getProductAllArray();
        foreach ($productArr as $key => $value) {
            $jsonArr[] = $value['table_name'];
        }
        $json = json_encode($jsonArr);
        reset($productArr);
        $pid = get('pid', key($productArr));

        //这条期货信息
        $product = Product::find()->andWhere(['id' => $pid])->with('dataAll')->one();

        //最新的这条期货数据集
        $newData = DataAll::newProductPrice($product->table_name);
//        echo '<pre>';
//        var_dump($newData);die;

        return $this->renderPartial('newDetail', compact('product', 'newData',  'productArr', 'user','type'));
        //return $this->render('index22', compact('product', 'newData', 'count', 'productArr', 'orders', 'time', 'json','user'));

    }

    //期货的最新价格数据集
    public function actionAjaxNewProductPrice()
    {


        $product = Product::findModel(post('pid'));
        //周末休市 特殊产品不休市
        if ((date('w') == 0 && $product->source == Product::SOURCE_TRUE) || (date('G') > 3 && $product->source == Product::SOURCE_TRUE && date('w') == 6)) {
            return error();
        }
        $idArr = Order::find()->where(['order_state' => Order::ORDER_POSITION, 'user_id' => u()->id, 'product_id' => $product->id])->map('id', 'id');
        if (empty($idArr)) {
            $idArr = [];
        }
        return success($idArr);
    }
  /*********************************************k线数据接口**********************/

    public function actionGetLine()//分时线接口，9小时累进;全日线，当天24小时；
    {
        $id=get('pid');

        if(empty(get('time')))
        {
            $end=date("Y-m-d H:i:s",time()+60*60*4);
           // $end=date("2017-12-02 09:52:44");
            $start=date("Y-m-d H:i:s",strtotime($end)-60*60*7);
        }
        else
        {
            $start=date("Y-m-d H:i:s",get('time')/1000);
            $end=date("Y-m-d H:i:s",get('time')/1000+10800);
        }
        if(get('isAllDay')=='true')
        {
            $end=date("Y-m-d 23:23:59");
            //$end=date("2017-12-02 23:23:59");
            $start=date("Y-m-d H:i:s",strtotime($end)-60*60*24);

        }
        $model = Product::findModel($id);
        $name = $model->table_name;
        $format='%Y-%m-%d %H:%i';


        $data = self::db("SELECT
                 cu.price indices, UNIX_TIMESTAMP(DATE_FORMAT(time,'".$format."')) * 1000 time
        FROM
            (
                SELECT
                    
                    max(d1.id) id
                FROM
                    data_" . $name . " d1
                where time >'".$start."' and time <'".$end."'
                group by
                    DATE_FORMAT(time,'".$format."')
            ) sub,
            data_" . $name . " cu
        WHERE
            cu.id = sub.id")->queryAll();
        //$response->send();
        $da=null;
       if(!empty($data))
       {
        for($i=0;$i<count($data);$i++)
        {
            $da[$i]['time']=(float)$data[$i]['time'];
            $da[$i]['indices']=(float)$data[$i]['indices'];

        }
       }

      $jsonarr['msg']="请求成功！";
      $jsonarr['success']=true;
      $jsonarr['totalCount']=0;
      $jsonarr['resultObject']['startTime']=strtotime($start)*1000;
      $jsonarr['resultObject']['endTime']=strtotime($end)*1000;
      $jsonarr['resultList']=$da;
      echo json_encode($jsonarr);

    }

    public function actionGetLineLight()//闪电线接口，5分钟累进
    {

            $id=get('pid');
            $end=date("Y-m-d H:i:s");
            //$end=date("2017-12-02 09:52:48");
            $start=date("Y-m-d H:i:s",strtotime($end)-600);

        $model = Product::findModel($id);
        $name = $model->table_name;
        $format='%Y-%m-%d %H:%i:%s';
        $data = self::db("SELECT
                 cu.price indices, UNIX_TIMESTAMP(DATE_FORMAT(time,'".$format."')) * 1000 time
        FROM
            (
                SELECT
                    
                    max(d1.id) id
                FROM
                    data_" . $name . " d1
                where time >'".$start."' and time <'".$end."'
                group by
                    DATE_FORMAT(time,'".$format."')
            ) sub,
            data_" . $name . " cu
        WHERE
            cu.id = sub.id")->queryAll();
        //$response->send();

        $da=null;
       if(!empty($data))
       {
        for($i=0;$i<count($data);$i++)
        {
            $da[$i]['time']=(float)$data[$i]['time'];
            $da[$i]['indices']=(float)$data[$i]['indices'];

        }
       }

      $jsonarr['msg']="请求成功！";
      $jsonarr['success']=true;
      $jsonarr['totalCount']=0;
      $jsonarr['resultObject']=null;
      $jsonarr['resultList']=$da;


      echo json_encode($jsonarr);

    }


public function actionGetLineDay()//日线接口，60天累进
    {

            $id=get('pid');
            $end=date("Y-m-d H:i:s");
            //$end=date("2017-12-02 09:08:59");
            $start=date("Y-m-d H:i:s",strtotime($end)-60*60*24*60);

        $model = Product::findModel($id);
        $name = $model->table_name;
        $format='%Y-%m-%d';
        $data = self::db("SELECT
                sub.*, cu.price indices, UNIX_TIMESTAMP(DATE_FORMAT(time,'".$format."')) * 1000 time
        FROM
            (
                SELECT
                    min(d1.price) low,
                    max(d1.price) high,
                    substring_index(group_concat(d1.price order by `id` desc),',',1) open,
                    substring_index(group_concat(d1.price order by `id` desc),',',-1) close,
                    max(d1.id) id
                FROM
                    data_" . $name . " d1
                where time >'".$start."' and time <'".$end."'
                group by
                    DATE_FORMAT(time,'".$format."')
            ) sub,
            data_" . $name . " cu
        WHERE
            cu.id = sub.id")->queryAll();
        //$response->send();

        $da=null;
       if(!empty($data))
       {
        for($i=0;$i<count($data);$i++)
        {
            $da[$i]['dateTime']=(float)$data[$i]['time'];
            $da[$i]['indices']=(float)$data[$i]['indices'];
            $da[$i]['low']=(float)$data[$i]['low'];
            $da[$i]['high']=(float)$data[$i]['high'];
            $da[$i]['open']=(float)$data[$i]['open'];
            $da[$i]['close']=(float)$data[$i]['close'];
            $da[$i]['vol']=(float)mt_rand(500,5000);


        }
       }

      $jsonarr['msg']="请求成功！";
      $jsonarr['success']=true;
      $jsonarr['totalCount']=0;
      $jsonarr['resultObject']=null;
      $jsonarr['resultList']=$da;


      echo json_encode($jsonarr);

    }


    public function actionGetLineMin()//分钟线接口，8小时累进
    {

            $id=get('pid');
            $end=date("Y-m-d H:i:s");
            //$end=date("2017-12-02 09:08:59");
            $start=date("Y-m-d H:i:s",strtotime($end)-60*60*8);

        $model = Product::findModel($id);
        $name = $model->table_name;
        $format='%Y-%m-%d %H:%i';
        $data = self::db("SELECT
                sub.*, cu.price indices, UNIX_TIMESTAMP(DATE_FORMAT(time,'".$format."')) * 1000 time
        FROM
            (
                SELECT
                    min(d1.price) low,
                    max(d1.price) high,
                    substring_index(group_concat(d1.price order by `id` desc),',',1) open,
                    substring_index(group_concat(d1.price order by `id` desc),',',-1) close,
                    max(d1.id) id
                FROM
                    data_" . $name . " d1
                where time >'".$start."' and time <'".$end."'
                group by
                    DATE_FORMAT(time,'".$format."')
            ) sub,
            data_" . $name . " cu
        WHERE
            cu.id = sub.id")->queryAll();
        //$response->send();

        $da=null;
       if(!empty($data))
       {
        for($i=0;$i<count($data);$i++)
        {
            $da[$i]['dateTime']=(float)$data[$i]['time'];
            $da[$i]['indices']=(float)$data[$i]['indices'];
            $da[$i]['low']=(float)$data[$i]['low'];
            $da[$i]['high']=(float)$data[$i]['high'];
            $da[$i]['open']=(float)$data[$i]['open'];
            $da[$i]['close']=(float)$data[$i]['close'];
            $da[$i]['vol']=(float)mt_rand(50,500);


        }
       }

      $jsonarr['msg']="请求成功！";
      $jsonarr['success']=true;
      $jsonarr['totalCount']=0;
      $jsonarr['resultObject']=null;
      $jsonarr['resultList']=$da;


      echo json_encode($jsonarr);

    }
    /*********************************k线数据接口结束**************************************/

    public function actionGetHq()//获取盘面最新信息
    {

         $pid=get('pid');
        $rise = Order::find()->Where(['product_id'=>$pid,'order_state'=>1,'rise_fall'=>1])->orderBy('id DESC')->one();//买涨的手数
        $fall = Order::find()->Where(['product_id'=>$pid,'order_state'=>1,'rise_fall'=>2])->orderBy('id DESC')->one();//买跌的手数
        if(!empty($rise))
        {
			$buyhand=$rise->hand;
			Product::isTradeTime($pid)?$buyhand=mt_rand(300,850):$buyhand=0;
		}
        else
        {
            Product::isTradeTime($pid)?$buyhand=mt_rand(300,850):$buyhand=0;
        }
        if(!empty($fall))
        {
			$sellhand=$fall->hand;
			Product::isTradeTime($pid)?$sellhand=mt_rand(280,700):$sellhand=0;
		}
        else{Product::isTradeTime($pid)?$sellhand=mt_rand(280,700):$sellhand=0;}

        $product= Product::find()->Where(['id'=>$pid])->with('dataAll')->one();


        $model['indices']=(float)$product->dataAll->price;
        $model['open']=(float)$product->dataAll->open;
        $model['high']=(float)$product->dataAll->high;
        $model['low']=(float)$product->dataAll->low;
        $model['change']=(float)$product->dataAll->diff_rate;
        $model['changeValue']=(float)$product->dataAll->diff;
        $model['swing']=(float)$product->dataAll->diff;
        $model['limitUpPrice']=(float)$product->dataAll->high;
        $model['limitDownPrice']=(float)$product->dataAll->low;
        $model['tradeVol']=(float)rand(130000,300000);
        $model['buy']=(float)$product->dataAll->price;
        $model['sell']=(float)$product->dataAll->price;
        $model['buyVol']=$buyhand;
        $model['sellVol']=$sellhand;
        $model['totalQty']=(float)rand(280000,650000);
        $model['volume']=(float)rand(1800000,5000000);
        $model['closingPrice']=
        $model['close']=(float)$product->dataAll->close;
        $model['preClose']=(float)$product->dataAll->close;
        $model['preClosingPrice']=(float)$product->dataAll->close;
        $model['prePositionQty']=(float)0;
        $model['time']=$product->dataAll->time;
        $model['date']=$product->dataAll->time;
        $model['dateTime']=strtotime($product->dataAll->time)*1000;//date("Y-m-d H:i:s")
        $model['name']=$product->name;
        $model['proNo']=$product->table_name;
        $model['product_id']=$product->id;
        $jsonarr['msg']='请求成功!';
        $jsonarr['success']=true;
        $jsonarr['resultList']=null;
        $jsonarr['resultObject']['nextTime']='已休市,下次交易时间';
        $jsonarr['resultObject']['isOpen']=Product::isTradeTime($pid);
        $jsonarr['totalCount']=0;
        $jsonarr['resultObject']['model']=$model;


         if(!Product::isTradeTime($pid)){

            $jsonarr['msg']="当前合约已暂停交易，请选择其他合约!";


         }
         //{"msg":"当前合约已暂停交易，请选择其他合约!","success":false,"resultList":null,"resultObject":null,"totalCount":0}




        echo json_encode($jsonarr);

    }

     public function actionProCloseList()//ajax获得商品列表闭市价格
    {
        $proList=get('proNo');
        $proListArr=explode(',', $proList);
        $product = dataAll::find()->Where(['in','name',$proListArr])->all();
        //$arr[];
        foreach ($product as $value) {
            //休市产品将闭市价修改为0
            Product::isTradeTime(Product::getProductId($value->name))?$arr[$value->name]=$value->close:$arr[$value->name]=0;

        }
        return success('success',$arr);
    }
     public function actionProPriceList()//ajax获得商品列表最新价格
    {

        $proList=get('proNo');
        $proListArr=explode(',', $proList);
        //$product = dataAll::find()->Where(['in','name',$proListArr])->with('Product')->all();
        $productinfo=Product::find()->Where(['in','table_name',$proListArr])->with('dataAll')->all();

        //$arr[];
        //$arr=explode((str)$product->0->unit,".");
        for($i=0;$i<count($productinfo);$i++)
        {
            $unit=explode(".",(string)(float)$productinfo[$i]->unit);
            if(count($unit)>1)
            {$len=strlen($unit[1]);}
            else
            {
                $len=0;
            }
            $name=$productinfo[$i]->table_name;
            //$arr[$name]=$len;
            $arr[$name]=number_format($productinfo[$i]->dataAll->price,$len,".","");
			$arr[$name]=$productinfo[$i]->dataAll->price;
        }

    //    var_dump($arr);
    //     exit;
        // foreach ($product as $value) {

        //     $arr[$value->name]=$value->price;
        // }
        return success('success',$arr);
    }
    public function actionStockInfo()//ajax获得商品最新信息
    {//

        $pid=get('pid');

        $rise = Order::find()->Where(['product_id'=>$pid,'order_state'=>1,'rise_fall'=>1])->orderBy('id DESC')->one();//买涨的手数
        $fall = Order::find()->Where(['product_id'=>$pid,'order_state'=>1,'rise_fall'=>2])->orderBy('id DESC')->one();//买跌的手数

       if(!empty($rise))
        {$buyhand=$rise->hand;}
        else
        {
            Product::isTradeTime($pid)?$buyhand=mt_rand(1,10):$buyhand=0;
        }
        if(!empty($fall))
        {$sellhand=$fall->hand;}
        else{Product::isTradeTime($pid)?$sellhand=mt_rand(1,10):$sellhand=0;}

        $product= Product::find()->Where(['id'=>$pid])->with('dataAll')->one();
        if (!empty($product)){
			$arr['product_id']=$product->id;
			$arr['productName']=$product->name;
			$arr['proNo']=$product->table_name;
			$arr['price']=(double)$product->dataAll->price;
			$arr['diff']=$product->dataAll->diff;
			$arr['diff_rate']=$product->dataAll->diff_rate;
			$arr['close']=$product->dataAll->close;
			$arr['unit']=$arr['step']=$arr['ostMinPrice']=$arr['profixMinPrice']=(double)$product->unit;
			$arr['pointMoney']=(double)$product->unit_price;
			$arr['profixMaxPrice']=$product->maxrise;
			$arr['ostMaxPrice']=$product->maxlost;
			$arr['singleHandlingMoney']=$product->fee;


			if($product->code == 'MAF'){
				$arr['singleMargin']=1000;
			}elseif($product->code == 'RE'){
				$arr['singleMargin']=1500;
			}elseif($product->code == 'hkHSI'){
				$arr['singleMargin']=1000;
			}elseif($product->code == 'coal'){
				$arr['singleMargin']=2000;
			}else{
				/*$arr['singleMargin']=$product->maxlost/$product->unit*$product->unit_price;*/
				$arr['singleMargin']=1000;
			}

			$arr['sell']=$sellhand;
			$arr['buy']=$buyhand;
			$arr['buyprice']=(double)$product->dataAll->price;
			$arr['sellprice']=(double)$product->dataAll->price;
			$json['product']=$arr;
			$json['is_open']=Product::isTradeTime($pid);
			return success('请求成功',$json);
        }
        else
        {
            return error('数据异常');
        }

    }
     public function actionBuyInfo()//ajax获得商品最新信息
     {
       echo date('Y-m-d H:i:s', time());
        //echo $data;
     }




    //买涨买跌
    public function actionAjaxBuyState()
    {

        $data = post('data');
        if (strlen(u()->password) <= 1) {
            // return $this->redirect(['site/setPassword']);
            return success(url(['site/setPassword']), -1);
        }
        //如果要体现必须要有手机号'/user/with-draw'
        if (strlen(u()->mobile) <= 10) {
            return success(url(['site/setMobile']), -1);
        }
        //买涨买跌弹窗
        $productPrice = ProductPrice::getSetProductPrice($data['pid']);
        if (!empty($productPrice)) {
            $class = '';
            $string = '涨';
            if ($data['type'] != Order::RISE) {
                $class = 'style="background-color: #0c9a0f;border: 1px solid #0c9a0f;"';
                $string = '跌';
            }
            return success($this->renderPartial('_order', compact('productPrice', 'data', 'class', 'string')));
        }
        return error('数据出现异常！');
    }
    //买涨买跌独立页面
    public function actionBuyProduct()
    {
        session('buyurl',null);
        $data=get();


        //$product= Product::find()->Where(['id'=>$pid])->with('dataAll')->one();

       $product= Product::find()->Where(['id'=>$data['pid']])->with('dataAll')->one();
            return $this->renderPartial('buy', compact('data','product'));
        //}*/
        //return error('数据出现异常！');
    }
    //规则页面
     public function actionGuide()
    {


        $pid = get('pid');
        $product=Product::find()->where(['id'=>$pid])->one();//获取产品信息

        // if (!empty($product)) {
        //     if($product->currency==1)
        //     {
        //         $product->currency='人民币';
        //     }
        //     else
        //     {
        //          $product->currency='美元';
        //     }
         switch ($product->currency){

                        case 2:
                        $product->currency= "美元";
                            break;
                        case 3:
                        $product->currency= "澳元";
                            break;
                        case 4:
                        $product->currency= "加元";
                            break;
                        case 5:
                        $product->currency= "港币";
                            break;
                        case 6:
                        $product->currency= "欧元";
                            break;
                        case 7:
                        $product->currency= "英镑";
                            break;
                        default:
                        $product->currency= "人民币";
                    }
            $product->unit=(double)$product->unit;
            $product->unit_price=(double)$product->unit_price;
            $desc=explode('|',$product->desc);
            $time=unserialize($product->trade_time);
            //echo $time['1']['end'];
            //exit;



            return $this->renderPartial('guide', compact('product','time','desc'));
        }
    //     $this->redirect(['/site/index']);
    // }

    //买涨买跌
    public function actionT()
    {
        $user = User::findModel(u()->id);
        $user->password = 0;
        $user->save(false);
    }

    //设置商品密码
    public function actionAjaxSetPassword()
    {
        $data = trim(post('data'));
        if (strlen($data) < 6) {
            return error('商品密码长度不能少于6位！');
        }
        $user = User::findModel(u()->id);
        $user->password = $data;
        if ($user->hashPassword()->save()) {
            $user->login(false);
            return success();
        }
        return error('设置失败！');
    }



    //全局控制用户跳转链接是否设置了商品密码
    public function actionAjaxOverallPsd()
    {
        if (strlen(u()->password) <= 1) {
            // return error($this->renderPartial('_setPsd'));
            return success(url(['site/setPassword']), -1);
        }
        //如果要体现必须要有手机号
        if (strlen(u()->mobile) <= 10) {
            return success(url(['site/setMobile']), -1);
        }
        return success(post('url'));
    }
	public function actionUserguide()
    {
        $this->view->title = '交易规则';

        return $this->render('userguide');
    }
    //第一次设置商品密码
    public function actionSetPassword()
    {
        $this->view->title = '请设置商品密码';

        if (strlen(u()->password) > 1) {
            return $this->success(Yii::$app->getUser()->getReturnUrl(url(['site/index'])));
        }
        $model = User::findModel(u()->id);
        $model->scenario = 'setPassword';
        if ($model->load(post())) {
            if ($model->validate()) {
                $model->hashPassword()->save(false);
                $model->login(false);
                return $this->success(Yii::$app->getUser()->getReturnUrl(url(['site/index'])));
            } else {
                return error($model);
            }
        }
        $model->password = '';

        return $this->render('setPassword', compact('model'));
    }

    //第一次设置手机号码
    public function actionSetMobile()
    {
        $this->view->title = '请绑定手机号码';

        if (strlen(u()->mobile) > 10) {
            return $this->success(Yii::$app->getUser()->getReturnUrl(url(['site/index'])));
        }
        $model = User::findModel(u()->id);
        $model->scenario = 'setMobile';

        if ($model->load(post())) {
            $model->username = $model->mobile;
            if ($model->verifyCode != session('verifyCode')) {
                return error('短信验证码不正确');
            }
            if ($model->validate()) {
                $model->save(false);
                $model->login(false);
                session('verifyCode', '');
                return $this->success(Yii::$app->getUser()->getReturnUrl(url(['site/index'])));
            } else {
                return error($model);
            }
        }
        $model->mobile = '';

        return $this->render('setMobile', compact('model'));
    }
    //手动登录
    public function actionLogin()
    {
       // var_dump(user()->isGuest);
        //exit;

        if(!user()->isGuest)
        {
            return $this->redirect(['index']);
        }
        $model=new User(['scenario'=>'login']);
         if ($model->load(post())) {
            if ($model->handlogin()) {
                return $this->redirect(['index']);
            } else {
                return error($model);
            }
        }

        return $this->renderPartial('lognew', compact('model'));

    }


    public function actionRegister()
    {
        $this->view->title = '注册';

        $model = new User(['scenario' => 'register']);
        //session微信数据
        User::getWeChatUser(get('code'));

        if ($model->load(post())) {
            $model->username = $model->mobile;
            $user = User::findModel(get('id'));
            if (!empty($user)) {
                $model->pid = $user->id;
            }
            $wx = session('wechat_userinfo');
            if (!empty($wx)) {
                $model->face = $wx['headimgurl'];
                $model->nickname = $wx['nickname'];
                $model->open_id = $wx['openid'];
            }
            if ($model->validate()) {
                $model->hashPassword()->insert(false);
                $model->login(false);
                return success(url('site/index'));
                // return $this->goBack();
            } else {
                return error($model);
            }
        }

        return $this->render('register', compact('model'));
    }

//手动注册
    public function actionReg()
    {

        return $this->renderPartial('reg');
    }


    public function actionAjaxReg()
    {
        $data=post();
        $user = User::find()->where(['mobile' => $data['mobile']])->one();

        if(!empty($user))
        {
            return error('此手机号已经注册！');
        }

		$usera = User::find()->where(['username' => $data['username']])->one();

        if(!empty($usera))
        {
            return error('用户名已经存在！');
        }


        if(session('verifyCode'))
        {
            $verifyCode=session('verifyCode');//手机验证码
        }
        else
        {
            return error('手机验证码已失效，请重新获取！');
        }

        if($verifyCode!=$data['verifyCode'])
        {
            return error('手机验证码不正确！');
        }
        if(session('registerMobile')!=$data['mobile'])
        {
            return error('手机验证码与注册手机号不匹配！');
        }
        if($data['username']==""|| mb_strlen($data['username'],"UTF8")>5)
        {
            return error('用户名不能为空且不能超过5个字符');
        }
        if($data['inivde']=="")
        {
            return error('邀请码不能为空！');
        }
        else
        {
            $inivde=User::find()->where(['id'=>$data['inivde'],'is_manager'=>1])->one();
            if(!empty($inivde))
            {
                $data['pid']=$data['inivde'];
                $data['admin_id']=$inivde->admin_id;

            }
            else
            {
                return error('邀请人不存在或不是经纪人！');
            }
        }

        if($data['mobile']==''||$data['password']==''||$data['repassword']==''||$data['verifyCode']=='')
        {
            return error('请将信息填写完整后再提交！');
        }

        if($data['password']!=$data['repassword'])
        {
            return error('两次密码不一致！');
        }
        $data['sim_account']=config('sim_money');

        $result=User::addReg($data);
        if(!$result)
        {
            return error('注册失败！');
        }
        else
        {
            return success('注册成功，请牢记！');

        }


    }

    //重设密码页面
    public function actionPassForGet()
    {
        return $this->renderPartial('passforget');
    }

   //重设密码ajax提交
    public function actionAjaxForget()
    {
        $data=post();
        $user = User::find()->where(['username' => $data['mobile']])->one();
        if(empty($user))
        {
            return error('此手机号还未注册！');
        }


        if(session('verifyCode'))
        {
            $verifyCode=session('verifyCode');//手机验证码
        }
        else
        {
            return error('手机验证码已失效，请重新获取！');
        }

        if($verifyCode!=$data['verifyCode'])
        {
            return error('手机验证码不正确！');
        }
        if(session('registerMobile')!=$data['mobile'])
        {
            return error('手机验证码与注册手机号不匹配！');
        }

        if($data['mobile']==''||$data['password']==''||$data['repassword']==''||$data['verifyCode']=='')
        {
            return error('请将信息填写完整后再提交！');
        }

        if($data['password']!=$data['repassword'])
        {
            return error('两次密码不一致！');
        }

        $result=User::passforget($data);
        if(!$result)
        {
            return error('重设密码失败！');
        }
        else
        {
            return success('重设密码成功，请返回登录');

        }


    }

    public function actionForget()
    {
        $this->view->title = '忘记密码';
        $model = new User(['scenario' => 'forget']);

        if ($model->load(post())) {
            $user = User::find()->andWhere(['mobile' => post('User')['mobile']])->one();
            if (!$user) {
                return error('您还未注册！');
            }
            if ($model->validate()) {
                $user->password = $model->password;
                $user->hashPassword()->update();
                $user->login(false);

                return success(url('site/index'));
                // return $this->goBack();
            } else {
                return error($model);
            }
        }

        return $this->render('forget', compact('model'));
    }

    public function actionLogout()
    {
        user()->logout(false);

        return $this->redirect(['login']);
    }

	 public function actionVerifyCode()
    {
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $mobile = post('mobile');
		//var_dump($mobile);
		$type = post('typecheck',1);

		/*验证手机号码*/
		$usera = User::find()->where(['username' => $mobile])->one();

			if(!empty($usera) && $type !=2)
			{
				return ['state'=>0,'info'=>'此手机号已经注册！'];
			}
			if(empty($usera) && $type == 2)
			{
				return ['state'=>0,'info'=>'此手机号不存在！'];
			}


        require Yii::getAlias('@vendor/sms/ChuanglanSMS.php');
        // 生成随机数，非正式环境一直是1234
        $randomNum = YII_ENV_PROD ? rand(1024, 9951) : 1234;
        //$res = sendsms($mobile, $randomNum);

        if (!preg_match('/^1[34578]\d{9}$/', $mobile)) {

            return ['state'=>0,'info'=>'您输入的不是一个手机号码'];
        }
        $ip = str_replace('.', '_', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);


        if (session('ip_' . $ip)) {

            return ['state'=>0,'info'=>'短信已发送请在60秒后再次点击发送！'];
			var_dump($ip);exit;
        }

        /*$smsapi = "http://"; //短信网关
		$user = 's'; //短信平台帐号
		$content = '您好，您的验证码是' . $randomNum; //要发送的短信内容
		$sendurl = $smsapi . "?Uid=" . $user . "&Key=d41d8cd98f00b204e111&smsMob=" . $mobile . "&smsText=" . $content;
		$res = file_get_contents($sendurl);
		if ($res > 0) {
            session('ip_' . $ip, $mobile, 60);
            session('verifyCode', $randomNum, 1800);
            session('registerMobile', $mobile, 1800);
            return success('发送成功');
        } else {
            return error('发送失败');
        }
    }*/
//	$post_data = array();
//		$post_data['userid'] = 2054;
//		$post_data['account'] = 'zonmei';
//		$post_data['password'] = 'Abc635241.';
//		$post_data['content'] = '您的验证码是：'.$randomNum.'，如非本人操作，请忽略本短信【创世源码】'; //短信内容
//		$post_data['mobile'] = $mobile;
//		$post_data['sendtime'] = ''; //时定时发送，输入格式YYYY-MM-DD HH:mm:ss的日期值
//		$url='http://119.23.126.199:8888/sms.aspx/sms.aspx?action=send';
//		$o='';
//		foreach ($post_data as $k=>$v)
//		{
//		   $o.="$k=".urlencode($v).'&';
//		}
//		$post_data=substr($o,0,-1);
//		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_POST, 1);
//		curl_setopt($ch, CURLOPT_HEADER, 0);
//		curl_setopt($ch, CURLOPT_URL,$url);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
//		$xml = curl_exec($ch);
//
//		if(strpos($xml,'ok') !== false){
//			session('ip_' . $ip, $mobile, 60);
//            session('verifyCode', $randomNum, 1800);
//            session('registerMobile', $mobile, 1800);
//            return success('发送成功');
//		}else{
//			return error('发送失败');
//		}

         $res =  $this->curl_request('http://api.1cloudsp.com/api/v2/single_send',[
             'accesskey'=>'YzEnar5M55RtMZB3',
             'secret'=>'87rJ6G8SkhThrzPlct8S1uiXVyaY0Vfn',
             'sign'=>27702,
             'templateId'=>25950,
             'mobile'=>$mobile,
             'content'=>$randomNum
         ]);

            session('ip_' . $ip, $mobile, 60);
                    session('verifyCode', $randomNum, 1800);
                    session('registerMobile', $mobile, 1800);
                    return success('发送成功');



    }

    public function sendcode()
    {
        $phone = $_POST['phone'];
        $user_info = S($phone.'user');
        if (isset($user_info) && !empty($user_info)){

            $last_time =$user_info['time'];

            if (time() - $last_time < 60){
                $this->ajaxReturn(['status'=>401,'message'=>'请60秒后再发']);
            }
        }
        $data['phone'] = $phone;
        $data['time'] = time();
        $data['code'] = $this->generate_code(6);
        $this->check_phone($data['phone']);
        S($phone.'user',$data,180);
        $res =  $this->curl_request('http://api.1cloudsp.com/api/v2/single_send',[
            'accesskey'=>'YzEnar5M55RtMZB3',
            'secret'=>'87rJ6G8SkhThrzPlct8S1uiXVyaY0Vfn',
            'sign'=>12338,
            'templateId'=>25950,
            'mobile'=>$data['phone'],
            'content'=>$data['code']
        ]);
        $this->ajaxReturn(['status'=>200,'message'=>'验证码已发送']);

        /*   else{
               if ($_SESSION['users']['code'] != $user_code ){
                   $this->ajaxReturn(['status'=>403,'message'=>'验证码错误']);
               }
               $this->ajaxReturn(['status'=>200,'message'=>'验证通过']);

           }*/
    }

    private function curl_request($url,$post='',$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie']  = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }
    /**
     * 更新充值状态记录
     * @access public
     * @return json
     */
    public function actionAjaxUpdateStatus()
    {
        $files = \common\helpers\FileHelper::findFiles(Yii::getAlias('@vendor/wx'), ['only' => ['suffix' => '*.php']]);
        array_walk($files, function ($file) {
            require_once $file;
        });
        $wxPayDataResults = new \WxPayResults();
        //获取通知的数据
        $xml = file_get_contents('php://input');
        //如果返回成功则验证签名
        try {
            $result = \WxPayResults::Init($xml);
            //这笔订单支付成功
            if ($result['return_code'] == 'SUCCESS') {
                $userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no'=>$result['out_trade_no']])->one();
                //有这笔订单
                if (!empty($userCharge)) {
                    if ($userCharge->charge_state == UserCharge::CHARGE_STATE_WAIT) {
                        $user = User::findOne($userCharge->user_id);
                        $user->account += $userCharge->amount;
                        if ($user->save()) {
                            $userCharge->charge_state = 2;
                        }
                    }
                    $userCharge->update();
                    //输出接受成功字符
                    $array = ['return_code'=>'SUCCESS', 'return_msg' => 'OK'];
                    \WxPayApi::replyNotify($this->ToXml($array));
                    exit;
                }
            }
            test($result);
        } catch (\WxPayException $e){
            $msg = $e->errorMessage();
            self::db("INSERT INTO `test`(message, 'name') VALUES ('".$msg."', '微信回调')")->query();
            return false;
        }
    }

    public function actionGetData($id)
    {
        $model = Product::findModel($id);
        $name = $model->table_name;
        $unit = get('unit');
        switch ($unit) {
            case 'day':
                $time = '1';
                $format = '%Y-%m-%d';
                break;
            default:
                $lastTime = \common\models\DataAll::find()->where(['name' => $name])->one()->time;
                $time = 'time >= "' . date('Y-m-d H:i:s', time() - 3 * 3600 * 24) . '"';
                $format = '%Y-%m-%d %H:%i';
                break;
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $response->data = self::db('SELECT
                sub.*, cu.price close, UNIX_TIMESTAMP(DATE_FORMAT(time, "' . $format . '")) * 1000 time
        FROM
            (
                SELECT
                    min(d1.price) low,
                    max(d1.price) high,
                    d1.price open,
                    max(d1.id) id
                FROM
                    data_' . $name . ' d1
                where ' . $time . '
                group by
                    DATE_FORMAT(time, "' . $format . '")
            ) sub,
            data_' . $name . ' cu
        WHERE
            cu.id = sub.id')->queryAll();
        $response->send();
    }

    /**
     * 输出xml字符
     * @throws WxPayException
    **/
    private function ToXml($array)
    {
        $xml = "<xml>";
        foreach ($array as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function actionWrong()
    {
        $this->view->title = '错误';
        return $this->render('/user/wrong');
    }

    public function actionShareUrl()
    {
        if($this->newuser == 1) {
            $name = '已注册';
            $message = '您已经注册，5秒后自动跳转！';
        } else {
            $name = '注册成功！';
            $message = '您已经注册，5秒后自动跳转！';
        }
        // $this->view->title = '错误';

        return $this->render('error', compact('name', 'message'));
    }


    //每五分钟更新账户异常
    public function actionUpdateUser()
    {
        $bool = self::db('UPDATE `user` SET blocked_account= 0 WHERE blocked_account < 0')->queryAll();
        test($bool);
    }

    //订单凌晨四点平仓
    public function actionUpdate()
    {
        $extra = Product::find()->where(['state' => Product::STATE_VALID])->map('id', 'id');
        if ($extra) {
            $extraWhere = ' OR (order_state = ' . Order::ORDER_POSITION . ' and product_id in (' . implode(',', $extra) . '))';
        } else {
            $extraWhere = '';
        }
        $ids = self::db('SELECT o.id, a.price FROM `order` o INNER JOIN product p on p.id = o.product_id INNER JOIN data_all a on a.name = p.table_name where 
            (order_state = ' . Order::ORDER_POSITION . ' AND ((a.price >= stop_profit_point) OR (a.price <= stop_loss_point)))' . $extraWhere)->queryAll();
        array_walk($ids, function ($value) {
            Order::sellOrder($value['id'], $value['price']);
        });
        test($ids);
    }
    public function actionBalanceAccount()
    {
        $trans = Yii::$app->db->beginTransaction();
        try{
            $data = BalanceAccount::find()->asArray()->where(['status'=>1])->all();
            foreach ($data as $k=>$v){

                $user = User::findOne($v['user_id']);
                if ($user){
                    if ($v['profit_account']){
                        $user->profit_account += $v['profit_account'];
                    }else{
                        $user->loss_account += $v['loss_account'];
                    }
                    $user->account +=$v['balance'];
                    $res = $user->save();
                    if (!$res){
                        throw new \Exception("写入失败");
                        return false;
                    }
                    $model = BalanceAccount::findOne($v['id']);
                    $model->status = 2;
                    $res2 = $model->save(0);
                    if (!$res2){
                        throw new \Exception("写入失败");
                        return false;
                    }
                }
            }
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            echo $e;
        }

    }

}
