<?php

namespace admin\controllers;

use Yii;
use common\helpers\Hui;
use common\helpers\Html;
use common\helpers\Inflector;
use common\helpers\FileHelper;
use common\helpers\ArrayHelper;
use admin\models\AdminMenu;
use admin\models\AdminUser;
use admin\models\Article;
use admin\models\Retail;
use admin\models\User;
use admin\models\RingWechat;
use admin\models\AdminDeposit;
use common\modules\rbac\models\AuthItem;
use common\helpers\StringHelper;

/**
 * @author ChisWill
 */
class RecordController extends \admin\components\Controller
{

    /**
     * @authname 用户头寸统计记录
     */
    public function actionDepositList($id = null)
    {
        $query = (new AdminDeposit)->search()
            ->joinWith(['adminUser', 'user.admin'])
            ->selfManager();
        $count = $query->sum('amount') ?: 0;

        $html = $query->getTable([
            'admin_id' => ['header' => '运营中心ID'],
            'adminUser.username' => ['header' => '运营中心账号'],
            'amount' => ['header' => '头寸金额'],
            'user.nickname' => ['header' => '用户昵称'],
            'user.mobile',
            'user.admin.username' => ['header' => '归属会员单位账号'],
            'adminUser.power' => ['header' => '用户类型'],
            'created_at' => ['header' => '创建时间'],
        ], [
            'searchColumns' => [
                'admin_id' => ['header' => '运营中心ID'],
                'adminUser.username' => ['header' => '运营中心账号'],
                'user.nickname' => ['header' => '用户昵称'],
                'user.mobile',
            ],
            'ajaxReturn' => [
                'count' => $count
            ]
        ]);

        return $this->render('depositList', compact('html', 'count'));
    }
}
