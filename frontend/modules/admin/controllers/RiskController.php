<?php

namespace admin\controllers;

use Yii;
use admin\models\Product;
use admin\models\Order;
use common\models\DataAll;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\ArrayHelper;

class RiskController extends \admin\components\Controller
{
    /**
     * @authname 风险控制
     */
    public function actionCenter()
    {
        $switch = option('risk_switch');
        $products = Product::find()->where(['on_sale' => Product::ON_SALE_YES, 'state' => Product::STATE_VALID])->asArray()->all();
        $risk_product = option('risk_product') ?: [];

        if (req()->isPost) {
            option('risk_switch', post('risk_switch'));
            if ($post = post('product', [])) {
                foreach ($post as $product => $value) {
                    $params[$product] = $value;
                }
                option('risk_product', $params);
            }

            return success();
        }

        return $this->render('center', compact('switch', 'products', 'risk_product'));
    }
	public function actionRisk()
    {
		if (req()->isPost) {
            $product = (new Product)->findModel(get('id'));
            $product->risk = post('risk');
            if ($product->update()) {
                return success();
            } else {
                return error($product);
            }
        }
		
        $query = (new Product)->listQuery()->orderBy('hot ASC');
		
		$countQuery = (new Order)->listQuery()->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();

		
		$productArr = Product::getIndexProduct();
		foreach($productArr as $k=>$v){
			$productArr[$k]['upprice'] = $countQuery->where(['order_state' => 1, 'rise_fall' => 1 , 'product_id' => $k])->select('SUM(order.deposit) deposit')->one()->deposit ?: 0;
			$productArr[$k]['downprice'] = $countQuery->where(['order_state' => 1, 'rise_fall' => 2 , 'product_id' => $k])->select('SUM(order.deposit) deposit')->one()->deposit ?: 0;
		}

        return $this->render('risk', compact('productArr'));
    }
	public function actionSetRisk($id)
    {
        $model = Product::findOne($id);

        if ($model->load(post())) {
            $data = [];
            foreach ($model->trade_start_time as $key => $value) {
                if ($value && $model->trade_end_time[$key]) {
                    $item = [];
                    $item['start'] = $value;
                    $item['end'] = $model->trade_end_time[$key];
                    $data[] = $item;
                }
            }
            $model->trade_time = serialize($data);
            $model->update(false);
            return success('设置成功');
        }
        if ($model->trade_time) {
            $time = unserialize($model->trade_time);
        }
        if (empty($time)) {
            $time = [
                ['start' => '', 'end' => ''],
            ];
        }

        return $this->render('setRisk', compact('model', 'time'));
    }
	public function actionRisk1()
    {
        $countQuery = (new Order)->listQuery()->joinWith(['product.dataAll', 'user.parent', 'user.admin'])->manager();

		
		$productArr = Product::getIndexProduct();
		foreach($productArr as $k=>$v){
			$productArr[$k]['upprice'] = $countQuery->where(['order_state' => 1, 'rise_fall' => 1 , 'product_id' => $k])->select('SUM(order.deposit) deposit')->one()->deposit ?: 0;
			$productArr[$k]['downprice'] = $countQuery->where(['order_state' => 1, 'rise_fall' => 2 , 'product_id' => $k])->select('SUM(order.deposit) deposit')->one()->deposit ?: 0;
		}
        return success($productArr);
    }
}
