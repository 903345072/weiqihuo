<?php

namespace admin\controllers;

use Yii;
use admin\models\Retail;
use admin\models\AdminUser;
use admin\models\AdminAccount;
use admin\models\AdminLeader;
use admin\models\User;
use admin\models\Order;
use admin\models\UserCharge;
use admin\models\UserWithdraw;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\StringHelper;
use common\modules\rbac\models\AuthItem;

class RetailController extends \admin\components\Controller
{
    /**
     * @authname 会员单位列表
     */
    public function actionList()
    {
        $query = (new Retail)->search()->joinWith('adminUser')->retail();
      
        $html = $query->getTable([
            'admin_id',
			 'account',
            'company_name' => ['type' => 'text'],
            'realname' => ['type' => 'text'],
            'tel' => ['type' => 'text'],
			
			
			['header' => '运营中心', 'value' => function ($row) {
				return $row->adminUser->getHuiyuaninfo($row->adminUser->pid);
            }],
			['header' => '下级总余额','value' => function ($row) {
                return User::find()->manager()->joinWith(['admin'])->andWhere(['user.is_moni'=>0,'admin.id'=>$row->admin_id])->select('SUM(account) account')->one()->account ?:0;
            }],
		    ['header' => '下级总盈亏','value' => function ($row) {
				$countQuery = (new Order)->listQuery()->joinWith(['user.parent', 'user.admin', 'user.adminUser'])->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id])->manager();
                return $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id,'order.is_moni'=>0])->select('SUM(order.profit) profit')->one()->profit ?: 0;
            }],
		    ['header' => '下级总手续费','value' => function ($row) {
                $countQuery = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id])->joinWith(['user.parent', 'user.admin', 'user.adminUser'])->manager();
                return $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'admin.id'=>$row->admin_id,'order.is_moni'=>0])->select('SUM(order.fee) profit')->one()->profit ?: 0;
            }],		    
			['header' => '下级总充值','value' => function ($row) {
                $countQuery = (new UserCharge)->listQuery()->joinWith(['user.admin', 'user.adminUser'])->manager();
                return $countQuery->andWhere(['charge_state' => 2,'admin.id'=>$row->admin_id])->select('SUM(amount) amount')->one()->amount ?: 0;
            }],
			['header' => '下级总提现','value' => function ($row) {
				$countQuery = (new UserWithdraw)->listQuery()->joinWith(['user', 'user.parent', 'user.admin', 'user.adminUser'])->andWhere(['op_state' => UserWithdraw::OP_STATE_PASS]);
                return $countQuery->andWhere(['user.is_moni' => 0,'admin.id'=>$row->admin_id])->select('SUM(amount) amount')->one()->amount ?: 0;
            }],
			
			
			
			

            'created_at',
			['header' => '查看下级','value' => function ($row) {
				$html = '<a class="parentLink" href="http://'.$_SERVER['HTTP_HOST'].'/admin/sale/manager-list?search%5Badmin.username%5D='.$row->account.'">下级经纪人</a>&nbsp&nbsp&nbsp&nbsp';
				$html .= '<a class="parentLink" href="http://'.$_SERVER['HTTP_HOST'].'/admin/user/list?search%5Badmin.username%5D='.$row->account.'">下级用户</a>&nbsp&nbsp&nbsp&nbsp';
                return $html;
            }],
			u()->power < 9999?'':['type' => ['delete']]

        ],[
            'searchColumns' => [
                'account',
                'company_name',
				'realname',
				'tel',
				'adminUser.pid' => ['header' => '运营中心ID'],
                'time' => ['header' => '', 'type' => 'dateRange'],
            ],
        ] );

        return $this->render('list', compact('html'));
    }

public function actionDelete()
{
    parent::actionDelete();
    $obj = AdminUser::findOne(post('id'));
    $obj->state = -1;
    $obj->save(0);
    
}

    /**
     * @authname 添加/编辑会员单位
     */
    public function actionSaveRetail($id = 0)
    {
        $model = Retail::findModel($id);
        $adminUser = new AdminUser;
		// 获取当前的所有角色
        $roles = AuthItem::getRoleQuery()->map('name', 'name');
        // 填充当前用户拥有的角色
        $authItem = new AuthItem;
        $authItem->roles = AdminUser::roles($id);
        if ($model->load()) {
            $model->code = StringHelper::random(6, 'n');
            $model->admin_id = rand(1000, 9999);
            if ($model->validate()) {
                if ($model->file1) {
                    $model->file1->move();
                    $model->id_card = $model->file1->filePath;
                }
                if ($model->file2) {
                    $model->file2->move();
                    $model->paper = $model->file2->filePath;
                }
                if ($model->file3) {
                    $model->file3->move();
                    $model->paper2 = $model->file3->filePath;
                }
                if ($model->file4) {
                    $model->file4->move();
                    $model->paper3 = $model->file4->filePath;
                }
                $model->save(false);
                $admin = new AdminUser;
                $admin->username = $model->account;
                $admin->password = $model->pass;
                $admin->realname = $model->realname;
                $adminUser = req('AdminUser');
                $admin->pid = isset($adminUser['pid']) ? $adminUser['pid'] : u()->id;
				
                if ($admin->saveAdmin()) {
					

                    $admin->power = AdminUser::POWER_MANAGER;
                    $admin->update(false);
					
                    $model->admin_id = $admin->id;
                    $model->update();
                } else {
                    $model->delete();
                    return error($admin);
                }
				$this->redirect(['/admin/retail/list']);
                return false;

            } else {
                return error($model);
            }
        }

        return $this->render('saveRetail', compact('model', 'authItem', 'roles', 'adminUser'));
    }

}
