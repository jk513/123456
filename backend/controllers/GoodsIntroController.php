<?php
namespace backend\controllers;
use backend\models\GoodsIntro;
use yii\web\Controller;

class GoodsIntroController extends Controller{
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];

    }

   public function actionTest(){
     $model=new GoodsIntro();
       return $this->render('test');
   }

}