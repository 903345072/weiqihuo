<?php

namespace frontend\models;

use Yii;

class UserCharge extends \common\models\UserCharge
{
    public function rules()
    {
        return array_merge(parent::rules(), [
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
        ]);
    }


	    //11111支付
	    public static function apipay($amount,$pay_type){
        //保存充值记录
        $userCharge = new UserCharge();
        $userCharge->user_id = u()->id;
        $userCharge->trade_no = u()->id . date("YmdHis") . rand(1000, 9999);
        $userCharge->amount = $amount;
        if($pay_type == 'zz') {
            $userCharge->charge_type = 5;//微信支付
        }else if($pay_type == 'zh5'){
            $userCharge->charge_type = 4;//QQ支付 
        }
		
        $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
		
        if (!$userCharge->save()) {
            return false;
        }
			$apiurl = "/codepay";	//网关接口地址
			//支付QQ：http://uacp12.abcapi.cn/ QQ3429610735  1679423517 415483118 836525916
			
            $partner = "17180";  //商户号
			
            $key = "8b0c91fb63dc68c69afcc84b0ee27ebb";		//MD5密钥，安全检验码
			
            $ordernumber = $userCharge->trade_no; //商户订单号
			
            $bank =$userCharge->charge_type; //支付类型
			
			if($bank == 5){				
				$banktype =	"ALIPAY";
			}elseif($bank == 4){
				$banktype = "ALIPAY";
			}
		
            $attach = "yhcz";  //订单描述
			
            $paymoney = $userCharge->amount; // 付款金额
			
            $callbackurl = 'http://'.$_SERVER['HTTP_HOST'].'/pay/apicback'; //服务器异步通知页面路径
			
            $hrefbackurl = 'http://'.$_SERVER['HTTP_HOST'].'/user'; //页面跳转同步通知页面路径
			
            $signSource = sprintf("partner=%s&banktype=%s&paymoney=%s&ordernumber=%s&callbackurl=%s%s", $partner, $banktype, $paymoney, $ordernumber, $callbackurl, $key); //字符串连接处理
            $sign = md5($signSource);  //字符串加密处理
            $postUrl = $apiurl. "?banktype=".$banktype;
			$postUrl.="&partner=".$partner;
            $postUrl.="&paymoney=".$paymoney;
            $postUrl.="&ordernumber=".$ordernumber;
            $postUrl.="&callbackurl=".$callbackurl;
            $postUrl.="&hrefbackurl=".$hrefbackurl;
            $postUrl.="&attach=".$attach;
            $postUrl.="&sign=".$sign;
			header ("location:$postUrl");
        exit('1');
    }

}