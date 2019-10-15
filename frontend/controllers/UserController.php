<?php

namespace frontend\controllers;

use Yii;
use frontend\models\User;
use frontend\models\UserAccount;
use frontend\models\UserWithdraw;
use frontend\models\ExuserWithdraw;
use frontend\models\UserCharge;
use frontend\models\UserRebate;
use frontend\models\UserExtend;
use common\helpers\FileHelper;
use frontend\models\Product;
use frontend\models\Order;
use frontend\models\ProductPrice;
use frontend\models\BankCard;
use frontend\models\Coupon;
use frontend\models\UserCoupon;
use frontend\models\DataAll;


class UserController extends \frontend\components\Controller
{
    public function beforeAction($action)
    {
        //$actions = ['ajax-update-status', 'wxtoken', 'wxcode', 'test', 'rule', 'captcha','notify', 'hx-weixin', 'zynotify', 'update-user', 'update', 'tynotify','login','reg','verify-code','ajax-reg','ajax-forget','pass-for-get'];
        $actions = ['recharge', 'pay'];
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

    public function actionIndex()
    {
        $this->view->title = '我的个人中心';
        if (user()->isGuest) {
            return $this->redirect('/site/login');
        }
         //test(u()->id);
        $user = User::findModel(u()->id);
        $manager = '申请经纪人';
        //如果是经纪人
        if ($user->is_manager == User::IS_MANAGER_YES) {
            $manager = '我是经纪人';
        }
    
        return $this->renderPartial('user_index', compact('user', 'manager'));
    }

    public function actionNews(){
        return $this->renderPartial('shareNew');
    }

    public function actionShare()
    {
        $this->view->title = '我的分享';
        if (user()->isGuest) {
            return $this->redirect('/site/login');
        }
        if(u()->is_manager==1)
        {
            $manager=UserExtend::find()->where(['user_id'=>u()->id])->one();
            $mpnum=User::find()->where(['pid'=>u()->id])->count();
            $mdnum=UserRebate::find()->where(['pid'=>u()->id])->count();
			
			//名下的用户
			$idArr = User::getUserOfflineId();
			$idArr = array_merge($idArr[0], $idArr[1]);
			$totalrecharge = UserCharge::find()->where(['charge_state' => UserCharge::CHARGE_STATE_PASS])->andWhere(['in', 'user_id', $idArr])->select('SUM(amount) amount')->one()->amount ?: 0;
			$totalwithdraw = UserWithdraw::find()->where(['op_state' => 2])->andWhere(['in', 'user_id', $idArr])->select('SUM(amount) amount')->one()->amount ?: 0;
			$alluser = User::find()->where(['in', 'id', $idArr])->all();
			
			$totalprofit = 0;
			$totalloss = 0;
			foreach($alluser as $vvv){
				$totalprofit += $vvv->profit_account;
				$totalloss += $vvv->loss_account;
			}
			/*$totalloss = User::find()->where(['pid'=>u()->id])->select('SUM(loss_account) amount')->one()->amount ?: 0;*/
			$totalyk = $totalprofit + $totalloss;
			$totalfeeall = Order::find()->where(['<>','order_state',1])->andWhere(['sim'=>1])->andWhere(['in', 'user_id', $idArr])->with('product')->all();
			$totalfee = 0;
			foreach($totalfeeall as $vvvv){
				$totalfee += $vvvv->fee;
			}
        }
        else
        {
			$totalyk=null;
			$totalfee=null;
			$totalrecharge=null;
			$totalwithdraw=null;
            $manager=null;
            $mpnum=null;
            $mdnum=null;
        }
        
        //生成二维码
        require Yii::getAlias('@vendor/phpqrcode/phpqrcode.php');

        $url = 'http://' . $_SERVER['HTTP_HOST'] . url(['/site/reg', 'id' => u()->id]); //二维码内容 
        $errorCorrectionLevel = 'L';//容错级别   
        $matrixPointSize = 6;//生成图片大小 
        $filePath = Yii::getAlias('@webroot/' . config('uploadPath') . '/images/');
        FileHelper::mkdir($filePath);
        $src = $filePath . 'code_' . u()->id . '.png';
        //生成二维码图片   
        \QRcode::png($url, $src, $errorCorrectionLevel, $matrixPointSize, 2);
        $img = config('uploadPath') . '/images/code_' . u()->id . '.png';  

        return $this->renderPartial('shareNew1', compact('img', 'url','manager','mpnum','mdnum','totalyk','totalfee','totalrecharge','totalwithdraw'));
    }

    public function actionWithDraw()
    {
        $this->view->title = '提现';

        $user = User::findModel(u()->id);
        $userAccount = UserAccount::find()->where(['user_id' => u()->id])->one();
        if (empty($userAccount)) {
            $userAccount = new UserAccount();
        }
        $userAccount->scenario = 'withDraw';
        $userWithdraw = new UserWithdraw();
        if ($userAccount->load(post()) || $userWithdraw->load(post())) {
            $userWithdraw->amount = post('UserWithdraw')['amount'];
            if (!is_numeric($userWithdraw->amount)) {
                return error('取现金额必须是数字');
            }

            if ($userWithdraw->amount < 100) {
                return error('单笔提现最少为100元！');
            }
			if ($userWithdraw->amount > 50000) {
                return error('单笔提现最大为50000元！<br> 单日不限笔数！');
            }

            if ($userWithdraw->amount < 0 || $userWithdraw->amount > ($user->account - $user->blocked_account - 2)) {
                return error('取现金额不能超过您的可用余额！');
            }
            if ($user->account < 0 || $user->blocked_account < 0) {
                return error('您的账户暂时不能提现');
            }
			/*if($user->is_moni != 1){
				if ($userAccount->verifyCode != session('verifyCode')) {
					return error('短信验证码不正确11');
				}
			}*/
            

            $userAccount->user_id = $userWithdraw->user_id = u()->id;
            $userAccount->bank_user = $userAccount->realname;
            $userAccount->id_card = 'xx';
            if ($userAccount->validate()) {
                if ($userAccount->id) {
                    $userAccount->update();
                } else {
                    $userAccount->insert(false);
                }
                $userWithdraw->account_id = $userAccount->id;
				$fee=($userWithdraw->amount)*0.02;
				$userWithdraw->fee = $fee;
				if($user->is_moni == 1){
					$userWithdraw->op_state = 2;
				}
                $userWithdraw->insert(false);
				//手续费2%
				
                //扣除取现金额
               // $user->account -= $userWithdraw->amount;
               $user->account = $user->account-$userWithdraw->amount-$userWithdraw->fee;
			    if ($user->account < 0) {
                return error('取现金额手续费不足！');
               }
                $user->save(false);
                session('verifyCode', null);
                return success('提现申请成功！');
            } else {
                return error($userAccount);
            }
        }
        return $this->render('withDraw', compact('userAccount', 'userWithdraw', 'user','fee'));
    }


    public function actionExWithDraw()
    {
        //$this->view->title = '提现';
        $data=post();
        //$user = User::findModel(u()->id);
        $userEx=UserExtend::find()->where(['user_id'=>u()->id])->one();
        //var_dump($userEx);
        //exit;
       
        $exuserWithdraw=new ExuserWithdraw();
            if (!is_numeric($data['money'])) {
                return error('取现金额必须是数字');
            }

            if ($data['money'] < 100) {
                return error('取现不能小于等于100元！');
            }

            if ($data['money'] < 0 || $data['money'] > $userEx->rebate_account) {
                return error('取现金额不能超过您的可用余额！');
            }
            $exuserWithdraw->user_id=u()->id;
            $exuserWithdraw->amount=$data['money'];
            $exuserWithdraw->op_state=1;
            $exuserWithdraw->insert(false);
            //扣除取现金额
            $userEx->rebate_account -= $exuserWithdraw->amount;
            $userEx->save(false);
            return success('提现申请成功！');
    }

    /*public function actionTransDetail()
    {
        $this->view->title = '商品明细';

        $query = Order::find()->where(['order_state' => Order::ORDER_THROW, 'user_id' => u()->id])->with('product')->orderBy('order.updated_at DESC');

        $data = $query->paginate(PAGE_SIZE);
        $count = $query->totalCount;
        $pageCount = $count / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p')) {
            return success($this->renderPartial('_transDetail', compact('data')), $pageCount);
        }

        return $this->render('transDetail', compact('count', 'pageCount', 'data'));
    }*/
    public function actionTransDetail1()//结算记录页面
    {
        if(!get('type')=='sim')
        {
            session('sim_type',null);//清除模拟盘标记
        }
        else
        {
            session('sim_type','sim');
        }
        return $this->renderPartial('orderDetail1');
    }
	public function actionTransDetail()//结算记录页面
    {
        if(!get('type')=='sim')
        {
            session('sim_type',null);//清除模拟盘标记
        }
        else
        {
            session('sim_type','sim');
        }
        return $this->renderPartial('orderDetail');
    }
     public function actionAjaxTransDetail()//结算记录ajax请求
    {
        if(session('sim_type')=='sim')
        {
            $query = Order::find()->where(['<>','order_state',1])->andWhere(['user_id' => u()->id,'sim'=>2])->with('product')->orderBy('order.updated_at DESC');
        }
        else
        {
            $query = Order::find()->where(['<>','order_state',1])->andWhere(['user_id' => u()->id,'sim'=>1])->with('product')->orderBy('order.updated_at DESC');
        }
        
        $data = $query->asArray()->paginate(PAGE_SIZE);
        $count = $query->totalCount;
        $pageCount = $count / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p')&&get('p')<=$pageCount) {
            return success("请求成功", $data);
        }
        return success("请求成功");
    }
    public function actionHoldStock()//持仓页面
    {
        if(!get('type')=='sim')
        {
            session('sim_type',null);//清除模拟盘标记
        }
        else
        {
            session('sim_type','sim');
        }
        return $this->renderPartial('orderHold');
    }
     public function actionAjaxHoldStock()//持仓记录ajax请求
    {
        if(session('sim_type')=='sim')
        {
            $data = Order::find()->where(['order_state'=>1,'user_id' => u()->id,'sim'=>2])->with('product')->orderBy('order.created_at DESC')->asArray()->all();
        }
        else
        {
             $data = Order::find()->where(['order_state'=>1,'user_id' => u()->id,'sim'=>1])->with('product')->orderBy('order.created_at DESC')->asArray()->all();
        }
		
		$aaa = array(
			'MAF'   => 1000,
			'RE'    => 1500,
			'hkHSI' => 1000,
			'coal'  => 2000,
		);
		
		
		
		foreach($data as $k=>$v){
			if(isset($aaa[$data[$k]['product']['code']])){
				$data[$k]['beishu'] = $data[$k]['deposit']/$aaa[$data[$k]['product']['code']];
			}else{
				/*$data[$k]['beishu'] = $data[$k]['deposit']/($data[$k]['product']['maxlost']/$data[$k]['product']['unit']*$data[$k]['product']['unit_price']);*/
				$data[$k]['beishu'] = $data[$k]['deposit']/1000;
			}
		}
		
		
		
        if(empty($data))
        {
            return error("请求成功",$data);

        }
        else
        {
            return success("请求成功",$data);

        }
        
    }

	






