<?php
namespace backend\controllers;
use backend\models\Logo;
use yii\web\Controller;
use yii\data\Pagination;
use yii\data\Sort;

use yii\web\Request;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;
class LogoController extends Controller{
    public function actionAdd($id){
        $models=Logo::find()->where(['goods_id'=>$id])->all();
        /*var_dump($models);exit;*/
        $request=new Request();
        if($request->isPost){
            $models->load($request->post());//load是向$model添加post提交的数据 除了图片文件

            if($models->validate()){//后台验证$model里面的字段


                $models->save();
                \Yii::$app->session->setFlash('successs','修改成功');
                return $this->redirect(['goods/index']);//注意 1要return 2 redirect里面要有中括号
            }else{
                var_dump($models->getErrors());
            }
        }
        return $this->render('add',['models'=>$models]);
    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $imgUrl=$action->getWebUrl();

                    //$action->output['fileUrl'] = $action->getWebUrl();
                    //调用七牛云组件，将图片上传到七牛云
                    $qiniu=\Yii::$app->qiniu;  //实例化定义的$qiniu方便使用里面的功能
                    $qiniu->upLoadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取该图片在七牛云的地址
                    $url=$qiniu->getLink($imgUrl);

                    $action->output['fileUrl']=$url;
                    /*  $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                      $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                      $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"*/
                },
            ],
        ];
    }
}