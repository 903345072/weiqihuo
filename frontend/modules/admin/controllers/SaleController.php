<?php

namespace admin\controllers;

use Yii;
use admin\models\User;
use admin\models\AdminUser;
use admin\models\UserExtend;
use admin\models\Retail;
use admin\models\UserRebate;
use admin\models\UserCharge;
use admin\models\UserWithdraw;
use admin\models\Order;
use common\helpers\Hui;
use common\helpers\Html;

class SaleController extends \admin\components\Controller
{
    /**
     * @authname 经纪人列表
     */
    public function actionManagerList()
    {
		
		$query = (new User)->managerQuery()->joinWith(['userAccount', 'userExtend', 'admin'])->andWhere(['user.is_moni' => 0,'user.is_manager' => User::IS_MANAGER_YES])->orderBy('total_fee DESC')->manager();
		

        $html = $query->getTable([
            u()->power < AdminUser::POWER_ADMIN?'':['type' => 'checkbox'],
            'id',
            'nickname' => ['type' => 'text'],
            'mobile',

			'admin.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
				return $row->getHuiyuaninfo($row->admin_id);
            }],

			['header' => '下级总余额','value' => function ($row) {
                return User::find()->manager()->joinWith(['admin'])->andWhere(['user.is_moni'=>0,'user.pid'=>$row->id])->select('SUM(account) account')->one()->account ?: '0.00';
            }],
		    ['header' => '下级总盈亏','value' => function ($row) {
				$countQuery = (new Order)->listQuery()->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id])->manager();
                return $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'user.pid'=>$row->id,'order.is_moni'=>0])->select('SUM(order.profit) profit')->one()->profit ?: 0;
            }],
		    ['header' => '下级总手续费','value' => function ($row) {
                $countQuery = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id])->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();
                return $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'user.pid'=>$row->id,'order.is_moni'=>0])->select('SUM(order.fee) profit')->one()->profit ?: 0;
            }],

		    ['header' => '下级总充值','value' => function ($row) {
                $countQuery = (new UserCharge)->listQuery()->joinWith(['user.admin'])->manager();
                return $countQuery->andWhere(['charge_state' => 2,'user.pid'=>$row->id])->select('SUM(amount) amount')->one()->amount ?: 0;
            }],

			['header' => '下级总提现','value' => function ($row) {
				$countQuery = (new UserWithdraw)->listQuery()->joinWith(['user', 'user.parent', 'user.admin'])->andWhere(['op_state' => UserWithdraw::OP_STATE_PASS]);
                return $countQuery->andWhere(['user.is_moni' => 0,'user.pid'=>$row->id])->select('SUM(amount) amount')->one()->amount ?: 0;
            }],
			
			['header' => '查看下级','value' => function ($row) {
				$html = '<a class="parentLink" href="http://'.$_SERVER['HTTP_HOST'].'/admin/user/list?search%5Bpid%5D='.$row->id.'">下级用户</a>&nbsp&nbsp&nbsp&nbsp';
                return $html;
            }],
            'created_at',
            'state' => ['search' => 'select'],
            ['header' => '操作', 'width' => '240px', 'value' => function ($row) {
                if ($row['state'] == User::STATE_VALID) {
                    $deleteBtn = Hui::dangerBtn('冻结', ['deleteUser', 'id' => $row->id], ['class' => 'deleteBtn']);
                } else {
                    $deleteBtn = Hui::successBtn('恢复', ['deleteUser', 'id' => $row->id], ['class' => 'deleteBtn']);
                }
                return implode(str_repeat('&nbsp;', 3), [
                    $deleteBtn
                ]);
            }]
        ],[
            'searchColumns' => [
                'id',
                'nickname',
                'mobile',
                'admin.username' => ['header' => '会员单位帐号'],
                'admin.pid' => ['header' => '运营中心ID'],
				'time' => ['header' => '', 'type' => 'dateRange'],
            ]
        ]);

        // 会员总数，总手数，总余额,总盈利，总亏损
        $count = User::find()->manager()->count();
        $hand = Order::find()->joinWith(['user'])->manager()->andWhere(['sim'=>1])->select('SUM(hand) hand')->one()->hand ?: 0;
        $amount = User::find()->manager()->andWhere(['is_moni'=>0])->select('SUM(account) account')->one()->account ?: 0;

        return $this->render('managerList', compact('html', 'count', 'hand', 'amount', 'profit_account', 'loss_account'));


        return $this->render('managerList', compact('html'));
    }
	public function actionSavemanage($id = null)
    {
		$user = User::findModel($id);
		
		if ($user->load()) {
			$postdata = post('User');
            $user->is_manager = 1;
			$user->password = md5($postdata['password']);
            if ($user->insert(false)) {
				$this->redirect(['/admin/sale/manager-list']);
                return false;
				
            } else {
                return self::error($user);
            }
        }

        return $this->render('savemanage', compact('user'));
    }

}
