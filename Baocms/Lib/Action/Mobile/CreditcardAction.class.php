<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 14:10
 */



class CreditcardAction extends CommonAction{


    //todo 信用卡      Lee_zhj
    public function creditcard(){


        //获取用户信息
        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];


        //调用登录验证
        $this->login_verify($user_id,$randpwd);


        $username = $_GET['name'];
        $idcard = $_GET['sfz'];
        $phonenum = $_GET['phonenum'];
        $fromxyktongdao = $_GET['title'];
        $yzm = $_GET['yzm'];

//        echo "user_id ==".$user_id;
//        echo " name ==".$username;
//        echo " idcard ==".$idcard;
//        echo " phonenum ==".$phonenum;
//        echo " title ==".$fromxyktongdao;
//
//        die();



        $tb_dkinfo = M('dkinfo');
        $dkinfo['tel'] = $phonenum;
        $dkinfo['yzm'] = $yzm;

        $wer = $tb_dkinfo->where($dkinfo)->find();


        if (!$wer){
            $this->jsonout('faild','验证码不对！');
        }


        if($user_id=="" || $username=="" || $idcard=="" || $phonenum=="" || $fromxyktongdao==""){
            $this->jsonout('faild1111','非法操作！');
        }


        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");



       // if($xykinfo){
            $Uid = "1012256710569492481";
//拼接url
            $urll = "https://api.dsyhj.cn/apis/api/merchant/credit/card/save/" . $Uid;

            $res_get = $this->get_info("https://api.dsyhj.cn/apis/api/merchant/credit/card/bank/list");

//返回的json转数组
            $res_get = (json_decode($res_get));

//给银行通道ID赋值
            if ($fromxyktongdao == "民生银行") {
                $msg="民生银行";
                $oemChannelId = $res_get -> result[0] -> oemChannelId;
            } else if ($fromxyktongdao == "交通银行") {
                $msg="交通银行";
                $oemChannelId = $res_get -> result[1] -> oemChannelId;
            } else if ($fromxyktongdao == "浦发银行") {
                $msg="浦发银行";
                $oemChannelId = $res_get -> result[2] -> oemChannelId;
            } else if ($fromxyktongdao == "xingye") {
                $oemChannelId = $res_get -> result[3] -> oemChannelId;
            } else if ($fromxyktongdao == "平安银行") {
                $oemChannelId = $res_get -> result[4] -> oemChannelId;
                $msg="平安银行";
            } else if ($fromxyktongdao == "光大银行") {
                $oemChannelId = $res_get -> result[6] -> oemChannelId;
                $msg="光大银行";
            } else if ($fromxyktongdao == "华夏银行") {
                $oemChannelId = $res_get -> result[7] -> oemChannelId;
                $msg="华夏银行";
            } else if ($fromxyktongdao == "guangfa") {
                $oemChannelId = $res_get -> result[8] -> oemChannelId;
            } else if ($fromxyktongdao == "上海银行") {
                $oemChannelId = $res_get -> result[9] -> oemChannelId;
                $msg="上海银行";
            } else if ($fromxyktongdao == "minshengxs") {
                $oemChannelId = $res_get -> result[9] -> oemChannelId;
            } else if ($fromxyktongdao == "中信银行"){
                $post_arr['message']="中信银行";
                $post_arr['result']['applyUrl']="https://creditcard.ecitic.com/h5/shenqing/?sid=SJR360TX8";
                $this->jsonout('success',$post_arr['message'],$post_arr);
            }

