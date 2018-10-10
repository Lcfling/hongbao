<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/14
 * Time: 17:00
 */

require_once (__DIR__.'/alipay-sdk-PHP-3.3.0/AopSdk.php');

class Alipay{

    //应用ID
    const APPID = '2018060760275873';

    //请填写开发者私钥去头去尾去回车，一行字符串
    const RSA_PRIVATE_KEY = 'MIIEowIBAAKCAQEAxPss7q5Xb82ZfkPGPd+jnJrkF2xj/toJXzgDiuowHmn/MdjWNblD8pwxlqAWcice/OCwTNMDyoCJP6IbwjvTeaDFxQLiJyQfwZBAWfNJDxhZJP7+WzfSx+qaDAbVtWLTZT/VArT4uoyAU+v8H0EuEvB1YY0URI9SiEEOGqee5Xu8oFM6SIu6/Vt95LORBcqhAbMPnSS6CgWbDu/fVnKuBvmWsBCo7dXBEhpqJ2QyogQtpFXdA1SA1Sg+wLtaoGMJv2C17tkUXcVnYtYfxFXLIqdz9oxaz4pCSMOa1Nol+aQcIJRcJrGuoNzzCyHGReZZnQ1DvWWnuRMQQFBZvntplQIDAQABAoIBACZJM6iIllISwhy6i5OHHkPYDzFGTgFA28V4G6gqcIPY0lMb9Dao9b34AeNm5jX1yJ4aBepIsVnwtbx6g66y9h2T1BzAvLgi21FB+mABW1flwZ3hOgr8xKk6vgpMYclJlhD94ScPibCokmC9Y1mnz4660fXN9/yDZuU4z8b6gXXUyWhzyG4pB7fxq/jjb3p8ho9lw0XpDIxyl0zoCFiJDxXlFTbIUZIQ0tDCXK8162WfxtKrUGgxJ3U+WENqH1IXv283cU2pIQcGpC1E4DbIR96erVUY0ycIU7jEL/wot1FMaggWgz0gajIN1hC0gS+cmvDqWilrxuqHSThcB+Hl1QECgYEA9UoLcuuv9fwuOUFzF1pd5s/vtxhjjCGXBZQwb9xQfvlBnSK7EYuty12PAzXbJxD3rxQd6BvNkpPW84kCuWDvQu4PPcTHWfQ99mNL6WXoebyeK+fXy9UmxZ5qvThspYnaZGIIzu8qZTyVJ0Mc0Abn8VRrGCVx7wKrQDQq0Ko0U/UCgYEAzZUe9qIBEpnZOQKbBst1tbv8cu7ctBaDKyyz/rg0wukjWk2s4xo9Z/Jrsx77v6Xi4kcFc1OeknuQSe+vP+6jsr25zerqKasDTBntOOhyFokTNeTcBlGpaRvb2FatBRQwjJr2PeuG+o99Nl3k2TkhKicBNYa1scCw+Noo05oC2yECgYEA6zPXCplJHvxzbl999rSmOf7Fg7IVMne7EpRoZbrCTR7BdeWpr0danRjXW2K3BBzXA8CsdLbERnsQsHF+dTen9WvEnZwk8/Kpv6qzTdh0NNdSbBNh74gyJ2iiPVLvi0RGb985RwQ6iNywwPcvl6InolcqYfr15xOOFoBvVar/hkkCgYBdKAJ03epAhIiDJeQbyxxWso1tg0FtNXpQmRwjA1OdMsm7RNFfw2cp8BbPfu2y5TePM4GLxctoMyep6Ttva/KvrvtADP/4y65d0K3HCMWR4qFa3Y66Kkzq/R28xWH3mDN7s0h7vHtYlKa3eQqXSCT4Fd6dY6J3nopS6YBwiixbIQKBgDLscuNUuoBHR/PZUdyJ3SJfkn0QVhggsQl+CUlHz+lxXH+IaJN2+J2s6EIUkjbSH6RUC+DrFFILX3oLPvEhtz6lx0AfMaiNEyC8hQRMOr9pA9eF4yj+XoHAFPoGrvUmsUnKi/hOnl//T0HTpLJTcc7lF556dfqcKK2CcDoX5NxS';

    //支付宝公钥
    const ALIPAY_RSA_PUBLIC_KEY = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxPss7q5Xb82ZfkPGPd+jnJrkF2xj/toJXzgDiuowHmn/MdjWNblD8pwxlqAWcice/OCwTNMDyoCJP6IbwjvTeaDFxQLiJyQfwZBAWfNJDxhZJP7+WzfSx+qaDAbVtWLTZT/VArT4uoyAU+v8H0EuEvB1YY0URI9SiEEOGqee5Xu8oFM6SIu6/Vt95LORBcqhAbMPnSS6CgWbDu/fVnKuBvmWsBCo7dXBEhpqJ2QyogQtpFXdA1SA1Sg+wLtaoGMJv2C17tkUXcVnYtYfxFXLIqdz9oxaz4pCSMOa1Nol+aQcIJRcJrGuoNzzCyHGReZZnQ1DvWWnuRMQQFBZvntplQIDAQAB';

    /**
     * 支付宝服务器主动通知商户服务器里指定的页面
     * @var string
     */
    //private $callback = "http://www.test.com/notify/alipay_notify.php";
    private $callback = "http://qp.webziti.com/mobile/upmember/alipaynotify";

    /**
     * 生成APP支付订单信息
     * @param $orderId订单ID
     * @param $subject标题
     * @param $body描述
     * @param $pre_price支付总金额
     * @param $expire交易时间
     * @return bool|string 返回支付宝签名后订单信息，否则返回false
     */
    public function unifiedorder($orderId, $subject,$body,$pre_price,$expire){
        try{
            $aop = new \AopClient();
            $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
            $aop->appId = self::APPID;
            $aop->rsaPrivateKey = self::RSA_PRIVATE_KEY;
            $aop->format = "json";
            $aop->charset = "UTF-8";
            $aop->signType = "RSA2";
            $aop->alipayrsaPublicKey = self::ALIPAY_RSA_PUBLIC_KEY;
            //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
            $request = new \AlipayTradeAppPayRequest();
            //SDK已经封装掉了公共参数，这里只需要传入业务参数
            $bizcontent = "{\"body\":\"{$body}\","      //支付商品描述
                . "\"subject\":\"{$subject}\","        //支付商品的标题
                . "\"out_trade_no\":\"{$orderId}\","   //商户网站唯一订单号
                . "\"timeout_express\":\"{$expire}m\","       //该笔订单允许的最晚付款时间，逾期将关闭交易
                . "\"total_amount\":\"{$pre_price}\"," //订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                . "}";

            /*$bizcontent = "{\"body\":\"ceshi\","
                . "\"subject\": \"Appceshi\","
                . "\"out_trade_no\": \"sadasd2012541401\","
                . "\"total_amount\": \"0.01\","
                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                . "}";*/
            $request->setNotifyUrl($this->callback);
            $request->setBizContent($bizcontent);
            //这里和普通的接口调用不同，使用的是sdkExecute
            $response = $aop->sdkExecute($request);
            //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
            return $response;//就是orderString 可以直接给客户端请求，无需再做处理。
        }catch (\Exception $e){
            return false;
        }
    }
}