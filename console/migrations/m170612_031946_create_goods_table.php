<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m170612_031946_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('商品名称'),
            'sn'=>$this->string(20)->comment('货号'),
            'logo'=>$this->string(255)->comment('logo图片'),
            'goods_category_id'=>$this->integer()->comment('商品分类'),
            'brand_id'=>$this->integer()->comment('品牌分类'),
            'market_price'=>$this->decimal(10,2)->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->comment('商品价格'),
            'stock'=>$this->integer()->comment('库存'),
            'is_on_sale'=>$this->integer(1)->comment('收否在售'),
            'status'=>$this->integer(1)->comment('状态'),
            'sort'=>$this->integer()->comment('排序'),
            'create_time'=>$this->integer()->comment('添加时间')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
