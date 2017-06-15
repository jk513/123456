<?php
namespace backend\controllers;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use backend\models\Logo;
use xj\uploadify\UploadAction;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\UploadedFile;

class GoodsController extends Controller{
  public function actionAdd(){
      $model=new Goods(['scenario'=>Goods::SCENARIO_ADD]);
      $request=new Request();
      if($request->isPost){
          $model->load($request->post());
        /* var_dump($model);
          exit;*/
          $goods_intro=new GoodsIntro();
          $goods_intro->content=$model->content;
          $model->imgFile=UploadedFile::getInstance($model,'imgFile');
          if($model->validate()){
              //保存图片
              $fileName = '/images/'.uniqid().'.'.$model->imgFile->extension;
              /*var_dump($fileName);
              exit;*/
              $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
              //图片地址赋值
              $model->logo=$fileName;
              $date=date('Ymd');

              $good_day_count=GoodsDayCount::find()->where(['day'=>$date])->one();
              if($good_day_count){
                 $count=$good_day_count->count+1;
                  $good_day_count->count=$count;
                  $good_day_count->save();
              }else{
                  $good_day_count=new GoodsDayCount();
                $good_day_count->day=$date;
                $good_day_count->count=1;

                  $good_day_count->save();
              }

              $model->sn=date('Ymd')*100000+$good_day_count->count;
              $model->save(false);//要加false 因为这里save的时候默认还要验证一次

              $goods_intro->goods_id=$model->id;
              $goods_intro->save(false);
              \Yii::$app->session->setFlash('success','添加成功');
              return $this->redirect(['goods/index']);
          }
      }

      $categories=GoodsCategory::find()->asArray()->all();
      return $this->render('add',['model'=>$model,'categories'=>$categories]);
  }


    public function actionIndex(){
     //实例化GoodsSearchForm()生成搜索表单 并用里面定义的search方法查找符合条件的数据
        $model=new GoodsSearchForm();
        $query=Goods::find();//定义query语句，下面传入GoodsSearchForm()中的search方法中拼接
        $model->search($query);//通过model调用里面的search方法并传入$query参数
        //上面这句返回的不是得到符合条件的对象，而只是重新拼接了query语句
        //接收get得到的参数和搜索条件都封装在了search()方法里
        /*提问：为什么可以在模型里面通过$this->load(\Yii::$app->request->get());接收get参数
        答案：当然可以在Model里面通过$this->load接收，想一下，平时在controller中就是先new一个model
        通过$model->load(),这里直接在Model里面当然就是$this了,不是只能在controller里面接收参数*/
        $pager=new Pagination([//实例化分页对象 实现分页操作
            'totalCount'=>$query->count(),//一共多少页
            'pageSize'=>3//一页显示3条
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();//这里是查询获得所有符合条件的对象
        return $this->render('index',['model'=>$model,'pager'=>$pager,'models'=>$models]);
    }

    public function actionEdit($id){
        $model=Goods::find()->where(['id'=>$id])->one();

        $model->scenario=Goods::SCENARIO_EDIT;
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
           /*var_dump($model);exit;*/


            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            if(!$model->content){
                $goods_intro=GoodsIntro::find()->where(['goods_id'=>$id])->one();
                $model->content=$goods_intro->content;
            }

            if($model->validate()){
               if($model->imgFile){

                   //保存图片
                   $fileName = '/images/'.uniqid().'.'.$model->imgFile->extension;

                   $model->imgFile->saveAs(\Yii::getAlias('@webroot').$fileName,false);
                   //图片地址赋值
                   $model->logo=$fileName;
               }

                $date=date('Ymd');

                $good_day_count=GoodsDayCount::find()->where(['day'=>$date])->one();
                if($good_day_count){

                }else{
                    $good_day_count=new GoodsDayCount();
                    $good_day_count->day=$date;
                    $good_day_count->count=1;

                    $good_day_count->save();
                }
                $goods_intro=GoodsIntro::find()->where(['goods_id'=>$id])->one();

                $goods_intro->content=$model->content;

                $goods_intro->save(false);
              /*  $model->sn=date('Ymd')*100000+$good_day_count->count;*/
                $model->save(false);//要加false 因为这里save的时候默认还要验证一次



                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getErrors());exit;
            }
        }

        $categories=GoodsCategory::find()->asArray()->all();

        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }

    public function actionDelete($id){
        $model=Goods::find()->where(['id'=>$id])->one();
        $model->status=0;
        $model->save(false);
        \Yii::$app->session->setFlash('success','删除成功');
        $this->redirect(['goods/index']);
    }

   public function actionTest(){
     /*echo date('Ymd')*100000;*/
      /* $date=date('Ymd');
       $count=GoodsDayCount::find()->where(['day'=>$date])->one();
       if($count){
           var_dump(2);exit;
       }else{
           var_dump(1);exit;
       }*/
   }

    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],

            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/logo',
                'baseUrl' => '@web/upload/logo',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "/{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //图片上传成功的同时，将图片和商品关联起来
                    $model = new GoodsGallery();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->logo = $action->getWebUrl();
                    $model->save();
                    $action->output['fileUrl'] = $model->logo;
                    //$action->output['goods_id'] = $model->goods_id;

//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //$action->output['Path'] = $action->getSavePath();
                    /*
                     * 将图片上传到七牛云
                     */
                    /* $qiniu = \Yii::$app->qiniu;//实例化七牛云组件
                     $qiniu->uploadFile($action->getSavePath(),$action->getFilename());//将本地图片上传到七牛云
                     $url = $qiniu->getLink($action->getFilename());//获取图片在七牛云上的url地址
                     $action->output['fileUrl'] = $url;//将七牛云图片地址返回给前端js
                    */
                },
            ],
        ];
    }
    public function actionGallery($id){
      $goods=Goods::findOne(['id'=>$id]);
        if($goods==null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('gallery',['goods'=>$goods]);
    }
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

    }
}