<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 10:15
 */

class ZnhkbankAction extends CommonAction {

    /**
     * 文件：注册和开通
     * User: hyk
     * Date: 2018/8/1
     * Time: 11:25
     */

     //todo 查询是否实名认证
    public function index(){

        $user_id=$_GET['user_id'];

        $tb_znhk=M("znhk");
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->field('idCard,phone')->find();

        if ($list['idCard'] == "" || $list['phone'] == ""){
            $this->jsonout('faild','未实名认证',$list);
        }else{
            $this->jsonout('success','已实名认证',$list);
        }

    }

    //todo 注册开通+实名认证
   public function register()
    {
        //秘钥
        $key = '8e2d0e2ee2523e54b1f2858742b03438';
        //私钥
        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        //请求接口
        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/registMember';

        //代理商编号
       $channelNo="478927021147422720";



//        $user_id=8949;     // id
//        $phone="18739707237";  //手机号
//        $idcard="41050319890124511X"; //身份证号
//        $name="刘辰飞"; //姓名


        $user_id=$_GET['user_id'];// id
        $phone=$_GET['phone'];//手机号
        $idcard=$_GET['idcard'];//身份证号
        $name=$_GET['name'];//姓名

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);

     // 查询是否已注册
        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $znhk['phone']=$phone;
        $list=$tb_znhk->where($znhk)->find();

