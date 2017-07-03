<?php

namespace backend\models;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     *
     */
    //场景验证
    const SCENARIO_ADD='add';
    const SCENARIO_EDIT='edit';
    public $imgFile;
    public $content;
    public static function tableName()
    {
        return 'goods';
    }
   /* public function scenarios(){
        $scenarios=parent::scenarios();
        $scenarios[self::SCENARIO_ADD]=['imgFile'];
        $scenarios[self::SCENARIO_EDIT]=['imgFile'];
        return $scenarios;
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time',], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
             ['content','required'],
            ['imgFile','file','extensions'=>['jpg','png','gif'],'skipOnEmpty'=>false,'on'=>self::SCENARIO_ADD],
            ['imgFile','file','extensions'=>['jpg','png','gif'],'skipOnEmpty'=>true,'on'=>self::SCENARIO_EDIT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'logo图片',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '收否在售',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
        ];
    }
    //静态附加行为
    public function behaviors(){
        return [
            'time'=>[
                'class'=>TimestampBehavior::className(),
                'attributes'=>[

                    self::EVENT_BEFORE_INSERT => ['create_time'],
                ]
            ]
        ];
    }
    public static function getBrand(){//这个是添加的时候调用的
        return ArrayHelper::map(Brand::find()->asArray()->all(),'id','name');
    }
    public function getCategory(){//index展示页面调用
      return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }
    public function getBrands(){//这个是展示的时候调用的
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
  /*  public function scenarios(){
        $senarios=parent::scenarios();//先保存父类中的场景
        $senarios[self::SCENARIO_ADD]=['imgFile'];
        $senarios[self::SCENARIO_EDIT]=['imgFile'];
    }*/
    /*
   * 商品和相册关系 1对多
   */
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }

}
