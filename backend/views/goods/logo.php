

<?php
use yii\web\JsExpression;
use xj\uploadify\Uploadify;
$form=\yii\bootstrap\ActiveForm::begin();

echo \yii\bootstrap\Html::fileInput('test',null,['id'=>'test']);

echo \xj\uploadify\Uploadify::widget([
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

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
   <style>
body{
    margin: 0;padding: 0;
}
       ul li {
    float: left;
    margin-right: 40px;
           list-style: none;
       }
       img{
    width: 200px;
           height: 200px;
       }
   </style>
</head>

<body>
<h1>商品图片</h1>

<ul>
   <?php foreach($models as $model) : ?>

    <li><img  src="<?=$model->logo?>"> </li>
<?php endforeach;   ?>
</ul>