        if ($list){
            //已注册成功直接进行实名认证
            $this->Authentication($name,$idcard,$list['registrationNo']);
        }


        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'mobile' => $phone,
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);


        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;

        $res = $this->https_request($apiUrl);

        $res=json_decode($res);


        if ($res->code =='000000'){
            $tb_znhk=M("znhk");
            $znhk['user_id']=$user_id;
            $znhk['phone']=$phone;
            $znhk['registrationNo']=$res->data->registrationNo;
            $tb_znhk->add($znhk);

            //调用身份验证方法
            $this->Authentication($name,$idcard,$res->data->registrationNo,$rand);
        }else{
        // print_r($res);
         $this->jsonout("faild111",$res->msg,$res);

        }
    }


    // todo 身份证认证方法   参数 1.姓名 2.身份证号 3.注册号
    public function Authentication($name,$idcard,$registrationNo){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/certification';
        //代理商编号
        $channelNo="478927021147422720";

//        $key = '52ab9e748b6767f941784a86d8b97658';
//        $priKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAOBEpl2yeYwQfNPJDHI0NEEdVx+2uImspE7gb8+cM/wR2GdZ7p8j3+Zy3XCYUqRF/nojkvfNRkmukTAylZIjt8WDtdVzQtbuc1eB18RZBCECagKnVxlFkxPb4zuXDtbUHq1IsQfHRCfe2vWaXC4vG5e9KyQZ9OAlq/06NEW1zRD3AgMBAAECgYEAgKE/XpxgNKK8ReiZd+NTWUmP0APIQkbAEvGNj+FCu8Asg9LEF4jHAfE96zeips/yjnFa+UBGoTo70g4hVamg29iNcd/VXvREY9Fe2qtnDqdo9nqx4qolxR6/TFwLsNIwOkLpddA9mOUtB60IRjSqa3hbVKKbdXuX8QC/Rf/2mpkCQQD4duDZCjpNQnAlUBi9BXralgT4Qrlb1nDzrrloeiQEUZ8/1MhkmusMHTa3E4XoH8D40KqqtpWmy23idkvgvXrjAkEA5xHobGDC3cSi+NVPT8l8YjJbqFWvV/038c/XfOv2Xa1UFGYz+RhyA8i9Ixk78yfjambSZM1bzrv4R/XUn/gJ3QJATDvNSUqGEOZtmkU1Een4g9C7vaBbVv44scvOP2waWOjiP6d9xMBzlcfw3cMztsDnaHA9rRtQV1jbYjyBk3cPmQJAGF3kE6G0iuxUf0cHQROvQS+sSLkYb3/taVuQjsTXSxOfHTOV4Xu5cjq170CW+NJJAgxrvWOGfeuGiBgdXu0qJQJAB+b/f/Nf4X3zh7hjHz61XhHtupoG/sAyVKVoQzpbSwpNqm0YufEwx1NKPe0ZzrNND6ldjzm1EvBbseVGYQ7WCg==';
//        $pubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDgRKZdsnmMEHzTyQxyNDRBHVcftriJrKRO4G/PnDP8EdhnWe6fI9/mct1wmFKkRf56I5L3zUZJrpEwMpWSI7fFg7XVc0LW7nNXgdfEWQQhAmoCp1cZRZMT2+M7lw7W1B6tSLEHx0Qn3tr1mlwuLxuXvSskGfTgJav9OjRFtc0Q9wIDAQAB';
//        $apiUrl = 'http://openapi.allinpaycard.cn/allinpay.transfer.api/certification';
//
//        $channelNo="123456789";
        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'customerName' => $name,
            'registrationNo' => $registrationNo,
            'idCardNo' => $idcard
        ];

        //转成json数据
        $jsonParams = json_encode($params,JSON_UNESCAPED_UNICODE );
        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);
        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);
        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;

        $resr = $this->https_post1($apiUrl,$jsonParams);

        $resr=json_decode($resr);


        if ($resr->code == '000000'){

            $tb_znhk=M("znhk");
            $znhk['name']=$name;
            $znhk['idCard']=$idcard;
            $tb_znhk->where("registrationNo='$registrationNo'")->save($znhk);
            $this->jsonout('success',$resr->msg,$resr);

        }else{
           // print_r($resr);
            $this->jsonout('faild222',$resr->msg,$resr);
        }
    }

    // todo 完善信息
    public  function Addinformation(){
        $key = '52ab9e748b6767f941784a86d8b97658';
        $priKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAOBEpl2yeYwQfNPJDHI0NEEdVx+2uImspE7gb8+cM/wR2GdZ7p8j3+Zy3XCYUqRF/nojkvfNRkmukTAylZIjt8WDtdVzQtbuc1eB18RZBCECagKnVxlFkxPb4zuXDtbUHq1IsQfHRCfe2vWaXC4vG5e9KyQZ9OAlq/06NEW1zRD3AgMBAAECgYEAgKE/XpxgNKK8ReiZd+NTWUmP0APIQkbAEvGNj+FCu8Asg9LEF4jHAfE96zeips/yjnFa+UBGoTo70g4hVamg29iNcd/VXvREY9Fe2qtnDqdo9nqx4qolxR6/TFwLsNIwOkLpddA9mOUtB60IRjSqa3hbVKKbdXuX8QC/Rf/2mpkCQQD4duDZCjpNQnAlUBi9BXralgT4Qrlb1nDzrrloeiQEUZ8/1MhkmusMHTa3E4XoH8D40KqqtpWmy23idkvgvXrjAkEA5xHobGDC3cSi+NVPT8l8YjJbqFWvV/038c/XfOv2Xa1UFGYz+RhyA8i9Ixk78yfjambSZM1bzrv4R/XUn/gJ3QJATDvNSUqGEOZtmkU1Een4g9C7vaBbVv44scvOP2waWOjiP6d9xMBzlcfw3cMztsDnaHA9rRtQV1jbYjyBk3cPmQJAGF3kE6G0iuxUf0cHQROvQS+sSLkYb3/taVuQjsTXSxOfHTOV4Xu5cjq170CW+NJJAgxrvWOGfeuGiBgdXu0qJQJAB+b/f/Nf4X3zh7hjHz61XhHtupoG/sAyVKVoQzpbSwpNqm0YufEwx1NKPe0ZzrNND6ldjzm1EvBbseVGYQ7WCg==';
        $pubKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDgRKZdsnmMEHzTyQxyNDRBHVcftriJrKRO4G/PnDP8EdhnWe6fI9/mct1wmFKkRf56I5L3zUZJrpEwMpWSI7fFg7XVc0LW7nNXgdfEWQQhAmoCp1cZRZMT2+M7lw7W1B6tSLEHx0Qn3tr1mlwuLxuXvSskGfTgJav9OjRFtc0Q9wIDAQAB';
        $apiUrl = 'http://openapi.allinpaycard.cn/allinpay.transfer.api/extendedInfo';

        $user_id=$_GET['user_id']; // 用户id
        $education=$_GET['education']; // 学历
        $career=$_GET['career']; //职业
        $address=$_GET['address'];//地址

        // 查询注册号
        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();

        //封装参数
        $params = [
            'channelNo' => '123456789',
            'channelOrderId' =>'okokok1',
            'timestamp' => time() .'',
            'registrationNo' => $list['registrationNo'],
            'education' => $education,
            'career' => $career,
            'address' => $address,

        ];


        //转成json数据
        $jsonParams = json_encode($params,JSON_UNESCAPED_UNICODE );
        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);
        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);
        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;

        //调用POST请求
        $resr = $this->https_post1($apiUrl,$jsonParams);
        $resr=json_decode($resr);

        if ($resr->code == '000000'){
            $znhk1['education']=$education;// 学历
            $znhk1['career']=$career;//职业
            $znhk1['address']=$address;//地址
            $tb_znhk->where('user_id='.$user_id)->save($znhk1);

            $this->jsonout('success',$resr->msg,$resr);
        }else{

            $this->jsonout('faild',$resr->msg,$resr);
        }
    }
