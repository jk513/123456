<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;

use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller{
    public function actionAdd(){
      $model=new ArticleCategory();
       $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex(){
        $query=ArticleCategory::find()->where(['status'=>1]);
        $total=$query->count();//总共多少条
       $page=new Pagination([
           'totalCount'=>$total,
               'defaultPageSize'=>2,
       ]);
        $models=$query->offset($page->offset)->limit($page->limit)->all();

        return $this->render('index',['models'=>$models,'page'=>$page]);
    }
    public function actionEdit($id){
        $model=ArticleCategory::find()->where(['id'=>$id])->one();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
              /*  var_dump($model);
                exit;*/
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){

        $article=Article::find()->where(['article_category_id'=>$id])->one();
        if($article){


           \Yii::$app->session->setFlash('error','该分类下面有文章不能删除');
            return $this->redirect(['article-category/index']);

        }else{
            $model=ArticleCategory::find()->where(['id'=>$id])->one();
            $model->status=0;
            $model->save();
            \Yii::$app->session->setFlash('success','删除成功');
            $this->redirect(['article-category/index']);
        }

    }
}