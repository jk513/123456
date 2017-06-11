<a class="btn btn-info" href="<?= \yii\helpers\Url::to(['brand/add']) ?>">添加</a>
<table class="table">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>LOGO</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
      <tr>
          <td><?= $model->id ?></td>
          <td><?= $model->name ?></td>
          <td><img width="50" src="<?= $model->logo ?>"></td>
          <td><?= $model->intro ?></td>
           <td>
               <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$model->id],['class'=>'btn btn-xs btn-warning'])?>
              <?=\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$model->id],['class'=>'btn btn-xs btn-danger']) ?>
           </td>
      </tr>
    <?php endforeach;  ?>
</table>
<?php echo \yii\widgets\LinkPager::widget(['pagination'=>$page]) ?>
