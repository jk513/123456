<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=>'zh-CN',//这里把语言设置为中文后，后面使用的时候提示语就会是中文
    'charset'=>'utf-8',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',

        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'authManager'=>[
          'class'=>\yii\rbac\DbManager::className(),
        ],
    ],
];
