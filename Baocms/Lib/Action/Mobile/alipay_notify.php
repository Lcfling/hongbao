<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/14
 * Time: 17:09
 *
 *
 * @author Lee_zhj
 * 支付宝支付成功异步通知
 */

include_once (__DIR__.'/alipay-sdk-PHP-3.3.0/AopSdk.php');
var_dump('notify');

//验证签名
$aop = new \AopClient();
$aop->alipayrsaPublicKey = Alipay::ALIPAY_RSA_PUBLIC_KEY;
$flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");

//验签
if($flag){
    //处理业务，并从$_POST中提取需要的参数内容
    if($_POST['trade_status'] == 'TRADE_SUCCESS'
        || $_POST['trade_status'] == 'TRADE_FINISHED'){//处理交易完成或者支付成功的通知
        //获取订单号
        $orderId = $_POST['out_trade_no'];
        //var_dump($orderId);

        //交易号
        $trade_no = $_POST['trade_no'];
        //订单支付时间
        $gmt_payment = $_POST['gmt_payment'];
        //转换为时间戳
        $gtime = strtotime($gmt_payment);

        //此处编写回调处理逻辑

        //处理成功一定要返回 success 这7个字符组成的字符串，
        //die('success');//响应success表示业务处理成功，告知支付宝无需再异步通知

    }
}




