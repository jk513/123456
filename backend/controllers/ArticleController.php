<?php
namespace backend\controllers;
use backend\models\Article;

use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
    public function actionAdd(){
        /*$article_category=ArticleCategory::find()->all();*///文章分类
      /* 以上查找分类的方法封装在了Model里 这里就可以不用查找了*/
      $model=new Article();
      $request=new Request();

        if($request->isPost){

       $model->load($request->post())  ;

            if($model->validate()){

                $model->save();
                $article=Article::find()->where(['name'=>$model->name])->one();//假设名字唯一 最好有编号是唯一键

             $article_detail=new ArticleDetail();
             $article_detail->article_id=$article->id;

                $article_detail->content=$model->content;
               /* var_dump($article_detail->content);exit;*/

                $article_detail->save();

                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex(){
       $models=Article::find()->where(['status'=>1])->all();
        return $this->render('index',['models'=>$models]);
    }
    public function actionEdit($id){
        /*$article_category=ArticleCategory::find()->all();*///文章分类
        $model=Article::find()->where(['id'=>$id])->one();
        $content=ArticleDetail::find()->where(['article_id'=>$id])->one();
        $model->content=$content->content;
        $request=new Request();
        if($request->isPost){
            $model->load($request->post())  ;
            if($model->validate()){
                $model->save();
                $article_detail=ArticleDetail::find()->where(['article_id'=>$id])->one();
                $article_detail->content=$model->content;
                /* var_dump($article_detail->content);exit;*/
                $article_detail->save();

                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model,'content'=>$content]);
    }

    public function actionDelete($id){
        $model=Article::find()->where(['id'=>$id])->one();
       /* $article_detail=ArticleDetail::find()->where(['article_id'=>$id])->one();
        $model->status=-1;*/
        $model->status=-1;
         $model->save(false);
      if($model->save(false)){
          \Yii::$app->session->setFlash('success','删除成功');
          $this->redirect(['article/index']);
      }else{
          var_dump($model->getErrors());

      }

    }

}