    public function actionOutMoney()
    {
        $this->view->title = '入金记录';

        $query = UserCharge::find()->where(['charge_state' => UserCharge::CHARGE_STATE_PASS, 'user_id' => u()->id])->orderBy('created_at DESC');
        $data = $query->paginate(PAGE_SIZE);
        $pageCount = $query->totalCount / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p') > 1) {
            return $this->renderPartial('_outMoney', compact('data'));
        }

        return $this->render('outMoney', compact('count', 'pageCount', 'data'));
    }
    /**
     * @authname 出金记录
     */
    public function actionInsideMoney()
    {
        $this->view->title = '出金记录';
        $query = UserWithdraw::find()->where(['user_id' => u()->id])->orderBy('created_at DESC');
        // 每页显示几条
        $data = $query->paginate();
        // 一共多少页
        $pageCount = $query->totalCount / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p') > 1) {
            return $this->renderPartial('_insideMoney', compact('data'));
        }
        return $this->render('insideMoney', compact('pageCount','data'));
    }


    public function actionSetting()
    {
        $this->view->title = '个人设置';

        return $this->render('setting');
    }
	
	 public function actionBankbind()
    {
        $this->view->title = '银行卡设置';

        return $this->render('bankbind');
    }
	
    public function actionManager()
    {
        $this->view->title = '申请经纪人';
        //如果是经纪人
        if (u()->is_manager == User::IS_MANAGER_YES) {
            $this->view->title = '我是经纪人';
            $idArr = User::getUserOfflineId();
            $data = User::getUserOfflineData($idArr);
            return $this->render('isManager', compact('data', 'idArr'));
        }

        if (Yii::$app->request->isPost) {
                $user = User::findModel(u()->id);
                $user->apply_state = User::APPLY_STATE_WAIT;
                $user->update();
                return success('信息提交成功！');
        }
        return $this->render('manager', compact('userAccount'));
    }
	
	
	public function actionHoldStock1()//持仓页面
    {
        if(!get('type')=='sim')
        {
            session('sim_type',null);//清除模拟盘标记
        }
        else
        {
            session('sim_type','sim');
        }
        return $this->renderPartial('orderHold1');
    }
	public function actionOfflineDetail() 
    {
		if(!get('uid')){
			return $this->redirect('/site/wrong');
		}
		//如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
		//名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);
		if(!in_array(get('uid'),$idArr)){
			return $this->redirect('/site/wrong');
		}
		
		
		
        $user = User::find()->where(['id' => get('uid')])->one();
		
		if(!$user){
			return $this->redirect('/site/wrong');
		}
		
		//名下的用户
		$totalrecharge = UserCharge::find()->where(['charge_state' => UserCharge::CHARGE_STATE_PASS,'user_id'=>get('uid')])->select('SUM(amount) amount')->one()->amount ?: 0;
		$totalwithdraw = UserWithdraw::find()->where(['op_state' => 2,'user_id'=>get('uid')])->select('SUM(amount) amount')->one()->amount ?: 0;
		$alluser = User::find()->where(['in', 'id', $idArr])->all();
		
		$totalfeeall = Order::find()->where(['<>','order_state',1])->andWhere(['sim'=>1,'user_id'=>get('uid')])->with('product')->all();
		$totalfee = 0;
		$mdnum = 0;
		foreach($totalfeeall as $vvvv){
			$totalfee += $vvvv->fee;
			$mdnum += $vvvv->hand;
		}
		
		
		
		
		
		return $this->render('offlineDetail', compact('user','totalrecharge','totalwithdraw','totalfee','mdnum'));
    }
	
	
     public function actionAjaxHoldStock1()//持仓记录ajax请求
    {
		//如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
		//名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);
        if(session('sim_type')=='sim')
        {
            $data = Order::find()->where(['order_state'=>1,'sim'=>2])->andWhere(['in', 'user_id', $idArr])->with('product')->with('user')->orderBy('order.created_at DESC')->asArray()->all();
        }
        else
        {
             $data = Order::find()->where(['order_state'=>1,'sim'=>1])->andWhere(['in', 'user_id', $idArr])->with('product')->with('user')->orderBy('order.created_at DESC')->asArray()->all();
        }
		
		
		
       
        if(empty($data))
        {
            return error("请求成功",$data);

        }
        else
        {
            return success("请求成功",$data);

        }
        
    }
	
	
	
	
	public function actionAjaxTransDetail1()//结算记录ajax请求
    {
		//如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
		//名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);
		
        if(session('sim_type')=='sim')
        {
            $query = Order::find()->where(['<>','order_state',1])->andWhere(['sim'=>2])->andWhere(['in', 'user_id', $idArr])->with('product')->with('user')->orderBy('order.updated_at DESC');
        }
        else
        {
            $query = Order::find()->where(['<>','order_state',1])->andWhere(['sim'=>1])->andWhere(['in', 'user_id', $idArr])->with('product')->with('user')->orderBy('order.updated_at DESC');
        }
        
        $data = $query->asArray()->paginate(PAGE_SIZE);
		
        $count = $query->totalCount;
        $pageCount = $count / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p')&&get('p')<=$pageCount) {
            return success("请求成功", $data);
        }
        return success("请求成功");
    }
	public function actionOutMoney1()
    {
        $this->view->title = '下级入金记录';
		//如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
		//名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);

        $query = UserCharge::find()->joinWith(['user'])->where(['userCharge.charge_state' => UserCharge::CHARGE_STATE_PASS])->andWhere(['in', 'userCharge.user_id', $idArr])->orderBy('userCharge.created_at DESC');
        $data = $query->paginate(PAGE_SIZE);
		
		
		
        $pageCount = $query->totalCount / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p') > 1) {
            return $this->renderPartial('_outMoney', compact('data'));
        }

        return $this->render('outMoney1', compact('count', 'pageCount', 'data'));
    }
	public function actionInsideMoney1()
    {
        $this->view->title = '下级出金记录';
		//如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
		//名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);
		
		
        $query = UserWithdraw::find()->joinWith(['user'])->where(['in', 'userWithdraw.user_id', $idArr])->orderBy('userWithdraw.created_at DESC');
        // 每页显示几条
        $data = $query->paginate();
        // 一共多少页
        $pageCount = $query->totalCount / PAGE_SIZE;
        if (!is_int($pageCount)) {
            $pageCount = (int)$pageCount + 1;
        }
        if (get('p') > 1) {
            return $this->renderPartial('_insideMoney', compact('data'));
        }
        return $this->render('insideMoney', compact('pageCount','data'));
    }
	
	

    public function actionMyOffline()
    {
        $this->view->title = '名下用户记录';
        //如果是经纪人
        if (u()->is_manager != User::IS_MANAGER_YES) {
            return $this->redirect('/site/wrong');
        }
        //名下的用户
        $idArr = User::getUserOfflineId();
        $idArr = array_merge($idArr[0], $idArr[1]);
        $query = User::find()->where(['state' => User::STATE_VALID])->andWhere(['in', 'id', $idArr])->orderBy('created_at DESC');
        $data = $query->paginate(PAGE_SIZE);

        return $this->render('myOffline', compact('data'));
    }
	
    /**
     * @authname 绑定银行卡
     */
    public function actionBankCard()
    {	
		
		
        $bankCard = BankCard::find()->where(['user_id' => u()->id])->one();
        if (empty($bankCard)) {
            $bankCard = new BankCard;
        }
        // test(u()->id);
        $bankCard->scenario = 'bank';
        $this->layout = 'empty';
        if($bankCard->load(post())) {
            if ($bankCard->validate()) {
                $bankCard->user_id = u()->id;
                if ($bankCard->id) {
                    $bankCard->update();
                } else {
                    $bankCard->insert(false);
                }
                $charge = UserCharge::epayBankCard($bankCard);
                if($charge) {
                    return success('绑定成功');
                }else {
                    return error('绑定失败，请确认您的信息是否正确');
                }
            } else {
                return error($bankCard);
            }         
        }
        return $this->render('bankCard', compact('bankCard'));
		
    }

    public function actionWrong()
    {
        $this->view->title = '错误';
        return $this->render('wrong');
    }

    public function actionRecharge()
    {     
        $this->view->title = '充值';
        $this->layout = 'empty';
        return $this->render('recharge');
    }

    public function actionWechatPay()
    {     
        $this->view->title = '网通支付';
        
        return $this->render('wechatPay');
    }

    public function actionXianxia(){

        return $this->render('xianxia',['type'=>get('type')]);
    }
  public function actionXianxia1(){

        return $this->render('xianxia1');
    }


    public function getman($url){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查

        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;    //返回json对象
    }

    public function actionPay()
    {
//        echo '<pre>';
//       var_dump(post());die;


        $amount=post('amount');
        $pay_type = post('pay');
         if ($pay_type == 'wx' || $pay_type == 'zfb' || $pay_type == 'yl'  || $pay_type=='' ){

             $this->redirect(['user/xianxia','type'=>$pay_type]);
          }

          if ($pay_type=='zz'){
              $this->redirect(['user/xianxia1']);
          }

          if ($pay_type == 'zd'){
              $order_no = u()->id . date("YmdHis") . rand(1000, 9999);
              $userCharge = new UserCharge();
              $userCharge->user_id = u()->id;
              $userCharge->trade_no = $order_no;
              $userCharge->amount = number_format($amount,2,'.','');
              $userCharge->charge_type = 1;
              $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
              $userCharge->save();
              $data['APPKey'] = 'c9daca83d23d44f686ec869ee9c4c129';
              $data['customerAmountCny'] = number_format($amount,2,'.','');
              $data['outOrderId'] = $order_no;
              $data['pickupUrl'] = 'http://'.$_SERVER['HTTP_HOST'];
              $data['receiveUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/site/starnotify';
              $data['signType'] = 'MD5';
              $data['sign'] = md5($data['outOrderId'].''.$data['pickupUrl'].$data['receiveUrl'].$data['customerAmountCny'].$data['signType'].'4d503bfd8b0c14ff0e02992315da73fe');
              $url = 'http://s.starfireotc.com/payLink/mobile.html?APPKey='.$data['APPKey'].'&customerAmountCny='.$data['customerAmountCny'].'&customerAmount='.'&outOrderId='.$data['outOrderId'].'&pickupUrl='.$data['pickupUrl'].'&receiveUrl='.$data['receiveUrl'].'&signType='.$data['signType'].'&sign='.$data['sign'];
             echo "<script>window.location.href='$url'</script>";
          }

        if ($pay_type == 'zd2'){
            $order_no = u()->id . date("YmdHis") . rand(1000, 9999);
            $userCharge = new UserCharge();
            $userCharge->user_id = u()->id;
            $userCharge->trade_no = $order_no;
            $userCharge->amount = number_format($amount,2,'.','');
            $userCharge->charge_type = 1;
            $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
            $userCharge->save();
            $url='http://www.yizhifu88.com/api/pay/gateway.html?';
            $data['merchant'] = 10046;
            $data['notify'] = 'http://'.$_SERVER['HTTP_HOST'].'/site/ylnotify';
            $data['ordercode'] = $order_no;
            $data['amount'] = number_format($amount,2,'.','');
            $data['key'] = '27CD4517298EEC5E245BBC3B740FDC38';
            $data['sign'] =  md5(http_build_query($data));
            unset($data['key']);
            $data_ = http_build_query($data);
            $url .= $data_;
            echo "<script>window.location.href='$url'</script>";
        }

        if ($pay_type == 'weixin' || $pay_type == 'zhifubao')
        {
            $type = $pay_type=='weixin'?1:2;
            $order_no = u()->id . date("YmdHis") . rand(1000, 9999);
            $userCharge = new UserCharge();
            $userCharge->user_id = u()->id;
            $userCharge->trade_no = $order_no;
            $userCharge->amount = number_format($amount,2,'.','');
            $userCharge->charge_type = $pay_type=='weixin'?2:1;
            $userCharge->charge_state = UserCharge::CHARGE_STATE_WAIT;
            $userCharge->save();

            $prices = $userCharge->amount;
            //支付业务中的相关订单信息。包括支付用户orderuid(选填),购买商品名goodsname(选填),订单号orderid(必填)
            $goodsname = "充值";
            //必填,用户订单号, 这里使用时间戳代替做测试。
            //必填，填写登陆后台查看到的Token及identification。严禁在客户端计算key，严禁在客户端存储Token。
            $token = "BJ4NIX3N36DQYM3TPD3T0PO9IYX8T3KD";
            $identification = "HESWF8JYBSNQGECU";
            $orderid = $order_no;
            //必填，填写支付成功后的回调通知地址及用户转向页面
            $return_url = 'http://'.$_SERVER['HTTP_HOST'];
            $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/site/o2o-zf';
            $orderuid = 'username';
            //验证key,不可以更改参数顺序。
            $prices = $prices*100;    //注意：020支付需要的参数单元为分;
            $keys = md5($goodsname. $identification. $notify_url. $orderid. $orderuid. $prices. $return_url. $token. $type);
            $returndata['price'] = $prices;
            $returndata['type'] = $type;
            $returndata['orderuid'] =$orderuid;
            $returndata['goodsname'] = $goodsname;
            $returndata['orderid'] = $orderid;
            $returndata['identification'] = $identification;
            $returndata['notify_url'] = $notify_url;
            $returndata['return_url'] = $return_url;
            $returndata['key'] = $keys;

            echo "<form style='display:none;' id='form1' name='form1' method='post' action='https://pay.020zf.com'>
              <input name='goodsname' id='goodsname' type='text' value='{$returndata["goodsname"]}' />
              <input name='type' id='type' type='text' value='{$returndata["type"]}' />
              <input name='key' id='key' type='text' value='{$returndata["key"]}'/>
              <input name='notify_url' id='notify_url' type='text' value='{$returndata["notify_url"]}'/>
              <input name='orderid' id='orderid' type='text' value='{$returndata["orderid"]}'/>
              <input name='orderuid' id='orderuid' type='text' value='{$returndata["orderuid"]}'/>
              <input name='price' id='price' type='text' value='{$returndata["price"]}'/>
              <input name='return_url' id='return_url' type='text' value='{$returndata["return_url"]}'/>
              <input name='identification' id='identification' type='text' value='{$returndata["identification"]}'/>
            </form>
            <script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";
        }


    
		}



     public function actionOnlinepay()
    {
     echo "opstate=0";  
        
    }

    public function actionPayMoney()
    {
        $this->view->title = '支付';
        
        return $this->render('payMoney');
    }

    public function actionPassword()
    {
        $this->view->title = '修改密码';

        $model = User::findOne(u('id'));
        //验证老密码
         if (Yii::$app->request->isPost){
             if (!post('User')['oldPassword'] || !post('User')['newPassword'] || !post('User')['cfmPassword']){
                 return self::error('请输入密码');
             }
             if ($model->password != md5(post('User')['oldPassword'])){
                 return self::error('原始密码错误');
             }
             if (post('User')['newPassword'] != post('User')['cfmPassword']){
                 return self::error('两次密码必须相同');
             }
             $model->password = md5(post('User')['newPassword']);
             if ($model->save()){
                 return $this->redirect(['index']);
             }
         }
        return $this->render('password', compact('model'));
    }

    public function actionChangePhone()
    {
        $this->view->title = '修改手机号';

        $model = User::findOne(u('id'));
        $model->scenario = 'changePhone';

        if ($model->load($_POST)) {
            if ($model->validate()) {
                $model->username = $model->mobile;
                if ($model->update()) {
                    return $this->redirect(['user/index']);
                } else {
                    return error($model);
                }
            } else {
                return error($model);
            }
        }
        $model->mobile = null;
		
        return $this->render('changePhone', compact('model'));
    }

    public function actionLogout()
    {
        user()->logout(false);

        return $this->redirect('/site/index');
    }


}

        