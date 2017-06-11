<?php
namespace backend\models;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Article extends ActiveRecord{
    public $content;
    public function rules(){
        return[
          [['name','article_category_id','sort','status','content'],'required'],
          ['intro','safe']//sort如果不required为啥不能保存

        ];
    }
    public function attributeLabels(){
        return[
            'name'=>'文章名',
            'sort'=>'排序',
            'article_category_id'=>'文章分类',
            'status'=>'状态',
            'intro'=>'简介',
            'content'=>'文章内容',
        ];
    }
    public function behaviors(){
        return[
            'time'=>[
                'class'=>TimestampBehavior::className(),
                'attributes'=>[
                    self::EVENT_BEFORE_INSERT=>['create_time'],

                ]
            ]
        ];
    }
    public static function getCategoryOptions()
    {
        return ArrayHelper::map(ArticleCategory::find()->where(['status'=>1])->asArray()->all(),'id','name');
    }
    public function getCategory(){

       return $this->hasOne(ArticleCategory::className(),['id'=>'article_category_id']);
    }
    public function getDetail(){

        return $this->hasOne(ArticleDetail::className(),['article_id'=>'id']);
    }
}