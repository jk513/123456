<?php
namespace backend\controllers;
use backend\models\Brand;
use yii\data\Pagination;
use yii\data\Sort;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;


class BrandController extends Controller{
    public  function actionIndex(){

        $query=Brand::find()->where(['status'=>0])->orderBy('sort asc');
        $total=$query->count();
        $page=new Pagination(['totalCount'=>$total,
            'defaultPageSize'=>2,]);

        $models=$query->offset($page->offset)->limit($page->limit)->all();//直接通过model的名查找出里面所有的数据
        /*var_dump($models);
        exit;*/
        return $this->render('index',['models'=>$models,'page'=>$page]);//注意符号=>

    }
    public function actionAdd(){
        $model=new Brand();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());//load是向$model添加post提交的数据 除了图片文件
           /* $model->imgFile=UploadedFile::getInstance($model,'imgFile');*///往$model里面添加图片
            if($model->validate()){//后台验证$model里面的字段
              /*  if($model->imgFile){ //因为图片不是必须上传，所以$model->imgFile可能不存在，所以要判断
                    $fileName = '/images/brand'.uniqid().'.'.$model->imgFile->extension;//设置$filename是因为它是数据库图片存储目录
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);//把图片保存在文件夹里
                    $model->logo=$fileName;

                }*/
                  /*  var_dump($model);*/
                $model->save();
                \Yii::$app->session->setFlash('successs','添加成功');
              return $this->redirect(['brand/index']);//注意 1要return 2 redirect里面要有中括号
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);

    }
    public function actionEdit($id){
        $model=Brand::find()->where(['id'=>$id])->one();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());//load是向$model添加post提交的数据 除了图片文件

            if($model->validate()){//后台验证$model里面的字段


                $model->save();
                \Yii::$app->session->setFlash('successs','修改成功');
                return $this->redirect(['brand/index']);//注意 1要return 2 redirect里面要有中括号
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    public function actionDelete($id){
        $model=Brand::find()->where(['id'=>$id])->one();
        $model->status=1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);

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

    public function actionTest(){
        $ak = 'oYzpv2MsK1xPLJcXQYHSEv_GL8cJ_NEswlr2nMY8';
        $sk = 'XRiBGtYiz6lHnaHXcWxfoKIBxeGZhCWO7540JsyD';
        $domain = 'http://or9o0adkn.bkt.clouddn.com/';


        $bucket = '123456';

        $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
        $fileName= \Yii::getAlias('@webroot').'/upload/1.png';
        $key = '1.png';
        $re=$qiniu->uploadFile($fileName,$key);

        $url = $qiniu->getLink($key);
        var_dump($url);
    }
}