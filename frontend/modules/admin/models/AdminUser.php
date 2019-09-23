<?php

namespace admin\models;

use Yii;

class AdminUser extends \common\models\AdminUser
{
    public $oldPassword;
    public $newPassword;
    public $cfmPassword;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['oldPassword', 'newPassword', 'cfmPassword'], 'required', 'on' => 'password'],
            [['oldPassword'], 'validateOldPassword'],
            [['newPassword'], 'compare', 'compareAttribute' => 'cfmPassword'],
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'password' => ['oldPassword', 'newPassword', 'cfmPassword'],
        ]);
    }

	public function getYunyinginfo($admin_id = 0)
    {
        if ($admin_id) {
            $adminUser = AdminUser::findOne($admin_id);
            return $adminUser->username.'/'.$adminUser->realname;
        } else {
            return Html::red('无');
        }
    }
	public function getHuiyuaninfo($admin_id = 0)
    {
        if ($admin_id > 0) {
            $adminUser = AdminUser::findOne($admin_id);
            return $adminUser->username.'/'.$adminUser->realname;
        } else {
            return Html::red('无');
        }
    }
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'oldPassword' => '旧密码',
            'newPassword' => '新密码',
            'cfmPassword' => '确认密码',
        ]);
    }

    public function validateOldPassword()
    {
        if (!u()->validatePassword($this->oldPassword)) {
            $this->addError('oldPassword', '旧密码不正确');
        }
    }
}
