<a class="btn btn-info" href="<?= \yii\helpers\Url::to(['article-category/add']) ?>">添加</a>
<table class="table">
    <tr>
        <th>名称</th>
        <th>简介</th>
        <th>类型</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
    <tr>
        <td><?= $model->name  ?></td>
        <td><?= $model->intro  ?></td>
        <td><?= \backend\models\Brand::$help_options[$model->is_help] ?></td>
        <td>
            <?= \yii\bootstrap\Html::a('编辑',['article-category/edit','id'=>$model->id],['class'=>'btn btn-warning']) ?>
            <?= \yii\bootstrap\Html::a('删除',['article-category/delete','id'=>$model->id],['class'=>'btn btn-danger']) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php echo \yii\widgets\LinkPager::widget(['pagination'=>$page]) ?>