//查询自己的信息
    public function user(){

        $user_id=$_GET['user_id'];
        $tb_znhk=M("znhk");
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();
        if ($list){
            $this->jsonout('success','个人信息',$list);
        }else{
            $this->jsonout('faild','失败',$list);
        }

    }


    // todo 调用绑卡方法(信用卡)
    public  function addBankCard(){


        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";

        //$apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/certification';
        //代理商编号

        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/tiedCard';

        $ID=$_GET['user_id']; //用户id

        $creditCardNo=$_GET['creditCardNo'];//信用卡号
        $mobile=$_GET['mobile'];//手机号

        $name=$_GET['name']; // 姓名
        $idcard=$_GET['idcard'];//身份证号
        $bank_name=$_GET['bankname']; //银行名称
        $yzm=$_GET['yzm'];  // 验证码0

        $write=json_encode($_GET);
        $myfile = fopen("abab2.txt", "a+") ;
//        // $txt = "";
        fwrite($myfile, $write);
//
        fclose($myfile);

        $tb_dkinfo = M('dkinfo');
        $dkinfo['tel'] = $mobile;
        $dkinfo['yzm'] = $yzm;
        $wer = $tb_dkinfo->where($dkinfo)->find();

        if (!$wer){
            $this->jsonout('faild','验证码不对！');
        }


//        $ID=8949;
//        $creditCardNo= '6252470075067402';
//        $mobile="18503723336";
        // 查询注册号
        $tb_znhk=M('znhk');
        $znhk['user_id']=$ID;
        $list=$tb_znhk->where($znhk)->find();

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);

        //封装参数
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'registrationNo' => $list['registrationNo'],
            'creditCardNo' => $creditCardNo,
            'mobile' => $mobile,
        ];

        //转成json数据
        $jsonParams = json_encode($params,JSON_UNESCAPED_UNICODE );
        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);
        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;

        $res = $this->http_request($apiUrl);
        $res=json_decode($res);


        if ($res->code == '000000'){

            $tb_xybank=M('xybank');
            $xybank['user_id']=$ID;
            $xybank['phone']=$mobile;
            $xybank['xybank']=$creditCardNo;
            $xybank['limit']=$res->data->limit;
            $xybank['name']=$name;
            $xybank['idcard']=$idcard;
            $xybank['bankname']=$bank_name;
            $tb_xybank->add($xybank);
            $this->jsonout('success',$res->msg,$res);
        }else{
            //print_r($res);
            $this->jsonout('faild',$res->msg,$res);
        }
    }

    //todo 查询信用卡
    public function xybank(){


        $user_id=$_GET['user_id'];
        $tb_xybank=M("xybank");
        $xybank['user_id']=$user_id;
        $list=$tb_xybank->where($xybank)->select();
        if ($list){
            $this->jsonout('success','信用卡',$list);
        }else{
            $this->jsonout('faild','没有信用卡',$list);
        }
    }

    //todo 查询单独信用卡
    public function xykbank(){

        $user_id=$_GET['user_id'];
        $xybanknum=$_GET['xybank'];
        $tb_xybank=M("xybank");
        $xybank['user_id']=$user_id;
        $xybank['xybank']=$xybanknum;
        $list=$tb_xybank->where($xybank)->find();
        if ($list){
            $this->jsonout('success','信用卡',$list);
        }else{
            $this->jsonout('faild','失败',$list);
        }
    }

