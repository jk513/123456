<table class="table table-bordered table-hover">
    <tr>
        <th>用户名</th>
        <th>密码</th>
        <th>email</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):  ?>
     <tr>
         <td><?=$model->username ?></td>
         <td><?= $model->password_hash?></td>
         <td><?= $model->email?></td>
         <td>
             <?= yii\bootstrap\Html::a('修改',['user/update','id'=>$model->id],['class'=>'btn btn-warning'])  ?>
             <?= yii\bootstrap\Html::a('删除',['user/delete','id'=>$model->id],['class'=>'btn btn-danger'])  ?>
         </td>
     </tr>
    <?php endforeach;   ?>
</table>