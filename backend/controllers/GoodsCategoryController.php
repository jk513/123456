<?php
namespace backend\controllers;
use backend\models\GoodsCategory;
use yii\web\Controller;

class GoodsCategoryController extends Controller{

    //添加商品分类
     public function actionAdd(){
         $model=new GoodsCategory();
         if($model->load(\Yii::$app->request->post()) && $model->validate()){
           //判断是否是添加一级分类
             if($model->parent_id){
                 $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);

                 $model->prependTo($parent);
             }else{
                 //添加一级分类
                 $model->makeRoot();
             }
             \Yii::$app->session->setFlash('success',['设置成功']);
             return $this->redirect(['goods-category/index']);
         }

         return $this->render('add',['model'=>$model]);
     }



    public function actionIndex(){

    }
    //测试
    public function actionTest(){
      //创建一级菜单
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