            $arr["oemChannelId"] = $oemChannelId;
            $arr["applyName"] = $username;
            $arr["applyIdCard"] = $idcard;
            $arr["applyMobile"] = $phonenum;
            //生成签名
            $sign = $this->getSign($arr);
//生成业务参数
            $post_data["Uid"] = $Uid;
            $post_data["applyName"] = $username;
            $post_data["applyMobile"] = $phonenum;
            $post_data["applyIdCard"] = $idcard;
            $post_data["oemChannelId"] = $oemChannelId;
            $post_data["sign"] = $sign;
//调用POST请求
            $post_json = $this->https_post($urll, $post_data);

//返回的json转数组
            $post_arr = (json_decode($post_json,true));
            if ($post_arr['success'] == true){
                $tb_creditcard = M('creditcard');

                $data['uid'] = $user_id;
                $data['username'] = $username;
                $data['idcard'] = $idcard;
                $data['phonenum'] = $phonenum;
                $data['fromxyktongdao'] = $fromxyktongdao;
                $data['userdate'] = $time;
                 $tb_creditcard->add($data);
                $this->jsonout("success",$post_arr['message'],$post_arr);
            }else{
                $this->jsonout("faild2222",$post_arr['message'],$post_arr);
            }

//        }else{
//            $this->jsonout('faild333','非法操作！');
//        }
    }

    public function creditcard1(){

        //获取用户信息
      //  $user_id=$_GET['ID'];
       // $randpwd=$_GET['randpwd'];


        //调用登录验证
       // $this->login_verify($user_id,$randpwd);


        $username = $_GET['name'];
        $idcard = $_GET['sfz'];
        $phonenum = $_GET['phonenum'];
        $fromxyktongdao = $_GET['title'];
       // $yzm = $_GET['yzm'];
        $username="张飞";
        $idcard="410503199808035139";
        $phonenum="18503723336";
        $fromxyktongdao="中信银行";

//        $tb_dkinfo = M('dkinfo');
//        $dkinfo['tel'] = $phonenum;
//        $dkinfo['yzm'] = $yzm;
//
//        $wer = $tb_dkinfo->where($dkinfo)->find();
//
//
//        if (!$wer){
//            $this->jsonout('faild','验证码不对！');
//        }


//        if($user_id=="" || $username=="" || $idcard=="" || $phonenum=="" || $fromxyktongdao==""){
//            $this->jsonout('faild','非法操作！');
//        }


        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");

//        $tb_creditcard = M('creditcard');
//
//        $data['uid'] = $user_id;
//        $data['username'] = $username;
//        $data['idcard'] = $idcard;
//        $data['phonenum'] = $phonenum;
//        $data['fromxyktongdao'] = $fromxyktongdao;
//        $data['userdate'] = $time;
//        $xykinfo = $tb_creditcard->add($data);

       // if($xykinfo){

            $Uid = "1012256710569492481";
//拼接url
            $urll = "https://api.dsyhj.cn/apis/api/merchant/credit/card/save/" . $Uid;

            $res_get = $this->get_info("https://api.dsyhj.cn/apis/api/merchant/credit/card/bank/list");
//返回的json转数组
            $res_get = (json_decode($res_get));
//给银行通道ID赋值
            if ($fromxyktongdao == "民生银行") {
                $oemChannelId = $res_get -> result[0] -> oemChannelId;
            } else if ($fromxyktongdao == "交通银行") {
                $oemChannelId = $res_get -> result[1] -> oemChannelId;
            } else if ($fromxyktongdao == "浦发银行") {
                $oemChannelId = $res_get -> result[2] -> oemChannelId;
            } else if ($fromxyktongdao == "xingye") {
                $oemChannelId = $res_get -> result[3] -> oemChannelId;
            } else if ($fromxyktongdao == "平安银行") {
                $oemChannelId = $res_get -> result[4] -> oemChannelId;
            } else if ($fromxyktongdao == "光大银行") {
                $oemChannelId = $res_get -> result[5] -> oemChannelId;
            } else if ($fromxyktongdao == "华夏银行") {
                $oemChannelId = $res_get -> result[6] -> oemChannelId;
            } else if ($fromxyktongdao == "guangfa") {
                $oemChannelId = $res_get -> result[7] -> oemChannelId;
            } else if ($fromxyktongdao == "上海银行") {
                $oemChannelId = $res_get -> result[8] -> oemChannelId;
            } else if ($fromxyktongdao == "minshengxs") {
                $oemChannelId = $res_get -> result[9] -> oemChannelId;
            } else if ($fromxyktongdao == "中信银行"){
                $post_arr['message']="中信银行";
                $post_arr['result']['applyUrl']="https://creditcard.ecitic.com/h5/shenqing/?sid=SJR360TX8";
                $this->jsonout('success','中信银行',$post_arr);
            }


            $arr["oemChannelId"] = $oemChannelId;
            $arr["applyName"] = $username;
            $arr["applyIdCard"] = $idcard;
            $arr["applyMobile"] = $phonenum;
            //生成签名
            $sign = $this->getSign($arr);
//生成业务参数
            $post_data["Uid"] = $Uid;
            $post_data["applyName"] = $username;
            $post_data["applyMobile"] = $phonenum;
            $post_data["applyIdCard"] = $idcard;
            $post_data["oemChannelId"] = $oemChannelId;
            $post_data["sign"] = $sign;
//调用POST请求
            $post_json = $this->https_post($urll, $post_data);
//返回的json转数组
            $post_arr = (json_decode($post_json,true));
          //  $this->jsonout("success",$post_arr['message'],$post_arr);


            print_r($post_arr);
      //  header('Location: ' . $post_arr['result']['applyUrl']);



//            if($fromxyktongdao=="交通银行"){
//                $linkurl['url'] = 'https://creditcardapp.bankcomm.com/applynew/front/apply/track/record.html?trackCode=A0428102278573';
//                $this->jsonout('success','交通银行',$linkurl);
//            }if($fromxyktongdao=="浦发银行"){
//                $linkurl['url'] = 'https://ecentre.spdbccc.com.cn/creditcard/indexActivity.htm?data=I2378563&itemcode=bjymt00028';
//                $this->jsonout('success','浦发银行',$linkurl);
//            }if($fromxyktongdao=="民生银行"){
//                $linkurl['url'] = 'https://creditcard.cmbc.com.cn/wsv2/?etr=x7fV66aX/xuu4rcMAyW9FEUC0nUCDxq5UrFVGI/5/KYRODDzwfdRmuOvW6rr9PV6dStxXVtTniQHThWwDQ%206RqwiwxYGf4Vmq891hu1PrDY3DmlLZFK8A9wWGv/vcZVqV05bB%20l%20590zhX8UfpEXwaQQpnFLpDVf0M09fkjkDivKvtVw0nsnHQd3p6PUENJlEimvYv/qKVQfV1c2iXwnXF%20z9nhkgOt3JoamDqZnyF62m/4w/XY0FiLU48WoIwInHoj1YF7NtOvUbHzS/7okfTujeUpHdRUKA6nTl9tTjNtOw5YRG2hosSi3OYDlhpWI3fmjDixlNYDW7aquwiGv8ZqFSmlBygeGx24ugfue6siC5BG4Xdh6msv54RvWJU/bmMUtdXMqRXTwo%20o2CJU6cEKEtTXc%20ZTYcNbH4sNzWekPb0ZR3rt8rXTXncL1MW/NIEqWWiX0vC7wscwsihqWtJngxSmXBeIinVO%208jt98%20hUMsD6tg3oC/bxkFwzsL63&time=1526527277145';
//                $this->jsonout('success','民生银行',$linkurl);
//            }if($fromxyktongdao=="平安银行"){
//                $linkurl['url'] = 'https://bank-static.pingan.com.cn/ca/index.html?channel=SGM&onlineSQFlag=N&sign=0c32ef15-5668-4427-a817-ab73176ec865d6344ed1159791dfd40f16970165366c&ccp=4a3a1a8a9a2a30ap3at4&cardCatenaNo=01a02a03a04a05a06&versionNo=R10338&scc=233610097&cc=NCP00002&isDisplaySales=Y&bt2=EDY00015131&bt5=employmee_edm&bt7=V0182&bt8=m_O0M5pDjt9oDLWpjM5131&salesName=%E4%BF%9D%E9%99%A9%E5%B1%8B%E4%B9%9D&salesCode=EDY00015131';
//                $this->jsonout('success','平安银行',$linkurl);
//            }if($fromxyktongdao=="光大银行"){
//                $linkurl['url'] = 'https://xyk.cebbank.com/cebmms/apply/ps/card-list.htm?level=124&pro_code=FHTG060000SJ162SHLD';
//                $this->jsonout('success','光大银行',$linkurl);
//            }if($fromxyktongdao=="中信银行"){
//                $linkurl['url'] = 'https://creditcard.ecitic.com/h5/shenqing/?sid=SJR360TX8';
//                $this->jsonout('success','中信银行',$linkurl);
//            }if($fromxyktongdao=="上海银行"){
//                $linkurl['url'] = 'https://mbank.bankofshanghai.com/Latte/#/CreditHot?YLLink=770070';
//                $this->jsonout('success','上海银行',$linkurl);
//            }if($fromxyktongdao=="华夏银行"){
//                $linkurl['url'] = 'https://creditapply.hxb.com.cn/fenhang/FHCardChoice.html?requestId=256&requestPage=1255&employeeID=92299630415';
//                $this->jsonout('success','华夏银行',$linkurl);
//            }

      //  }else{
           // $this->jsonout('faild','非法操作！');
      //  }

    }
