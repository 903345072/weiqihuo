<?php

namespace frontend\controllers;

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

class PayController extends \frontend\components\Controller
{

    //1111支付回调地址
	public function  actionApicback(){
		
			$partner = "17180";//商户ID
			
            $Key = "8b0c91fb63dc68c69afcc84b0ee27ebb";//商户KEY
			
            $orderstatus = $_GET["orderstatus"]; // 支付状态
			
            $ordernumber = $_GET["ordernumber"]; // 订单号
			
            $paymoney = $_GET["paymoney"]; //付款金额
			
            $sign = $_GET["sign"];	//字符加密串
			
            $attach = $_GET["attach"];	//订单描述
			
            $signSource = sprintf("partner=%s&ordernumber=%s&orderstatus=%s&paymoney=%s%s", $partner, $ordernumber, $orderstatus, $paymoney, $Key); //连接字符串加密处理
           		   
		if ($sign == md5($signSource))
		{
			$userCharge = UserCharge::find()->where('trade_no = :trade_no', [':trade_no' => $ordernumber])->one();
		
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
			
			echo "ok"; 
        }
		
        }else{
			echo "验证失败";
			}
	}

}
