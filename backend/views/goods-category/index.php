<a class="btn btn-info" href="<?= \yii\helpers\Url::to(['goods-category/add']) ?>">添加</a>
<table class="table table-bordered">
    <tr>
        <th>id</th>
        <th>分类名称</th>
        <th>所属父类</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model): ?>
        <tr data-lft="<?= $model->lft?>" >
            <td><?= $model->id  ?></td>
            <td><?=str_repeat(' - ',$model->depth).$model->name  ?></td>

            <!--因为一级分类就没有父类，所以要三元运算判断有没有父类先-->
            <td><?= $model->parent_id?$model->parent->name:'' ?>
                <span class="glyphicon glyphicon-chevron-down" style="float: right"></span>
            </td>

            <td>
                <?= \yii\bootstrap\Html::a('编辑',['goods-category/edit','id'=>$model->id],['class'=>'btn btn-warning']) ?>

            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php /*echo \yii\widgets\LinkPager::widget(['pagination'=>$page]) */?>