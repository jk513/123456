<?php
namespace backend\controllers;
use backend\components\RbacFilter;
use backend\models\User;
use backend\models\LoginForm;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller{
    //使用过滤器  过滤器是一种特殊的行为 所以用行为调用
   /* public function behaviors(){
        return[
          'rabc'=>[
              'class'=>RbacFilter::className(),//调用rabc这个过滤器
          ]
        ];
    }*/



    public function actionAdd(){
        $model=new User();
       /* $request=new Request();
        if($request->isPost){
            $model->load($request->post()); 这三行可以用下面的一行代替
        }*/
        if($model->load(\Yii::$app->request->post())){
         if($model->validate()){
             $model->save();
             if ($model->addUserRole($model->id)) {
           \Yii::$app->session->setFlash('success','新增成功');
            return $this->redirect(['user/index']);}
         }else{
             var_dump($model->getErrors());
         }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionIndex(){
        $models=User::find()->all();

      return $this->render('index',['models'=>$models]);
    }
    public function actionUpdate($id){
      $model=User::find()->where(['id'=>$id])->one();
        $model->loadDate($id);
        $oldName = $model->name;
        if($model->load(\Yii::$app->request->post())){

            if($model->validate()){
                $model->save();
                if ($model->updateUserRole($model->id, $oldName)) {
                \Yii::$app->session->setFlash('success','修改成功');
                $this->redirect(['user/index']);}
            }else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);

    }
    public function actionDelete($id){
        $model=User::find()->where(['id'=>$id])->one();
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        $this->redirect(['user/index']);
    }
    //判断是否登录
    public function actionUser(){
        $user=\Yii::$app->user;//实例化user组件
        var_dump($user->isGuest);//判断是不是游客 就可以判断是否登录
    }
    //登录
    public function actionLogin(){
        $model=new LoginForm;
       if( $model->load(\Yii::$app->request->post())){
         if($model->validate()){
             $last_login_time=time();
             $last_login_ip= \Yii::$app->request->userIP;
             $user=User::find()->where(['username'=>$model->username])->one();
             $user->last_login_time=$last_login_time;
             $user->last_login_ip=$last_login_ip;
             $user->save(false);
             \Yii::$app->session->setFlash('success','登录成功');
             return $this->redirect(['user/index']);
         }else{
             var_dump($model->getErrors());exit;
         }
       };
        return $this->render('login',['model'=>$model]);
    }
    //注销登录
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        $this->redirect(['user/index']);
    }
}