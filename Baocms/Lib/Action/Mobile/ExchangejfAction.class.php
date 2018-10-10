<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/4
 * Time: 15:34
 *
 * @author Lee_zhj
 */

class ExchangejfAction extends CommonAction{


    public function curl_request(){

        $method = "POST";
        $url = 'https://jdev.bhsgd.net/api/v1/tokens';
        $request_body = '{"data":{"phone":15900000000}}';
        $auth = $this->CreatAuthorization($method, $url,  $request_body);
        $arr_header[] = "Content-Type:application/json";
        $arr_header[] = "Authentication:" . $auth;
        $data['para'] = "1111";
        $res = $this->http_request_xml($url, $request_body, $arr_header);
        $d=json_decode($res);
        $token=$d->data->token;
        $GetUrl='https://jdev.bhsgd.net/user/login/redirect?token='.$token;
        //echo json_encode($GetUrl);
        //echo $GetUrl;

       $sss= $this->http_request_xml($GetUrl);
       $sss=json_decode($sss);
       print_r($sss);



    }



    public function order_to_push(){

        $xmlData = file_get_contents('php://input');
        $theone = json_decode($xmlData,true);

        $wewewe = $theone['order_id'];

        $tb_integral = M('integral');
        $ifhave=$tb_integral->where("order_id='".$wewewe."'")->find();

        // 日志记录
        $file = fopen('./integral.txt', 'a+');
        fwrite($file, var_export($theone, true));
        fclose($file);


        //判断
        if(!$ifhave){
            //没有该订单，则创建订单->存入数据库
            //$tb_integral = M('integral');


            $integral['order_id'] =$wewewe;
            $integral['user_id'] = $theone['user_id'];
            $integral['root_id'] = $theone['root_id'];
            $integral['root_name'] = $theone['root_name'];
            $integral['product_id'] = $theone['product_id'];
            $integral['product_name'] = $theone['product_name'];
            $integral['create_time'] = time();
            /*$integral['status'] = 0;
            $integral['price'] = $theone['price'];
            $integral['profit'] = $theone['profit'];*/
            $integral['phone'] = $theone['phone'];

            $tb_integral->add($integral);

        }else{
            //如果该订单存在，则更新对应的值
            $tb_integral = M('integral');

            $integral['integral'] = $theone['integral'];
            $integral['result_time'] = time();
            $integral['price'] = $theone['price'];
            $integral['profit'] = $theone['profit'];
            $integral['status'] = $theone['status'];

            $tb_integral->where("order_id='".$wewewe."'")->save($integral);
        }


    }


    /**
     * @param $url
     * @param null $data
     * @param null $arr_header
     * @return mixed
     * ------------------------curl
     */
    public function http_request_xml($url,$data = null,$arr_header = null){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        if(!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($arr_header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $arr_header);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        //echo curl_getinfo($curl);
        curl_close($curl);
        unset($curl);
        //echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.json_encode(json_decode($output),JSON_UNESCAPED_UNICODE);
        return $output;

    }


    /**
     * @param $method
     * @param $url
     * @param $request_body
     * @return string
     * -------------认证
     */
    private function CreatAuthorization($method,$url,$request_body){

        $app_secret = 'ce7sW3XAgYYzjzx9iQkX7R9zi00leVL2';
        $time_stamp = time();
        $nonce = $this->RandomStrAndNum(16);
        //sha1([method]+[URL]+[time_stamp]+[nonce]+[app_secret]+[request_body])方式组成digest
        $digest = sha1($method . "+" . $url . "+" . $time_stamp . "+" . $nonce . "+" . $app_secret . "+" . $request_body);
        //hmac空格 1531104359:A2QPhH1otYWMIWWW:f819300fe013fb9dc49934270259b738ea4a4d9b
        $auth = "hmac " . $time_stamp . ":" . $nonce . ":" . $digest;
        return $auth;
    }


    /**
     * @param $length
     * @return string
     * 生成php随机数
     */
    private function RandomStrAndNum($length){
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};
        }
        return $key;
    }



}