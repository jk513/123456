<?php
namespace backend\models;
use yii\base\Model;
use yii\db\ActiveQuery;

class GoodsSearchForm extends Model{//建这个模型的作用就是生成一个可以搜索的表单，所以下面要写出生成这个表单眼用到的字段
    public $name;
    public $sn;
    public $minPrice;
    public $maxPrice;

    public function rules()
    {
        return [
            ['name','string','max'=>50],
            ['sn','string'],
            ['minPrice','double'],
            ['maxPrice','double'],

        ];


    }
    public function search(ActiveQuery $query){/*要操作query语句必须有ActiveQuery才行，第二个参数是传过来的语句*/
        //加载表单提交是数据 也就是搜索条件
        $this->load(\Yii::$app->request->get());
        if($this->name){//如果搜索条件里面有根据名字搜索这个字段
            $query->andWhere(['like','name',$this->name]);//多个条件搜索用andwhere,模糊搜索用like
            //这里不是查询而是拼接query语句也就是
            //原来的$query=Goods::find()变成了$query=Goods::find()->andWhere(['like','name',$this->name])
        }
        if($this->sn){
            $query->andWhere(['like','sn',$this->sn]);
        }
        if($this->maxPrice){
            $query->andWhere(['<=','shop_price',$this->maxPrice]);
        }
        if($this->minPrice){
            $query->andWhere(['>=','shop_price',$this->minPrice]);
        }
    }

}