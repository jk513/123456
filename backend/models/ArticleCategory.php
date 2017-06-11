<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
   public function rules(){
   return[
     [['name','status','sort','is_help','intro'],'required'],

   ];
   }
    public function attributeLabels(){
        return[
          'name'=>'名称',
          'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
            'is_help'=>'类型',
        ];
    }
}