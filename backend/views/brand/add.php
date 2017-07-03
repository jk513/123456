<?php
use yii\web\JsExpression;
use xj\uploadify\Uploadify;
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'logo')->hiddenInput(['id'=>'brand-logo']);
/*echo $form->field($model,'imgFile')->fileInput();*/
/*echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);*/
echo \yii\bootstrap\Html::fileInput('test',null,['id'=>'test']);

echo Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'width' => 120,
        'height' => 40,
        'onUploadError' => new \yii\web\JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadSuccess' => new \yii\web\JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将上传成功后的图片地址(data.fileUrl)写入img标签
        $("#img_logo").attr("src",data.fileUrl).show();
        //将上传成功后的图片地址(data.fileUrl)写入logo字段
        $("#brand-logo").val(data.fileUrl);
    }
}
EOF
        ),
    ]
]);
if($model->logo){//如果是修改本来就有图片就显示图片
    echo \yii\helpers\Html::img($model->logo);
}else{//如果本来没有图片刚添加了图片就显示一个img,id为img_logo标签，上面通过jquery写入图片路径
    echo \yii\helpers\Html::img('',['style'=>'display:none','id'=>'img_logo','height'=>'50']);
}

echo $form->field($model,'sort');
echo $form->field($model,'status')->radioList([0=>'正常',1=>'隐藏']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();