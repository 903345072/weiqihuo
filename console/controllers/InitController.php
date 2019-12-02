<?php

namespace console\controllers;

use common\helpers\System;
use console\models\GatherHuobi;
use console\models\GatherSina;
use console\models\GatherSinaStock;
use console\models\GatherXinfu;
use console\models\GatherYiyuan;
use frontend\controllers\SiteController;
use Yii;
use common\helpers\Curl;
use frontend\models\User;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\DataAll;
use frontend\models\UserCharge;
use common\helpers\FileHelper;
use common\helpers\Json;
use yii\log\FileTarget;
use \Exception;

class InitController extends \common\components\ConsoleController {
	public function actionUser() {
		echo 'Input User Info' . "\n";

		$username = $this->prompt('Input Username:');
		$password = $this->prompt('Input password:');

		$user = new \frontend\models\User;

		$user->username = $username;
		$user->password = $password;
		$user->setPassword();

		if (!$user->save()) {
			foreach ($user->getErrors() as $field => $errors) {
				array_walk($errors, function ($error) {
					echo "$error\n";
				});
			}
		}
	}
  
 public function actionData(){

     $fp = fopen(Yii::getAlias('@frontend').'/web/lock.txt', "r");

	    while (1){
	        try{
                if(flock($fp, LOCK_EX | LOCK_NB))
                {
                    $this->actionGatherdata();

                    flock($fp,LOCK_UN);
                } else {
                    echo 2;
                }
//关闭文件
                fclose($fp);

            }catch (Exception $e){
	            echo $e->getMessage();
            }
            sleep(2);
        }





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
                    if($v['code'] == 'hkHSI' || $v['code'] == 'nf_M0'){
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

	public function actionHq() {
		$cnt = 0;
		$path = System::isWindowsOs() ? '' : './';
		while (true) {
			$cnt++;
			// echo exec('yii init/gather2');
			echo exec('yii init/gather');
			echo exec('yii init/gather6');
			sleep(1);
			// echo cnt;
			// echo date('h:i:sa').'\n';
			if ($cnt > 9223372036854775807) {
				break;
			}

		}
	}

	public function actionGather() {
		$gather = new GatherSina;
		$gather->run();
	}

	public function actionGather2() {
		$gather = new GatherXinfu;
		$gather->run();
	}

	public function actionGather3() {
		$gather = new GatherSinaStock;
		$gather->run();
	}

	public function actionGather4() {
		$gather = new GatherYiyuan;
		$gather->run();
	}

	public function actionGather6() {

		$gather = new GatherHuobi;
		$gather->run();
	}
}
