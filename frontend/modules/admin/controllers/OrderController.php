<?php

namespace admin\controllers;

use Yii;
use admin\models\Order;
use admin\models\User;
use admin\models\AdminUser;
use common\helpers\Hui;
use common\helpers\Html;

class OrderController extends \admin\components\Controller
{
    /**
     * @authname 订单列表
     */
    public function actionList()
    {
        $query = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager()->orderBy('order.id DESC');
        $countQuery = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();

        // 今日盈亏统计，交易额、手数
        $amount = $countQuery->select('SUM(order.deposit) deposit')->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->one()->deposit ?: 0;
        $hand = $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->select('SUM(order.hand) hand')->one()->hand ?: 0;
        $fee = $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->select('SUM(order.fee) fee')->one()->fee ?: 0;
        $profit = $countQuery->andWhere(['order.order_state' => Order::ORDER_THROW,'order.is_moni'=>0])->select('SUM(order.profit) profit')->one()->profit ?: 0;

        $html = $query->getTable([
            'user.id',
            'user.nickname',
            'user.pid' => ['header' => '经纪人ID'],
            'admin.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
				if(isset($row->user->admin_id)){
					return $row->user->getHuiyuaninfo($row->user->admin_id);
				}else{
					return '无';
				}
                
            }],
            'admin.pid' => ['header' => '运营中心帐号', 'value' => function ($row) {
				if(isset($row->user->admin_id)){
					return $row->user->getLeaderName($row->user->admin_id).'/'.$row->user->getLeadertrueName($row->user->admin_id);
				}else{
					return '无';
				}
                
            }],
            'product.name',
            'created_at',
            'updated_at' => function ($row) {
                return $row['order_state'] == Order::ORDER_POSITION ? '' : $row['updated_at'];
            },
            'rise_fall' => ['header' => '涨跌', 'value' => function ($row) {
                return $row['rise_fall'] == Order::RISE ? Html::redSpan('买涨') : Html::greenSpan('买跌');
            }],
            'fee',
			'price' => ['header' => '建仓点位'],
            'sell_price' => ['header' => '平仓点位'],
            

            'hand',
            'deposit',
            'profit' => function ($row) {
                return $row['profit'] >= 0 ? Html::redSpan($row['profit']) : Html::greenSpan($row['profit']);
            },
			['header' => '盈亏比', 'value' => function ($row) {
                return $row['profit'] >= 0 ? Html::redSpan(round(($row['profit']*100)/$row['deposit'],2).'%') : Html::greenSpan(round(($row['profit']*100)/$row['deposit'],2).'%');
            }],
            'order_state',

        ], [
            'ajaxReturn' => [
                'countProfit' => '盈亏统计：' . ($profit >= 0 ? Html::redSpan($profit) : Html::greenSpan($profit)) . '，',
                'countAmount' => $amount,
                'countHand' => $hand,
                'countFee' => $fee
            ],
            'searchColumns' => [
                'user.id',
                'user.pid' => ['header' => '经纪人ID'],
                'admin.username' => ['header' => '会员单位帐号'],
                'admin.pid' => ['header' => '运营中心ID'],
                'product_name' => ['type' => 'select', 'header' => '选择产品'],
                'user.nickname',
                'time' => 'timeRange',
                'is_profit' => ['type' => 'select', 'header' => '是否盈亏'],
            ]
        ]);

        return $this->render('list', compact('html', 'profit', 'amount', 'hand', 'fee'));
    }
	
	public function actionOnlist()
    {
        $query = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager()->orderBy('order.id DESC');
        $countQuery = (new Order)->listQuery()->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();

        // 今日盈亏统计，交易额、手数
        $amount = $countQuery->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->select('SUM(order.deposit) deposit')->one()->deposit ?: 0;
        $hand = $countQuery->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->select('SUM(order.hand) hand')->one()->hand ?: 0;
        $fee = $countQuery->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->select('SUM(order.fee) fee')->one()->fee ?: 0;
        $profit = $countQuery->andWhere(['order.order_state' => Order::ORDER_POSITION,'order.is_moni'=>0])->select('SUM(order.profit) profit')->one()->profit ?: 0;

        $html = $query->getTable([
            'user.id',
            'user.nickname',
            'user.pid' => ['header' => '经纪人ID'],
            'admin.username' => ['header' => '会员单位帐号', 'value' => function ($row) {
            	if($row->user->admin_id){
					return $row->user->getHuiyuaninfo($row->user->admin_id);
				}else{
					return '无';
				}
                
            }],
            'admin.pid' => ['header' => '运营中心帐号', 'value' => function ($row) {
            	if($row->user->admin_id){
					return $row->user->getLeaderName($row->user->admin_id).'/'.$row->user->getLeadertrueName($row->user->admin_id);
				}else{
					return '无';
				}
                
            }],
            'product.name',
            'created_at',

            'rise_fall' => ['header' => '涨跌', 'value' => function ($row) {
                return $row['rise_fall'] == Order::RISE ? Html::redSpan('买涨') : Html::greenSpan('买跌');
            }],
            'fee',
            'price' => ['header' => '建仓点位'],

            'hand',
            'deposit',
            'profit' => function ($row) {
                return $row['profit'] >= 0 ? Html::redSpan($row['profit']) : Html::greenSpan($row['profit']);
            },
			['header' => '盈亏比', 'value' => function ($row) {
                return $row['profit'] >= 0 ? Html::redSpan(round(($row['profit']*100)/$row['deposit'],2).'%') : Html::greenSpan(round(($row['profit']*100)/$row['deposit'],2).'%');
            }],
            'order_state',
            //            ['type' => [], 'value' => function ($row) {
//                if (u()->power >= AdminUser::POWER_ADMIN && $row['order_state'] == Order::ORDER_POSITION) {
//                    return Hui::primaryBtn('平仓', ['sellOrder', 'id' => $row['id']], ['class' => 'sellOrder']);
//                }
//            }]
        ], [
            'ajaxReturn' => [
                'countProfit' => '盈亏统计：' . ($profit >= 0 ? Html::redSpan($profit) : Html::greenSpan($profit)) . '，',
                'countAmount' => $amount,
                'countHand' => $hand,
                'countFee' => $fee
            ],
            'searchColumns' => [
                'user.id',
                'user.pid' => ['header' => '经纪人ID'],
                'admin.username' => ['header' => '会员单位帐号'],
                'admin.pid' => ['header' => '运营中心ID'],
                'product_name' => ['type' => 'select', 'header' => '选择产品'],
                'user.nickname',
                'time' => 'timeRange',
                'is_profit' => ['type' => 'select', 'header' => '是否盈亏'],

            ]
        ]);

        return $this->render('list', compact('html', 'profit', 'amount', 'hand', 'fee'));
    }



    /**
     * @authname 手动平仓
     */
    public function actionSellOrder()
    {
        $id = get('id');
        $price = post('price');
        if ($price < 0 || !is_numeric($price)) {
            return error('价格数据非法！');
        }
        if (Order::sellOrder($id, $price)) {
            return success('成功平仓');
        } else {
            return error('此单已平');
        }
    }
}
