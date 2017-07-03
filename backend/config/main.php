<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'gii'=>[
            'class'=>'yii\gii\Module'
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
           // 'identityClass' => 'common\models\User',
            //在哪个Model中实现了IdentityInterface这个接口就写那个类
            'identityClass' => 'backend\models\User',
            'loginUrl'=>['user/login'],//设置默认登录页面 可以修改,
            'enableAutoLogin' => true,//如果是基于cookie的自动登录 这里要为true
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
         //将七牛云的功能放在了组件了 可以在这里改配置
        'qiniu'=>[//����ţ��ʹ����Ҫ���õ��ļ�������������ļ� ÿ��ʹ�ÿ��������������
            'class'=>\backend\components\Qiniu::className(),//新建了一个七牛云的类把七牛云的功能拷在了里面
            'up_host'=>'http://up-z2.qiniu.com',
            'accessKey'=>'oYzpv2MsK1xPLJcXQYHSEv_GL8cJ_NEswlr2nMY8',
            'secretKey'=>'XRiBGtYiz6lHnaHXcWxfoKIBxeGZhCWO7540JsyD',
            'bucket'=>'123456',
            'domain'=>'http://or9o0adkn.bkt.clouddn.com/',
        ],
    ],


    'params' => $params,
];
