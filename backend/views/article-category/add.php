<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'sort');
echo $form->field($model,'status')->radioList([0=>'删除',1=>'正常']);
echo $form->field($model,'is_help')->radioList([0=>'不是帮助文档',1=>'是帮助文档']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);


\yii\bootstrap\ActiveForm::end();