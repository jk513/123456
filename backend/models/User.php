<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public $password_sure;
    public $name = []; // 定义角色身份
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password_hash', 'email', ], 'required'],
            [['status', 'created_at', 'updated_at', 'last_login_time'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'last_login_ip'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            ['email', 'email'],
            [['password_reset_token'], 'unique'],
            ['password_sure','required'],
            ['password_sure','validatePassword'],
            ['name','safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'password_sure'=>'确认密码'
        ];
    }

    public function validatePassword(){
     if($this->password_sure!=$this->password_hash) {
         $this->addError('password_sure','两次输入的密码不一致');
         return false;
     }
    }
  /* 以下两种方法得到创建时间和更新时间*/
    /*public function behaviors(){
        return[
            'time'=>[
                'class'=>TimestampBehavior::className(),
                'attributes'=>[
                    self::EVENT_BEFORE_INSERT=>['created_at'],
                    self::EVENT_BEFORE_UPDATE=>['updated_at'],

                ]
            ]
        ];

    }*/
    public function beforeSave($insert){//重写beforeSave()
        /*$insert=$this->getIsNewRecord(); $insert是系统设置的，它的存在用来判断是新增还是修改*/
        if($insert){//如果$insert存在表示是新增
            $this->created_at=time();//创建时间等于当前时间
            $this->password_hash=Yii::$app->security->generatePasswordHash($this->password_hash);//把得到的密码加盐加密
            //生成随机字符串
            $this->auth_key=Yii::$app->security->generateRandomString();


        }else{//如果$insert不存在表示是修改更新，
            $oldPassword=$this->getOldAttribute('password_hash');//getOldAttribute可以获取旧属性
            if($this->password_hash !=$oldPassword){//如果密码有改变，就要把新密码再加加盐加密以下
                $this->password_hash=Yii::$app->security->generatePasswordHash($this->password_hash);
            }
            $this->updated_at=time();//更新时间等于当前时间
        }
        return parent::beforeSave($insert);
    }

    //获得角色
   /* public function getRole(){
        return $this->hasOne(UserForm::className(),['user_id','id']);
    }*/

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return self::findOne(['id'=>$id]);//通过id获取账号
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        // 获取当前账号的Id
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;

    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this-> getAuthKey()==$authKey;

    }
    //处理角色权限 多选框
    public static function getRoleAction()
    {
        $authManager = Yii::$app->authManager;
        return ArrayHelper::map($authManager->getRoles(), 'name', 'name');
    }

    //处理角色回显的操作


    public function loadDate($id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($id);
       /* var_dump($roles);
        exit;*/
        if ($roles != null) {
            //遍历数组 得到的是每一个身份角色
            foreach ($roles as $key => $role) {
                //将当前的角色回显到模板中
                $this->name[$key] = $key;
            }
        }
    }

    //查询当前的管理员包含的角色  传入当前user的id
    public static function getNowUserRole($id)
    {
        $authManager = Yii::$app->authManager->getRolesByUser($id);
        $roles = [];
        foreach ($authManager as $roleObj) {
            if ($roleObj->name != null) {
                $roles[] = $roleObj->name;
            };
        }
        return $roles;
    }

    //添加管理员角色
    public function addUserRole($id)
    {
        if ($this->name != null) {
            $authManager = Yii::$app->authManager;
            foreach ($this->name as $userRole) {
                $role = $authManager->getRole($userRole);
                if ($authManager->assign($role, $id)) {
                };
            }
            return true;
        } else { // 没有选择管理角色时 直接跳过
            return true;
        }
    }


    //  修改管理员角色
    public function updateUserRole($id, $oldName)
    {
        if ($this != null && $this->name != $oldName) {
            //清除有关当前用户角色
            $authManager = Yii::$app->authManager;
            $authManager->revokeAll($id);
            foreach ($this->name as $userRole) {
                $role = $authManager->getRole($userRole);
                if ($authManager->assign($role, $id)) {

                };
            }
            return true;
        } else { // 没有选择管理角色时 直接跳过
            return true;
        }
    }

    //删除用户关联角色
    public function deleteUserRole($id)
    {
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRolesByUser($id);
        if ($role != null) {
            $authManager->revokeAll($id);
        }
        return true;
    }


}

