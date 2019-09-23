<?php

namespace admin\models;

use Yii;

class Product extends \common\models\Product
{
    public $trade_start_time;
    public $trade_end_time;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['trade_start_time', 'trade_end_time'], 'safe'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            // 'scenario' => ['field1', 'field2'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'currency' => '币种',
            'hot' => '是否热门',
            'force_sell' => '是否强制平仓',
            'on_sale' => '上架状态',
        ]);
    }
	public static function getIndexProduct()
    {
        $products = self::find()->where(['on_sale' => 1, 'state' => 1])->orderBy('hot DESC')->all();
        $arr = [];
        foreach ($products as $product) {
			$arr[$product->id]['id'] = $product->id;
            $arr[$product->id]['name'] = $product->name; 
            $arr[$product->id]['table_name'] = $product->table_name; 
            $arr[$product->id]['risk'] = $product->risk; 
            //$tradeData = (new Order)->tradeData($product->id);
            //$arr[$product->id]['upprice'] = $tradeData->deposit; 
            //$arr[$product->id]['close'] = $newData->close; 
        }
        return $arr;
    }

    public function listQuery()
    {
        return $this->search()
            ->andWhere(['product.state' => self::STATE_VALID]);
    }
	
}
