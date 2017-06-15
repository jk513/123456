<?php
namespace backend\models;
use backend\models\User;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    //记住我
    public $rememberMe;
    public function rules(){
        return[
            [['username','password_hash'],'required'],
            //添加自定义验证方法
           ['username','validateUsername'],
            ['rememberMe','boolean']
        ];
    }
    public function attributeLabels(){
        return[
            'username'=>'用户名',
            'password_hash'=>'密码',
            'rememberMe'=>'记住我'
        ];
    }
    //自定义验证方法
  public function validateUsername(){
        $user=User::find()->where(['username'=>$this->username])->one();
        if($user){
            //用户存在 验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //账号秘密正确，登录
                if($this->rememberMe){//点击了自动登录
                    \Yii::$app->user->login($user,7*24*3600);//第二个参数表示保存在cookie中的时间
                }else{//不自动登录
                    \Yii::$app->user->login($user);
                }

               return true;
            }else{

                $this->addError('password_hash','密码不正确');

              /*  var_dump($user);exit;*/
            }
        }else{
            //账号不存在  添加错误
            $this->addError('username','账号不正确');
        }
      return false;
    }
}