// todo 生成预约计划
   public function sendPlan(){
       $key = '8e2d0e2ee2523e54b1f2858742b03438';

       $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

       $channelNo="478927021147422720";

        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/appointApply';

        //格式化时间
        date_default_timezone_set('PRC');
//
        $user_id=$_GET['user_id'];
        $amount=$_GET['amount'];//金额
       $amount=$amount*100;
        $fee=$_GET['fee']; //手续费
       $fee=$fee*100;
        $endDate=$_GET['endData']; //结束时间
       $endDate = date("Y-m-d ",$endDate/1000);
       $creditCardNo=$_GET['creditCardNo'];//信用卡号


//aa

//
//       $user_id=8949;
//       $fee='9800';
//       $amount='1000000';
//        $endDate='2018-08-20';
//       $creditCardNo='6252470075067402';
       // 查询注册号
       $tb_znhk=M('znhk');
       $znhk['user_id']=$user_id;
       $list=$tb_znhk->where($znhk)->find();

       $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
       $rand =  substr($rand, 0, 6);


        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'registrationNo' => $list['registrationNo'],
            'amount' => $amount,
            'fee' => $fee,
            'endDate' => $endDate .'',
            'productCode' => '2',
            'creditCardNo' => $creditCardNo,
        ];


        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);
        $res=json_decode($res);

        if ($res->code == "000000"){
            $this->jsonout('success',$res->msg,$res);
        }else{
            $this->jsonout('faild',$res->msg,$res);
        }


    }
