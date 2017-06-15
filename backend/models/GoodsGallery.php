<?php
namespace backend\models;
use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{//因为要操作logo这张新表所以要建一个新的model
    public function rules()
    {
        return [
            [['goods_id'], 'integer'],
            [['logo'], 'required'],
            [['logo'], 'string', 'max' => 255],
        ];
    }
    public static function tableName()
    {
        return 'logo';
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品id',
            'logo' => '图片地址',
        ];
    }
}