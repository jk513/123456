<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Logo extends ActiveRecord{
    public function rules(){
        return[
          [['goods_id','logo'],'required'],
        ];
    }

    public function attributeLabels(){
        return[
            'goods_id'=>'商品id',
            'logo'=>'商品图片',
        ];
    }
}