// todo 预约计划
    public function getAppointPlan(){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";

        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/getAppointPlan';
        //格式化时间
        date_default_timezone_set('PRC');

        $user_id=$_GET['user_id'];
        $orderld=$_GET['orderid'];

//        echo "orderld ==".$orderld;
//        die();

      //  $orderld='YEDC-1534157677601';
        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);

        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
         'orderId'=>$orderld
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);
        $res=json_decode($res);

      if ($res->code =='000000'){

          $res =  json_encode($res);
          $str = json_decode($res, true);
          $data = $str['data'];
          $result = array();
          foreach($data as $k => $v)
          {
              $day = strtotime(date('Y-m-d', strtotime($v['executeTime'])));
              if($day)
              {
                  $result[$day]['days'] = date('Y-m-d', strtotime($v['executeTime']));
                  $result[$day]['list'][] = $v;
              }
          }
          sort($result);
          $str['data'] = $result;
          $res=json_decode($res);
            $this->jsonout('success',$res->msg,$str);
      }else{
          $this->jsonout('faild',$res->msg,$res);
      }

    }

    // todo 申请订单
    public  function ApplicationOrder(){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";
        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/appointApplyOrder';

        //格式化时间
        date_default_timezone_set('PRC');

        $user_id=$_GET['user_id'];  //用户id
        $creditCardNo=$_GET['creditCardNo']; //信用卡号
        $mobile=$_GET['mobile']; //手机号
        $amount=$_GET['amount']; //金额
        $amount=$amount*100;
        $fee=$_GET['fee'];//手续费
        $fee=$fee*100;
       // $repaymentDate=$_GET['repaymentDate'];//还款日
        $cvv2=$_GET['cvv2']; //cvv2
        $expiredDate=$_GET['expiredDate'];//有效期
        $endDate=$_GET['endData'];//结束时间
        $endDate = date("Y-m-d ",$endDate/1000);
        $orderid=$_GET['orderid'];//订单号
        $reservedAmount=$_GET['reservedAmount'];//卡内预留金额






//        echo "user_id ==".$user_id;
//        echo " credit ==".$creditCardNo;
//        echo " mobile ==".$mobile;
//        echo " amout == ".$amount;
//        echo " fee ==".$fee;
//        echo " repay ==".$repaymentDate;
//        echo " cvv2 ==".$cvv2;
//        echo " expicedData ==".$expiredDate;
//        echo "  endData ==".$endDate;
//        echo " orderid == ".$orderid;
//        echo " reserveAmount ==".$reservedAmount;

//
//        $user_id=8944;
//        $creditCardNo='6252470075067402'; //信用卡号
//        $mobile="15083080847"; //手机号
//        $amount='60000';   // 金额
//        $fee='2000';  //手续费
//        $repaymentDate='2018-8-17'; //还款日
//        $cvv2='7402931';   // cvv2
//        $expiredDate='0226'; // 有效期
//        $endDate='2018-08-17';    //结束时间
//        $orderid='YEDC-1533957646003';//订单号
//        $reservedAmount='100000'; //卡内预留金额

        // 查询注册号
        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);


        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'registrationNo' =>$list['registrationNo'],
            'creditCardNo' => $creditCardNo,
            'mobile' => $mobile,
            'amount' => $amount,
            'fee' => $fee,
            'cvv2' => $cvv2,
            'expiredDate' => $expiredDate,
            'productCode' => '2',
            'endDate' => $endDate .'',
            'orderId' => $orderid,
            'reservedAmount' => $reservedAmount,
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);

        $res=json_decode($res);
       // print_r($res);
        if ($res->code == '000000'){
                $this->jsonout('success',$res->msg,$res);
        }else{
            $this->jsonout('faild',$res->msg,$res);
        }
    }

    //todo 创建订单
    public  function CreateAnOrder(){
        header('Content-Type:text/html;Charset=utf-8;');

        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";

        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/appointCreateOrder';

           //接收手机号


        $oriChannelOrderId=$_GET['oriChannelOrderId'];  //机构订单号

        $smsSignNo=$_GET['smsSignNo']; //签约号

//        echo  "oriChannelOrderId == ".$oriChannelOrderId;
//
//        echo  "smsSignNo == ".$smsSignNo;

//        $write=json_encode($_GET);
//        $myfile = fopen("abab1.txt", "a+") ;
//        // $txt = "";
//        fwrite($myfile, $write);
//
//        fclose($myfile);


        $vercode=$_GET['vercode'];//验证码
        $user_id=$_GET['user_id']; //用户id
        $creditCardNo=$_GET['creditCardNo'];//信用卡号
        $bankname=$_GET['bankname'];//信用卡名称
        $money=$_GET['money'];//金额
        $fee=$_GET['fee'];//手续费
        $endDate=$_GET['endData'];//结束时间
        $endDate = date("Y-m-d ",$endDate/1000);
        $orderid=$_GET['orderid'];//订单号
        $reservedAmount=$_GET['reservedAmount'];//卡内预留金额

//        $user_id=8944;
//        $oriChannelOrderId='476792378902183936';
//        $smsSignNo='1533283660924';
//        $vercode='123456';  //验证码
//        $creditCardNo='6252470075067402'; //信用卡号
//        $money="500"; //金额
//        $fee='2000';  //手续费
//        $endDate='2018-08-17';    //结束时间
//        $orderid='YEDC-1533717325959';//订单号

        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);


        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'oriChannelOrderId' => $oriChannelOrderId,
            'smsSignNo' => $smsSignNo,
            'vercode' => $vercode,
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);
        $res=json_decode($res);

        if ($res->code == '000000'){

            $tb_hkrecord=M("hkrecord");
            $hkrecord['user_id']=$user_id;
            $hkrecord['oriChannelOrderId']=$oriChannelOrderId;
            $hkrecord['smsSignNo']=$smsSignNo;//签约号
            $hkrecord['creditCardNo']=$creditCardNo;  //信用卡号
            $hkrecord['bankname']=$bankname;//信用卡名称
            $hkrecord['money']=$money;  //金额
            $hkrecord['fee']=$fee;  //手续费
            $hkrecord['endDate']=$endDate;//结束时间
            $hkrecord['orderid']=$orderid;  //订单号
            $hkrecord['channelOrderId']=$res->data->channelOrderId; //机构订单号
            $hkrecord['transId']=$res->data->transId;  //交易号
            $hkrecord['reservedAmount']=$reservedAmount; // 卡内预留金额
            $hkrecord['date']=date("Y-m-d H:i:s");
            $tb_hkrecord->add($hkrecord);
          //  print_r($res);
             $this->jsonout('success',$res->msg,$res);
        }else{
            $this->jsonout('faild',$res->msg,$res);
          //  print_r($res);
        }
    }

    //todo 查询个人所有订单
    public function dingdan(){

        $user_id=$_GET['user_id'];
       // $user_id=8953;
        $tb_hkrecord=M('hkrecord');
        $hkrecord['user_id']=$user_id;
        $data=$tb_hkrecord->where($hkrecord)->select();

            $result = array();
            foreach($data as $k => $v)
            {
                $day = strtotime(date('Y-m', strtotime($data[$k]['date'])));
                if($day)
                {
                    $result[$day]['days'] = date('Y-m', strtotime($data[$k]['date']));
                    $result[$day]['list'][] = $v;
                }
            }
            sort($result);
            $str['data'] = $result;

        if ($data){
            $this->jsonout('success','个人订单记录',$str);
        }else{
            $this->jsonout('faild','查询订单失败',$data);
        }
    }

    // todo 查询订单
    public  function QueryOrder(){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";
        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/queryOrder';

        //接收手机号
        $user_id=$_GET['user_id'];
//        $user_id=8944;

      //  $oriChannelOrderId=$_GET['oriChannelOrderId'];
        $oriChannelOrderId='477795224862851072';
       // $transId=$_GET['transId'];
        $transId='YEDC-1533956842044';
        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>'okokok1',
            'timestamp' => time() .'',
            'oriChannelOrderId' => $oriChannelOrderId,
            'transId'=>$transId,
        ];

        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);
        $res=json_decode($res);
        print_r($res);

        if ($res->code  == '000000'){
            $this->jsonout('success',$res->msg,$res);
        }else{
            $this->jsonout('faild',$res->msg,$res);
        }
    }

  // todo 获取拆单信息
    public function appointSplit(){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";
        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/appointSplit';



//        $user_id=8944;
//        $amout='1000000';  //金额
//        $endDate='2018-08-22'; //结束时间
//        $creditCardNo='6252470075067402'; //信用卡号



        $user_id=$_GET['user_id'];
        $amout=$_GET['amount']; //金额
        $endDate=$_GET['endData'];//结束时间
        $creditCardNo=$_GET['creditCardNo'];//信用卡号

//        echo "user_id ==".$user_id;
//        echo " amout ==".$amout;
//        echo " endDate ==".$endDate;
//        echo " credit ==".$creditCardNo;
//        die();




        $amout=$amout*100;
       // $endDate='1535006743019';
        $endDate = date("Y-m-d ",$endDate/1000);

        // 查询注册号
        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();

        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);


        //封装参数转json数据
        $params = [
            'channelNo' =>$channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'registrationNo'=>$list['registrationNo'],
            'amount' => $amout,
            'productCode'=>'2',
            'endDate'=>$endDate,
            'creditCardNo'=>$creditCardNo
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);

        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);
        $res=json_decode($res);

        if ($res->code == '000000'){
          $res->data->fee= round(($amout*0.0065)+($res->data->number * 100),2);
           $this->jsonout('success',$res->msg,$res);
        }else{

            $this->jsonout('faild',$res->msg,$res);
        }
    }

    // todo 查询子执行计划
    public function  queryAppointApply(){
        $key = '8e2d0e2ee2523e54b1f2858742b03438';

        $priKey="MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALlhnn/BtKqTN37vT5oF/m0Em//wX6A1NRnvb1XeISl+nPSvk+dyDFNO8QoMR/MSBSXDNAMxKMPjrIzueMQfRFTyedHyxTqNp5gHmxI2QRVA/X8drcPCVxSyi7/FXEvI5tQCPfr1R4xf4e2rtdq/iJPo3IzB0IYRKJ0dZ9ZoAUrbAgMBAAECgYEAjGrF7M1jtKSIL6zDJc6OdjM8mrOPK0HCNB/pvCfiBJUz/B8WMARyE5RC/bJpuVMX9Q+T0SsmTqKgq6tibGOmlEoRt8EXBAaTLUzOziYRyyg9/ZenE+sIXWorbqwm7thVQJx4r8k6xOdmvO+WUlYswS5tWfeFbfEiyT3ne9D97TkCQQDvPDbfg4brQzpda41SG1J81iHCGOAbwY0yT76fUZHkhGEnAwsTJ33xrp56BfgovXJ3ZDf/MGZCVDL2ZU9oClTfAkEAxl9JhzQKRka0C4oY7KA+g5o9AnGvN/mG477EUtfQ5B79aA/b677QlNQSoikWAdeznkWEUatIG3BUjRretUgthQJBAOAK1RzHcU/b+snIUmXFXp+4bY73etGjlpa6ZbuQSX/nlZBSYknC30i6DoIaGwgUOyGigmqDKhEOB1gHErFNk6MCQDkvBrce3Udc4lHhQUYU+3BcafHma6grGiNUvqtS4zifZlU1HSRcISyF5ckxJtLpJzIcwAP66BJg0z7J3CFN6TECQQC7V2kHy7LHv9kIxlHLrrZT4uSn1INODeYO4Ct1LjC2AYklDmMi7dBEbGKBSZeBvBKQtTTP+gMzwEkBcCI0aqES";

        $channelNo="478927021147422720";
        $apiUrl = 'https://openapi.allinpaycard.com/allinpay.transfer.api/queryAppointApply';

        //接收手机号

        //$orderId="YEDC-1533976263893"; //订单号

     //   $orderId='YEDC-1533956842044';QueryOrder

        $user_id=$_GET['user_id'];
        $transId=$_GET['transId'];


//        echo "transId ==".$transId;
//        die();

        $tb_znhk=M('znhk');
        $znhk['user_id']=$user_id;
        $list=$tb_znhk->where($znhk)->find();


        $rand= str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $rand =  substr($rand, 0, 6);

        //封装参数转json数据
        $params = [
            'channelNo' => $channelNo,
            'channelOrderId' =>$rand,
            'timestamp' => time() .'',
            'orderId' => $transId,
        ];
        $jsonParams = json_encode($params);

        //参数加密
        $params = $this->priEncrypt($jsonParams, $priKey);
        $params = base64_encode($params);
        $params = urlencode($params);
        //获取签名
        $sign = $this->sign($jsonParams,$priKey);
        $sign = base64_encode($sign);
        $sign = urlencode($sign);

        //拼接URL并请求
        $apiUrl .= '?key='.$key.'&sign='.$sign.'&params='.$params;
        $res = $this->http_request($apiUrl);

        $res=json_decode($res);

       if ($res->code == '000000'){

           $res =  json_encode($res);
           $str = json_decode($res, true);
           $data = $str['data'];
           $result = array();
           foreach($data as $k => $v)
           {
               $day = strtotime(date('Y-m-d', strtotime($v['executeTime'])));
               if($day)
               {
                   $result[$day]['days'] = date('Y-m-d', strtotime($v['executeTime']));
                   $result[$day]['list'][] = $v;
               }
           }
           sort($result);

           $str['data'] = $result;
           $res=json_decode($res);
           $this->jsonout('success',$res->msg,$str);
       }else{
           $this->jsonout('faild',$res->msg,$res);
       }
    }



    public static function https_post1($URL,$Post_data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($Post_data))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return  $return_content;
    }
}