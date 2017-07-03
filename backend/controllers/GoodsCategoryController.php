<?php
namespace backend\controllers;
use backend\models\GoodsCategory;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GoodsCategoryController extends Controller{

    //添加商品分类
     public function actionAdd(){
         $model=new GoodsCategory();
         if($model->load(\Yii::$app->request->post()) && $model->validate()){
           //判断是否是添加一级分类
             if($model->parent_id){//父id存在表示不是一级分类，就只用找到它的父id，然后prependTo
                 $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);
                 $model->prependTo($parent);
             }else{//没有父id表示添加的是一级分类，添加一级分类用makeRoot()
                 $model->makeRoot();
             }
             \Yii::$app->session->setFlash('success',['设置成功']);
             return $this->redirect(['goods-category/index']);
         }
         $categories=GoodsCategory::find()->asArray()->all();//这里是为了使用ztree插件
         return $this->render('add',['model'=>$model,'categories'=>$categories]);
     }
    //修改
    public function actionEdit($id)
    {
        $model = GoodsCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //判断是否是添加一级分类（parent_id是否为0）
            if($model->parent_id){
                //添加非一级分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);//获取上一级分类
                $model->prependTo($parent);//添加到上一级分类下面
            }else{
                //先判断本来是不是顶级分类
                if($model->getOldAttribute('parent_id')==0){
                    $model->save();
                }else{
                    //添加一级分类
                    $model->makeRoot();
                }


            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-category/index']);
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());


        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }


    public function actionIndex(){
        $models=GoodsCategory::find()->orderBy('tree,lft')->all();  /*先按树排序，再按树里面的左值排序，默认是升序*/

        return $this->render('index',['models'=>$models]);

    }
    //测试
    public function actionTest(){
      //创建一级菜单 测试的时候分类名称这些数据是自己写的 实际添加的时候是表单提交load得到的
      /*  $jydq = new GoodsCategory(['name' => '家用电器','parent_id'=>0]);
        $jydq->makeRoot();//将当前分类设置为一级分类*/
       /* var_dump($jydq);
        exit;*/
        //创建二级分类
      /*  $parent=GoodsCategory::findOne(['id'=>5]);
       $xjd = new GoodsCategory(['name' => '小家电','parent_id'=>$parent->id]);
        $xjd->prependTo($parent);*/
    }
    public function actionZtree(){//测试ztree视图功能

        $categories=GoodsCategory::find()->asArray()->all();

        return $this->renderPartial('ztree',['categories'=>$categories]);


    }
}