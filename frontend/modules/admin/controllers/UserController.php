<?php

namespace admin\controllers;

use common\models\UserSearch;
use Yii;
use admin\models\User;
use admin\models\Order;
use admin\models\Product;
use admin\models\AdminUser;
use admin\models\UserCoupon;
use admin\models\UserCharge;
use admin\models\UserRebate;
use admin\models\UserWithdraw;
use admin\models\UserExtend;
use admin\models\Retail;
use common\helpers\Hui;
use common\helpers\Html;
use yii\grid\GridView;
use yii\log\FileTarget;

class UserController extends \admin\components\Controller
{


    public function actionList2(){

        $this->layout = false;
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchs(Yii::$app->request->queryParams);
        return $this->render('list2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'time'=>time()

        ]);
    }


    /**
     * @authname 会员列表
     */
    public function actionList()
    {

		$a = [
            'searchColumns' => [
                'id',
                'nickname',
                'mobile',
                'pid' => ['header' => '经纪人ID'],
                'admin.username' => ['header' => '会员单位帐号'],
                'admin.pid' => ['header' => '运营中心ID'],
				'time' => ['header' => '', 'type' => 'dateRange'],
            ],
			'addBtn' => ['saveuser' => '添加会员'],
			
			
			
        ];
		$b = [
            'searchColumns' => [
                'id',
                'nickname',
                'mobile',
                'pid' => ['header' => '经纪人ID'],
                'admin.username' => ['header' => '会员单位帐号'],
                'admin.pid' => ['header' => '运营中心ID'],
				'time' => ['header' => '', 'type' => 'dateRange'],
            ],
        ];
        $query = (new User)->listQuery()->andWhere(['user.is_moni' => 0])->manager();
		
		
        $html = $query->getTable([
		
            u()->power < AdminUser::POWER_ADMIN?'':['type' => 'checkbox'],
            'id',
            'nickname' => ['type' => 'text'],
            'mobile',
            'pid' => ['header' => '经纪人ID'],
			'admin.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
				
				
			    if($row->admin_id > 0){
                	return $row->getHuiyuaninfo($row->admin_id);
                }else{
                	return Html::red('无');
                }
            }],
            'admin.pid' => ['header' => '运营中心账号', 'value' => function ($row) {
            	if($row->admin_id > 0){
                	return $row->getLeaderName($row->admin_id).'/'.$row->getLeadertrueName($row->admin_id);
                }else{
                	return Html::red('无');
                }
            }],
            'account',
			['header' => '总盈亏', 'value' => function ($row) {
                  $totel = Order::find()->where(['user_id'=>$row->id])->sum('profit');
				return number_format($totel,2,'.','');
            }],
			['header' => '总充值', 'value' => function ($row) {
                $countQuery = (new UserCharge)->listQuery()->joinWith(['user', 'user.admin']);
				$UserChargecount = $countQuery->andWhere(['user_id' => $row->id])->andWhere(['charge_state' => 2])->select('SUM(amount) amount')->one()->amount ?: 0;
				return $UserChargecount;
            }],
			['header' => '总提现', 'value' => function ($row) {
                $countQuery = (new UserWithdraw)->listQuery()->joinWith(['user', 'user.admin'])->andWhere(['op_state' => UserWithdraw::OP_STATE_PASS]);
				$Withdrawtcount = $countQuery->andWhere(['user_id' => $row->id])->select('SUM(amount) amount')->one()->amount ?: 0;
				return $Withdrawtcount;
            }],
			['header' => '总手续费', 'value' => function ($row) {
                $countQuery = (new Order)->listQuery()->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();
				$fee = $countQuery->andWhere(['user_id' => $row->id])->select('SUM(order.fee) fee')->one()->fee ?: 0;
				return $fee;
            }],
            'created_at',
            'state' => ['search' => 'select'],
			
            u()->power < 9999?'':['header' => '操作', 'width' => '240px', 'value' => function ($row) {
                if ($row['state'] == User::STATE_VALID) {
                    $deleteBtn = Hui::dangerBtn('冻结', ['deleteUser', 'id' => $row->id], ['class' => 'deleteBtn']);
                } else {
                    $deleteBtn = Hui::successBtn('恢复', ['deleteUser', 'id' => $row->id], ['class' => 'deleteBtn']);
                }
                return implode(str_repeat('&nbsp;', 3), [
                    Hui::primaryBtn('修改密码', ['editUserPass', 'id' => $row->id], ['class' => 'editBtn']),
                    Hui::primaryBtn('修改经纪人', ['moveUser', 'id' => $row->id], ['class' => 'moveBtn']),
                    $deleteBtn
                ]);
            }]
        ],
		u()->power < 9999?$b:$a
		);

        // 会员总数，总手数，总余额,总盈利，总亏损
        $count = User::find()->manager()->count();
        $hand = Order::find()->joinWith(['user'])->manager()->andWhere(['sim'=>1])->select('SUM(hand) hand')->one()->hand ?: 0;
        $amount = User::find()->manager()->andWhere(['is_moni'=>0])->select('SUM(account) account')->one()->account ?: 0;
        return $this->render('list', compact('html', 'count', 'hand', 'amount', 'profit_account', 'loss_account'));
    }
	public function actionSaveuser($id = null)
    {
		$user = User::findModel($id);
		
		if ($user->load()) {
			$postdata = post('User');
			$usera = User::find()->where(['mobile' => $postdata['username']])->one();

			if(!empty($usera))
			{
				return error('此手机号已经注册！');
			}
			$userb = User::find()->where(['id' => $postdata['pid']])->one();
			if(empty($userb))
			{
				return error('经纪人ID不存在！');
			}
			$user->mobile = $postdata['username'];
			$user->admin_id = $userb->admin_id;
			
			$user->is_moni = 0;
			$user->password = md5($postdata['password']);
            if ($user->insert(false)) {
				$this->redirect(['/admin/user/list']);
                return false;
            } else {
                return self::error($user);
            }
        }

        return $this->render('saveuser', compact('user'));
    }
	public function actionMonilist(){
		$query = (new User)->listQuery()->andWhere(['user.is_moni' => 1])->manager();

        $html = $query->getTable([
            u()->power < AdminUser::POWER_ADMIN?'':['type' => 'checkbox'],
            'id',
			'username' => ['header' => '登陆帐号'],
            'nickname' => ['type' => 'text'],
            'mobile',
            'created_at',
        ],[
            'addBtn' => ['savemoniuser' => '添加模拟会员']
        ]);
		return $this->render('monilist', compact('html'));
	}
	public function actionSavemoniuser($id = null)
    {
		$user = User::findModel($id);
		
		if ($user->load()) {
			$postdata = post('User');
			$usera = User::find()->where(['mobile' => $postdata['username']])->one();

			if(!empty($usera))
			{
				return error('此手机号已经注册！');
			}
			$user->mobile = $postdata['username'];
			$user->is_moni = 1;
			$user->password = md5($postdata['password']);
			 $user->admin_id = u()->id;
            if ($user->insert(false)) {
				$this->redirect(['/admin/user/monilist']);
                return false;
            } else {
                return self::error($user);
            }
        }

        return $this->render('savemoniuser', compact('user'));
    }
	public function actionSavemanage($id = null)
    {
		$user = User::findModel($id);
		
		if ($user->load()) {
			$postdata = post('User');
            $user->is_moni = 1;
			$user->password = md5($postdata['password']);
            if ($user->insert(false)) {
                return self::success();
            } else {
                return self::error($user);
            }
        }

        return $this->render('savemanage', compact('user'));
    }
	
	

    /**
     * @authname 修改会员密码
     */
    public function actionEditUserPass() 
    {
        $user = User::findModel(get('id'));
        $user->password = md5(post('password'));
        if($user->update(false))
        {
            return success();
        }
        else
        {
            return error($user);
        }

    }
    /**
     * @authname 修改会员经纪人
     */
    public function actionMoveUser() 
    {
        $user = User::findModel(get('id'));
        $user->admin_id = post('admin_id');
        $admin=AdminUser::find()->where(['id'=>post('admin_id'),'power'=>9997])->one();
        if(!empty($admin))
        {
            if ($user->update(false)) {
            return success();
         } else {
            return error($user);
         }
        }
        else
        {
            return error('代理不存在');
        }
        
    }

    /**
     * @authname 冻结/恢复用户
     */
    public function actionDeleteUser() 
    {
        $user = User::find()->where(['id' => get('id')])->one();

        if ($user->toggle('state')) {
            return success('冻结成功！');
        } else {
            return success('账号恢复成功！');
        }
    }

    public function actionDeleteAll() 
    {
        $ids = post('list');
        foreach ($ids as $key => $value) {
            $model = User::findOne($value);
            $model->delete();          
        }
        return success('删除成功！');
        
    }

    /**
     * @authname 会员持仓列表
     */
    public function actionPositionList()
    {
        $query = (new User)->listQuery()->andWhere(['user.state' => User::STATE_VALID])->manager();

        $order = [];
        $html = $query->getTable([
            'id',
            'nickname' => ['type' => 'text'],
            'mobile',
            ['header' => '盈亏', 'value' => function ($row) use (&$order) {
                $order = Order::find()->where(['user_id' => $row['id'], 'order_state' => Order::ORDER_POSITION])->select(['SUM(hand) hand', 'SUM(profit) profit'])->one();
                if ($order->profit == null) {
                    return '无持仓';
                } elseif ($order->profit >= 0) {
                    return Html::redSpan($order->profit);
                } else {
                    return Html::greenSpan($order->profit);
                }
            }],
            ['header' => '持仓手数', 'value' => function ($row) use (&$order) {
                if ($order->hand == null) {
                    return '无持仓';
                } else {
                    return $order->hand;
                }
            }],
            'account',
            'state'
        ], [
            'searchColumns' => [
                'nickname',
                'mobile',
                'created_at' => ['type' => 'date']
            ]
        ]);

        return $this->render('positionList', compact('html'));
    }

    /**
     * @authname 会员赠金
     */
    public function actionGiveList()
    {
        if (req()->isPost) {
            $user = User::findModel(get('id'));
            $log = new FileTarget();
            $log->logFile = Yii::getAlias('@givemoney/give.log');
            $log->messages[] = ['给'.$user->nickname.'赠金'.post('amount'),8,'application',time()];
            $log->export();
            $user->account += post('amount');
            if ($user->update()) {
                return success();
            } else {
                return error($user);
            }
        }

        $query = (new User)->listQuery()->andWhere(['user.state' => User::STATE_VALID]);

        $html = $query->getTable([
            'id',
            'nickname',
            'mobile',
            
            'account' => ['header' => '资金'],
            ['header' => '操作', 'width' => '40px', 'value' => function ($row) {
                return Hui::primaryBtn('赠金', ['', 'id' => $row['id']], ['class' => 'giveBtn']);
            }]
        ], [
            'searchColumns' => [
                'nickname',
                'mobile'
            ]
        ]);

        return $this->render('giveList', compact('html'));
    }

    /**
     * @authname 会员出金管理
     */
    public function actionWithdrawList()
    {
        $query = (new UserWithdraw)->listQuery()->andWhere(['user.is_moni' => 0])->joinWith(['user.parent'])->orderBy('userWithdraw.created_at DESC');
        $countQuery = (new UserWithdraw)->listQuery()->joinWith(['user'])->andWhere(['op_state' => UserWithdraw::OP_STATE_PASS]);
        $count = $countQuery->andWhere(['user.is_moni' => 0])->select('SUM(amount) amount')->one()->amount ?: 0;

        $html = $query->getTable([
            'user.id',
            'user.nickname',
            'user.mobile',
            'user.account',
            'amount' => '出金金额',
            'fee' => '手续费2%',
			['header' => '实际打款', 'value' => function ($row) {
                return number_format($row->amount,2,'.','');
            }],
			['header' => '经纪人', 'value' => function ($row) {
                return $row->user->getParentLink1('user.id');
            }],
            'user.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
				return $row->user->getHuiyuaninfo($row->user->admin_id);
            }],
            'user.pid' => ['header' => '运营中心账号', 'value' => function ($row) {
				return $row->user->getLeaderName($row->user->admin_id).'/'.$row->user->getLeadertrueName($row->user->admin_id);
            }],
            ['header' => '总充值', 'value' => function ($row) {
				$countQuery = (new UserCharge)->listQuery()->joinWith(['user.admin'])->manager();
				$count = $countQuery->andWhere(['user_id' => $row->user->id])->select('SUM(amount) amount')->one()->amount ?: 0;
                return $count;
            }],
            'created_at',
            'op_state' => ['search' => 'select'],
            ['header' => '操作', 'width' => '70px', 'value' => function ($row) {
                if ($row['op_state'] == UserWithdraw::OP_STATE_WAIT && u()->power > AdminUser::POWER_LEADER) {
                    return Hui::primaryBtn('会员出金', ['user/verifyWithdraw', 'id' => $row['id']], ['class' => 'layer.iframe']);
                }if ($row['op_state'] == UserWithdraw::OP_STATE_WAIT && u()->power < AdminUser::POWER_ADMIN) {
                    return Html::successSpan('等待审核');
                } else {
                    return Html::successSpan('已审核');
                }
            }]
        ], [
            'searchColumns' => [
                'user.nickname',
                'user.mobile',
                'time' => ['header' => '审核时间', 'type' => 'dateRange']
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);
        

        return $this->render('withdrawList', compact('html', 'count'));
    }

    /**
     * @authname 会员出金操作
     */
    public function actionVerifyWithdraw($id)
    {
        $model = UserWithdraw::find()->with('user.userAccount')->where(['id' => $id])->one();
        $countQuery = (new UserWithdraw)->listQuery()->joinWith(['user'])->andWhere(['op_state' => UserWithdraw::OP_STATE_PASS]);
        $Withdrawtcount = $countQuery->andWhere(['user_id' => $model->user->id])->select('SUM(amount) amount')->one()->amount ?: 0;
		$countQuery = (new UserCharge)->listQuery()->joinWith(['user']);
        $UserChargecount = $countQuery->andWhere(['user_id' => $model->user->id])->select('SUM(amount) amount')->one()->amount ?: 0;
		$plosscount = $model->user->profit_account + $model->user->loss_account;
		
		
		
		
		

        if (req()->isPost) {
            $model->op_state = post('state');
			$model->op_reason = post('op_reason');
           
            if ($model->update()) {
                if ($model->op_state == UserWithdraw::OP_STATE_DENY) {
                    $model->user->account += $model->amount;
					$model->user->account += $model->fee;
                    $model->user->update();    
                }
                return success();
            } else {
                return error($model);
            }
        }

        return $this->render('verifyWithdraw', compact('model','Withdrawtcount','UserChargecount','plosscount'));
    }

    /**
     * @authname 会员充值记录
     */
    public function actionChargeRecordList()
    {
        $query = (new UserCharge)->listQuery()->andWhere(['user.is_moni' => 0])->joinWith(['user.parent', 'user.admin'])->manager()->orderBy('userCharge.id DESC');
		
        $countQuery = (new UserCharge)->listQuery()->joinWith(['user.admin'])->manager();
		
        $count = $countQuery->andWhere(['user.is_moni' => 0])->select('SUM(amount) amount')->one()->amount ?: 0;

        $html = $query->getTable([
            'user.id',
            'user.nickname' => '充值人',
            'user.mobile',
            'amount',
            ['header' => '经纪人', 'value' => function ($row) {
                return $row->user->getParentLink1('user.id');
            }],
            'admin.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
				return $row->user->getHuiyuaninfo($row->user->admin_id);
            }],
            'admin.pid' => ['header' => '运营中心账号', 'value' => function ($row) {
				return $row->user->getLeaderName($row->user->admin_id).'/'.$row->user->getLeadertrueName($row->user->admin_id);
            }],
            'user.account',
            'charge_type',
			'charge_state',
			'trade_no',
            'created_at'
        ], [
            'searchColumns' => [
                'user.id',
                'user.nickname',
                'user.mobile',
				'user.pid' => ['header' => '经纪人ID'],
                'admin.username' => ['header' => '经纪人账号'],
                'admin.pid' => ['header' => '运营中心ID'],
                'time' => ['header' => '充值时间', 'type' => 'dateRange'],
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);
		
        return $this->render('chargeRecordList', compact('html', 'count'));
    }
	

    /**
     * @authname 审核经纪人
     */
    public function actionVerifyManager()
    {
        if (req()->isPost) {
            $model = User::findModel(get('id'));
            $model->apply_state = get('apply_state');
            if ($model->apply_state == User::APPLY_STATE_PASS) {
                $model->is_manager = User::IS_MANAGER_YES;
                $userExtend = UserExtend::findOne($model->id);
            }
            if ($model->update()) {
                return success();
            } else {
                return error($model);
            }
        }

        $query = (new User)->listQuery()->joinWith(['userAccount', 'userExtend'])->manager()->andWhere(['user.apply_state' => User::APPLY_STATE_WAIT, 'user.state' => User::STATE_VALID]);

        $html = $query->getTable([
            'id',
            'nickname',
            'mobile' => ['header' => '注册手机号'],

            'admin.username' => ['header' => '经纪人账户'],
            'admin.pid' => ['header' => '运营中心账号', 'value' => function ($row) {
                return $row->getLeaderName($row->admin_id);
            }],
            'created_at',
            ['type' => [], 'value' => function ($row) {
                return implode(str_repeat('&nbsp;', 2), [
                    Hui::primaryBtn('审核通过', ['', 'id' => $row->id, 'apply_state' => User::APPLY_STATE_PASS], ['class' => 'verifyBtn']),
                    Hui::dangerBtn('不通过', ['', 'id' => $row->id, 'apply_state' => User::APPLY_STATE_DENY], ['class' => 'verifyBtn'])
                ]);
            }]
        ], [
            'searchColumns' => [
                'id',
                'nickname',
                'mobile',
                'admin.username' => ['header' => '经纪人账户'],
                'leader' => ['header' => '运营中心账号'],
            ]
        ]);

        return $this->render('verifyManager', compact('html'));
    }

}
