<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 9:32
 */


/**
 * 组装xml格式数据
 * @param array $data
 * @return string
 */
function toXml($data=array()){
    if(!is_array($data) || count($data) <= 0)
    {
        return 'dataERROR';
    }
    $xml = "<xml>";
    foreach ($data as $key=>$val)
    {
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return trim($xml);
}

/**
 * 生成随机字符串
 * @return string
 */
function rand_code(){
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = str_shuffle($str);
    $str = substr($str,0,32);
    return  $str;
}

/**
 * 生成签名
 * @param $params
 * @param $key
 * @return string
 */
function getSign($params, $key){
    ksort($params); // 将参数数组按照键名对关联数组进行升序排序
    foreach ($params as $k => $item) {
        if (!empty($item)) {
            $newArr[] = $k.'='.$item;
        }
    }
    $stringA = implode("&", $newArr);
    $stringSignTemp = $stringA."&key=".$key; // 拼接key 在商户平台API安全里自己设置的
    $stringSignTemp = MD5($stringSignTemp); // 将字符串进行MD5加密
    $sign = strtoupper($stringSignTemp); // 将所有字符转换为大写
    return $sign;
}

/**
 * 将xml数据转换为数组
 * @param $xml
 * @return mixed|string
 */
function fromXml($xml){
    if(!$xml){
        return "xml数据异常！";
    }
    // 将XML转为array 禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $data;
}

/**
 * 生成预支付订单
 * @param $body商品标题
 * @param $totalFee商品金额(元)
 * @param $orderId商户订单号
 * @return mixed
 */
function wx_appPay($body, $totalFee, $orderId){
    global $pe;
    $nonce_str      = rand_code(); // 调用随机字符串生成方法获取随机字符串
    $data['appid']  ='wx57faf750ee231971'; // appid
    $data['mch_id'] = '1507509581' ; // 商户号
    $data['body']   = $body;
    //$data['spbill_create_ip'] = pe_ip(); // $_SERVER['HTTP_HOST'];
    $data['total_fee']    = $totalFee * 100; // 金额(分)
    $data['out_trade_no'] = $orderId; // 商户订单号
    $data['nonce_str']    = $nonce_str; // 随机字符串
    $data['notify_url']   = "http://qp.webziti.com/mobile/wechat_notify.php"; // 回调地址
    $data['trade_type']   = 'APP'; // 支付方式
    $key = 'wangkun291wangkun291wangkun291wa'; // 拼接key 在商户平台API安全里自己设置的
    $data['sign'] = getSign($data, $key); // 获取签名
    $xml = toXml($data); // 数组转xml
    // curl 传递给微信方
    $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    // header("Content-type:text/xml");
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    }    else    {
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2); // 严格校验
    }
    // 设置header
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    // 要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // 设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    // 传输文件
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    // 运行curl
    $data = curl_exec($ch);
    // 返回结果
    if($data){
        curl_close($ch);
        // 返回成功,将xml数据转换为数组.
        $re = fromXml($data);
        if($re['return_code'] != 'SUCCESS'){
            $return['code'] = 201;
            $return['msg']  = "signFaild！";
            $return['data'] = $re;
        }else{
            // 接收微信返回的数据,传给APP!
            $timestamp = time();
            $arr =array(
                'prepayid'  => $re['prepay_id'],
                'appid'     => "wx426b3015555a46be", // appid
                'partnerid' => (string)"1225312702", // 商户号
                'package'   => "Sign=WXPay",
                'noncestr'  => (string)$nonce_str,
                'timestamp' => (string)$timestamp
            );
            // 第二次生成签名
            $sign = getSign($arr, $key);
            $arr['sign'] = $sign;
            $return['code'] = 200;
            $return['msg']  = "success！";
            $return['data'] = $arr;
        }
    }else{
        $error = curl_errno($ch);
        curl_close($ch);
        $return['code'] = 201;
        $return['msg']  = "curl出错，错误码:$error.'<br>'";
    }
    return json_encode($return);
}