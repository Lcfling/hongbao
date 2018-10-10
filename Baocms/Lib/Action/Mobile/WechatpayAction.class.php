<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 14:19
 */

include 'wechatAppPay.class.php';

//todo 微信APP支付获取预支付id      Lee_zhj
class WechatpayAction extends CommonAction{
    /*function __construct()
    {

    }*/

    /**
     * 统一下单方法
     */
    public function index(){
        

        $order_sn=$_POST['ordersn'];
        //$order_sn='A7171426592804347592';
        $o=D('Order');
        $map=array('outTradeNo'=>$order_sn);
        $order_data=$o->where($map)->find();
        /*print_r($order_data);
        die();*/
        $appid = "wx57faf750ee231971";
        $mch_id = "1507509581";
        $notify_url = "http://qp.webziti.com/mobile/upmember/wxnotify";

        $key = "wangkun291wangkun291wangkun291wa";
        $wechatAppPay = new wechatAppPay($appid, $mch_id, $notify_url, $key);
        $params['body'] = '升级会员';                       //商品描述
        $params['out_trade_no'] = $order_sn;         //自定义的订单号
        $params['total_fee'] = $order_data['payMoney']*100;
        //$params['total_fee'] = $order_data['payMoney'];//订单金额 只能为整数 单位为分
        $params['trade_type'] = 'APP';    //交易类型 JSAPI | NATIVE | APP | WAP
        $result = $wechatAppPay->unifiedOrder($params);
        //var_dump($params);
        //print_r($result); // result中就是返回的各种信息信息，成功的情况下也包含很重要的prepay_id
        //die();

        //创建APP端预支付参数
        $data = @$wechatAppPay->getAppPayParams($result['prepay_id']);
        $file = fopen('./log2.txt', 'a+');
        fwrite($file,'oder='.$order_sn);
        fwrite($file,var_export($order_sn,true));
        fwrite($file,var_export($result,true));
        fwrite($file,var_export($data,true));
        fclose($file);
        //var_dump($data);
        $out['status']=2;
        $out['json']=$data;
        die(json_encode($out));

    }
    public function createorder(){
        $order_sn=$this->ordersn();
        $uid=$_GET['uid'];
        $paymoney=$_GET['paymoney'];
        //$paymoney=198;
        if($uid<1){
            $this->jsonout('faild','用户不存在！');
        }
        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");
        $title=$paymoney;
        $tb_order = M('order');
        $order['prepayId'] = 0;
        $order['uid'] = $uid;
        $order['title'] = $title;
        $order['prepayTime'] = $time;
        $order['outTradeNo'] = $order_sn;
        $order['orderState'] =0;
        $order['payType'] =0;
        $order['delstate'] =0;
        $order['payTime'] =0;
        $order['payMoney'] =$paymoney;
        $tb_order->add($order);
        $this->jsonout('success','0',$order);
    }
    //自定义的订单号
    public function ordersn()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999), rand(0, 99));
        return $orderSn;
    }
}