public function getlist(){
    $applyName = "韩曜锴";
    $applyMobile = "18530599446";
    $applyIdCard = "410522199304229334";
    $js_xyktongdao = "交通银行";
    $Uid = "1012256710569492481";
//拼接url
    $urll = "https://api.dsyhj.cn/apis/api/merchant/credit/card/save/" . $Uid;

    $res_get = $this->get_info("https://api.dsyhj.cn/apis/api/merchant/credit/card/bank/list");
//返回的json转数组
    $res_get = (json_decode($res_get));
//给银行通道ID赋值
    if ($js_xyktongdao == "民生银行") {
        $oemChannelId = $res_get -> result[0] -> oemChannelId;
        $msg="民生银行";
    } else if ($js_xyktongdao == "交通银行") {
        $oemChannelId = $res_get -> result[1] -> oemChannelId;
        $msg="交通银行";
    } else if ($js_xyktongdao == "浦发银行") {
        $oemChannelId = $res_get -> result[2] -> oemChannelId;
        $msg="浦发银行";
    } else if ($js_xyktongdao == "xingye") {
        $oemChannelId = $res_get -> result[3] -> oemChannelId;
    } else if ($js_xyktongdao == "平安银行") {
        $oemChannelId = $res_get -> result[4] -> oemChannelId;
        $msg="平安银行";
    } else if ($js_xyktongdao == "光大银行") {
        $oemChannelId = $res_get -> result[5] -> oemChannelId;
        $msg="光大银行";
    } else if ($js_xyktongdao == "华夏银行") {
        $oemChannelId = $res_get -> result[6] -> oemChannelId;
        $msg="华夏银行";
    } else if ($js_xyktongdao == "guangfa") {
        $oemChannelId = $res_get -> result[7] -> oemChannelId;
    } else if ($js_xyktongdao == "上海银行") {
        $oemChannelId = $res_get -> result[8] -> oemChannelId;
        $msg="上海银行";
    } else if ($js_xyktongdao == "minshengxs") {
        $oemChannelId = $res_get -> result[9] -> oemChannelId;
    }else if ($js_xyktongdao == "中信银行"){
        echo"1111";
    }
    $arr["oemChannelId"] = $oemChannelId;
    $arr["applyName"] = $applyName;
    $arr["applyIdCard"] = $applyIdCard;
    $arr["applyMobile"] = $applyMobile;
    //生成签名
    $sign = $this->getSign($arr);
//生成业务参数
    $post_data["Uid"] = $Uid;
    $post_data["applyName"] = $applyName;
    $post_data["applyMobile"] = $applyMobile;
    $post_data["applyIdCard"] = $applyIdCard;
    $post_data["oemChannelId"] = $oemChannelId;
    $post_data["sign"] = $sign;
//调用POST请求
    $post_json = $this->https_post($urll, $post_data);
//返回的json转数组
    $post_arr = (json_decode($post_json,true));

    $this->jsonout("success",$msg,$post_arr);
}

    //GET请求
    function get_info($card) {
        $url = $card;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', ));
        //执行并获取HTML文档内容
        $output = curl_exec($curl);
        //释放curl句柄
        curl_close($curl);
        return $output;
    }

    function https_post($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', ));
        $ref = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $ref;
    }
    function getSign($params) {
        $sign = $params['oemChannelId'] . $params['applyName'] . $params['applyIdCard'] . $params['applyMobile'] . 'dot';
        $ret = strtoupper(md5(urlencode($sign)));
        return $ret;
    }

}