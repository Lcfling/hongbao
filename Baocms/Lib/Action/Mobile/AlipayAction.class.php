<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/14
 * Time: 17:26
 */

include 'Alipay.class.php';

class AlipayAction extends CommonAction{

    public function alipaytest(){
        $order_sn=$_POST['ordersn'];
        $handle=new alipay();
        echo $handle->unifiedorder($order_sn,'会员升级','会员升级',time()+86400,'1');
    }

    //创建订单
    public function createorder(){
        $order_sn=$this->ordersn();
        $uid=$_GET['uid'];
        $paymoney=$_GET['paymoney'];
        switch ($paymoney){
            case 598:
                break;
            case 198:
                break;
            default:
                $this->jsonout('faild','金额错误！');
                break;
        }
        if($uid<1){
            $this->jsonout('faild','用户不存在！');
        }
        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");
        $title=$paymoney;
        $tb_order = M(order);
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
        $handle=new alipay();
        $params['params']=$handle->unifiedorder($order_sn,'会员升级','会员升级',$paymoney,30);
        $params['ordersn']=$order_sn;
        //$params=$handle->unifiedorder($order_sn,'会员升级','会员升级',0.01,30);
        $this->jsonout('success','0',$params);
    }
    //自定义的订单号
    public function ordersn()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999), rand(0, 99));
        return $orderSn;
    }
}