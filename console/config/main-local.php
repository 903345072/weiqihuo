<?php
$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=weiqihuo',
            'username' => 'weiqihuo',
            'password' => 'PRH4YWF6nMPZTYDX',
            'charset' => 'utf8',
            'tablePrefix' => ''
        ],
       
    ],
];

return $config;


//return [
//    'bootstrap' => ['gii'],
//    'modules' => [
//        'gii' => 'yii\gii\Module',
//    ],
//];
