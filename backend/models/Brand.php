<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
   /* public $imgFile;*///数据表中没有这个属性字段，所以要自己定义一个，使用了插件就不需要这个了
    static public $help_options=[0=>'不是帮助文档',1=>'是帮助文档'];

    public function rules(){
        return[
            [['name','intro','sort', 'status'], 'required'],
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
         /*   ['imgFile','file','extensions'=>['jpg','png','gif']],*/
        ];
    }

    public function attributeLabels(){
        return[
          'name'=>'品牌名称',
            'intro'=>'简介',
            'imgFile'=>'LOGO',
            'sort'=>'排序',
            'status'>'状态',
        ];
    }
}