<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'content')->textarea();
/*echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($article_category,'id','name'),['prompt'=>'-请选择分类-']);*/
/*以上的显示分类的方法可以封装在model里成为一个getCategoryOptions()静态方法调用如下*/
echo $form->field($model,'article_category_id')->dropDownList(\backend\models\Article::getCategoryOptions(),['prompt'=>'-请选择分类-']);
echo $form->field($model,'sort');
echo $form->field($model,'status')->radioList([-1=>'删除',1=>'正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);


\yii\bootstrap\ActiveForm::end();