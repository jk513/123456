<a class="btn btn-info" href="<?= \yii\helpers\Url::to(['article/add']) ?>">添加</a>
<table class="table">
    <tr>
        <th>文章名称</th>
        <th>简介</th>
        <th>所属分类</th>
        <th>文章详情</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
    <tr>
        <td><?= $model->name  ?></td>
        <td><?= $model->intro  ?></td>
        <td><?= $model->category->name ?></td>
        <td><?= $model->detail->content ?></td>
        <td>
            <?= \yii\bootstrap\Html::a('编辑',['article/edit','id'=>$model->id],['class'=>'btn btn-warning']) ?>
            <?= \yii\bootstrap\Html::a('删除',['article/delete','id'=>$model->id],['class'=>'btn btn-danger']) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php /*echo \yii\widgets\LinkPager::widget(['pagination'=>$page]) */?>