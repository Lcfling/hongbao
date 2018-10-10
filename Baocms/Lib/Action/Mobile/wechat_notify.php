<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 9:08
 *
 * @author Lee_zhj
 * 微信异步通知页面
 */


require_once('wechat_app_pay.php');
$key = 'e10adc3949ba59abbe56e057f20f883e'; // 拼接key 在商户平台API安全里自己设置的

$xmlData = file_get_contents('php://input'); // 接收微信返回的数据数据,返回的xml格式
$data = fromXml($xmlData); // 将xml格式转换为数组
// 日志记录
$file = fopen('./log.txt', 'a+');
fwrite($file,var_export($data,true));
fclose($file);
//为了防止假数据，验证签名是否和返回的一样。
$sign = $data['sign'];
unset($data['sign']);
if($sign == getSign($data, $key)){
    //签名验证成功
    if ($data['result_code'] == 'SUCCESS') {
        //根据返回的订单号做业务逻辑
        var_dump($data['result_code']);

        // code...
        // code...
        // code...

        // 处理后返回给微信的状态
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        exit();
    }else{
        //支付失败，输出错误信息
        $file = fopen('./log.txt', 'a+');
        fwrite($file,"ERRORinfo：".$data['return_msg'].date("Y-m-d H:i:s"),time()."\r\n");
        fclose($file);
    }
}else{
    $file = fopen('./log.txt', 'a+');
    fwrite($file,"ERRORinfo:SIGNFAILD".date("Y-m-d H:i:s"),time()."\r\n");
    fclose($file);
}