<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'password_sure')->passwordInput();
echo $form->field($model,'email');
echo $form->field($model,'name')->checkboxList(\backend\models\User::getRoleAction());
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();