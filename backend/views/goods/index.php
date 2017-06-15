<!--第一部分为搜索框，通过get传输搜索的条件到goods/index中查询-->
<?php
$form=\yii\bootstrap\ActiveForm::begin([
    'method'=>'get',/*这里可以设置参数 平时没设置是因为默认的post，get要设置action,*/
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'options'=>['class'=>'form-inline'],/*options里面可以设置样式，这里是设置表单显示在一行*/
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名称'])->label(false);//label(false)不显示标签名就是不显示attributesLabel
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('-');
echo \yii\bootstrap\Html::submitButton('搜索');
\yii\bootstrap\ActiveForm::end();

?>
<!--第二部分 显示index页面的数据
-->

<?= \yii\bootstrap\Html::a('新增',['goods/add'],['class'=>'btn btn-info'])?>

<table class="table">
    <tr>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO图片</th>
        <th>所属分类</th>
        <th>所属品牌</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
  <?php foreach($models as $model): ?>
    <tr>
        <td><?= $model->name ?></td>
        <td><?= $model->sn ?></td>
        <td><img width="40" src="<?= $model->logo?>"></td>
        <td><?= $model->category->name ?></td>
        <td><?= $model->brands->name ?></td>
        <td><?= $model->market_price ?></td>
        <td><?= $model->shop_price ?></td>
        <td><?= $model->stock ?></td>
        <td><?= $model->is_on_sale==1?'在售':'下架' ?></td>
        <td><?= $model->status==1?'正常':'回收站' ?></td>
        <td><?= date('Y-m-d',$model->create_time) ?></td>
        <td>
            <?= \yii\bootstrap\Html::a('编辑',['goods/edit','id'=>$model->id],['class'=>'btn btn-warning']) ?>
            <?= \yii\bootstrap\Html::a('删除',['goods/delete','id'=>$model->id],['class'=>'btn btn-danger']) ?>
            <?= \yii\bootstrap\Html::a('相册',['goods/gallery','id'=>$model->id],['class'=>'btn btn-info']) ?>
        </td>
    </tr>


    <?php endforeach; ?>
</table>
<!--分页条-->
<?=\yii\widgets\LinkPager::widget([
    'pagination'=>$pager
])?>