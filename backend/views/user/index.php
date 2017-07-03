<table class="table table-bordered table-hover">
    <tr>
        <th>用户名</th>
        <th>密码</th>
        <th>email</th>
    <th>角色</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):  ?>
     <tr>
         <td><?=$model->username ?></td>
         <td><?= $model->password_hash?></td>
         <td><?= $model->email?></td>
         <td><?=\backend\models\User::getNowUserRole($model->id)==null?'NO':implode(" ",\backend\models\User::getNowUserRole($model->id)); ?></td>
       <!--  <td><?/*= $model->role->item_name  */?></td>-->

         <td>


         </td>
     </tr>
    <?php endforeach;   ?>
</table>