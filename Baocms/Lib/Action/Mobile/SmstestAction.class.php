<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 14:18
 */

include_once('aliyun-dysms-php-sdk/api_demo/SmsDemo.php');
class SmstestAction extends CommonAction
{

    public function Index(){

        $tel = $_GET['tel'];//输入手机
        $yzm =rand(100000,999999);
        //$yzm = $_GET['yzm'];

        //$tel = '18530599446';
        //$tel = '15824678491';

        $response = SmsDemo::sendSms($_GET['tel'],$yzm);
        //存入数据库
        $tb_dkinfo = M('dkinfo');
        $dkinfo['tel'] = $tel;
        $dkinfo['yzm'] = $yzm;

        $tb_dkinfo->add($dkinfo);
        $this->jsonout("success",'发送成功');

        /*if ($response->Code == "OK"){
            //存入数据库
            $tb_dkinfo = M('dkinfo');
            $dkinfo['tel'] = $tel;
            $dkinfo['yzm'] = $yzm;

            $tb_dkinfo->add($dkinfo);
            $this->jsonout("success",'发送成功');

        }else{
            $this->jsonout("faild",'发送失败');
        }*/
    }
}