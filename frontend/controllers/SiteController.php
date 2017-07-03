<?php
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Order;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use EasyWeChat\Foundation\Application;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>3,
                'maxLength'=>3,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'index';

        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }



    //商品详情页
    public function actionGoods($id)
    {
        //$this->layout = 'main';
        $goods = Goods::findOne(['id'=>$id]);
        if($goods==null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('goods',['goods'=>$goods]);
    }

    //添加到购物车
    public function actionAdd()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if($goods==null){
            throw new NotFoundHttpException('商品不存在');
        }
        if(Yii::$app->user->isGuest){
            //未登录
            //先获取cookie中的购物车数据
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                //cookie中没有购物车数据
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
                //$cart = [2=>10];
            }


            //将商品id和数量存到cookie   id=2 amount=10  id=1 amount=3
            $cookies = Yii::$app->response->cookies;
            /*$cart=[
                ['id'=>2,'amount'=>10],['id'=>1,'amount'=>3]
            ];*/
            //检查购物车中是否有该商品,有，数量累加
            if(key_exists($goods->id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
//            $cart = [$goods_id=>$amount];
            $cookie = new Cookie([
                'name'=>'cart','value'=>serialize($cart)
            ]);
            $cookies->add($cookie);



        }else{
            //已登录 操作数据库
        }
        return $this->redirect(['site/cart']);
    }

    //购物车
    public function actionCart()
    {
        if(Yii::$app->user->isGuest) {
            //取出cookie中的商品id和数量
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if ($cookie == null) {
                //cookie中没有购物车数据
                $cart = [];
            } else {
                $cart = unserialize($cookie->value);
            }
            $models = [];
            foreach ($cart as $good_id => $amount) {
                $goods = Goods::findOne(['id' => $good_id])->attributes;
                $goods['amount'] = $amount;
                $models[] = $goods;
            }
            //var_dump($models);exit;

        }else{
            //从数据库获取购物车数据
        }
        return $this->render('cart', ['models' => $models]);
    }

    public function actionUpdateCart()
    {
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if($goods==null){
            throw new NotFoundHttpException('商品不存在');
        }
        if(Yii::$app->user->isGuest){
            //未登录
            //先获取cookie中的购物车数据
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                //cookie中没有购物车数据
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
                //$cart = [2=>10];
            }


            //将商品id和数量存到cookie   id=2 amount=10  id=1 amount=3
            $cookies = Yii::$app->response->cookies;
            /*$cart=[
                ['id'=>2,'amount'=>10],['id'=>1,'amount'=>3]
            ];*/
            //检查购物车中是否有该商品,有，数量累加
            /*if(key_exists($goods->id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }*/
            if($amount){
                $cart[$goods_id] = $amount;
            }else{
                if(key_exists($goods['id'],$cart)) unset($cart[$goods_id]);
            }

//            $cart = [$goods_id=>$amount];
            $cookie = new Cookie([
                'name'=>'cart','value'=>serialize($cart)
            ]);
            $cookies->add($cookie);



        }else{
            //已登录  修改数据库里面的购物车数据
        }

    }


    //提交订单
    public function actionOrder()
    {
        $model = new Order();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            //继续获取其他属性的值
            $address_id = Yii::$app->request->post('address_id');
            $address = Address::findOne(['id'=>$address_id,'member_id'=>Yii::$app->user->id]);
            if($address == null){
                throw new NotFoundHttpException('地址不存在');

            }
            $model->province = $address->province;
            //.....
            //$model->delivery_id;//送货方式id
            $model->delivery_name = Order::$deliveries[$model->delivery_id]['name'];
            $model->delivery_price = Order::$deliveries[$model->delivery_id]['price'];
            //...

            //计算总价格
            //遍历购物车商品，循环累加
            //$model->total;

            //回滚--事务--innnodb存储引擎

            //开启事务
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model->save();
                //$model->id;//保存后就有id属性


                //订单商品详情表
                //根据购物车数据，把商品的详情查询出来，逐条保存
                $carts = Cart::findAll(['member_id'=>Yii::$app->user->id]);
                foreach($carts as $cart){
                    $goods = Goods::findOne(['id'=>$cart->goods_id,'status'=>1]);
                    if($goods==null){
                        //商品不存在
                        throw new Exception('xx商品已售完');
                    }
                    if($goods->stock < $cart->amount){
                        //库存不足
                        throw new Exception('xx商品库存不足');
                    }
                    $order_goods = new OrderGoods();
                    $order_goods->order_id= $model->id;
                    //....
                    $order_goods->total = $order_goods->price*$order_goods->amount;
                    $order_goods->save();
                    //扣库存 //扣减该商品库存
                    $goods->stock -= $cart->amount;
                    $goods->save();

                }
                //提交
                $transaction->commit();

                //如果选择的是微信支付，则跳转到微信支付页面
            }catch (Exception $e){
                //回滚
                $transaction->rollBack();
            }












        }


    }


    //清理超时未支付订单
    public function actionClean()
    {
        set_time_limit(0);//不限制脚本执行时间
        while (1){
            //超时未支付订单  待支付状态1超过1小时==》已取消0
            $models = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-3600])->all();
            foreach ($models as $model){
                //$model->status = 0;
                //$model->save();
                //返还库存
                /*foreach($model->goods as $goods){
                    Goods::updateAllCounters(['stock'=>$goods->amount],'id='.$goods->goods_id);
                }*/
                echo 'ID为'.$model->id.'的订单被取消了。。。';

            }
            //1秒钟执行一次
            sleep(1);
        }


    }


    //微信支付(已登录)
    public function actionPay($order_id)
    {
        //$order = Order::findOne(['id'=>$order_id,'status'=>1,'member_id'=>Yii::$app->user->id]);
        $model = Order::findOne(['id'=>$order_id]);
        //检查订单支付方式，支付状态
        if($model->payment_id != 1){
            //提示支付方式错误
        }
        //微信支付
        //2 调用统一下单api

        $options = Yii::$app->params['wechat'];
        $app = new Application($options);


        $attributes = [
            'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP... 扫码支付选择NATIVE
            'body'             => '京西商城订单支付',
            'detail'           => 'iPad mini 16G 白色，小米手机，索尼45寸液晶电视...',
            'out_trade_no'     => $model->trade_no,//订单交易号
            'total_fee'        => $model->total*100, // 单位：分  订单总价格
            'notify_url'       => 'http://www.yii2shop.com/site/notify.html', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            //'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new \EasyWeChat\Payment\Order($attributes);

        $payment = $app->payment;
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            //$prepayId = $result->prepay_id;
            $code_url = $result->code_url;

            //将交易链接制作成二维码
            // Create a basic QR code
            $qrCode = new QrCode($code_url);
            $qrCode->setSize(300);
            header('Content-Type: '.$qrCode->getContentType());
            echo $qrCode->writeString();
        }else{
            //请求支付失败
        }
        //var_dump($result);
    }

    //微信支付结果通知地址(关闭csrf验证)
    public function actionNotify(){
        $app = new Application(Yii::$app->params['wechat']);
        $response = $app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::findOne(['trade_no'=>$notify->out_trade_no]);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            /*if ($order->status != 1) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                //$order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 2;
            } else { // 用户支付失败
                //$order->status = 'paid_fail';
            }
            $order->save(); // 保存订单*/
            if($order->status == 1 && $successful){
                $order->status = 2;
                $order->save();
            }
            return true; // 返回处理完成
        });
        return $response;
    }



}
