<?php
require_once LIB_PATH.'/GatewayClient/Gateway.php';

use GatewayClient\Gateway;

class IndexAction extends CommonAction
{
	public function index()
	{
	    //$gameInfo=D('Room')->
        $data=array(
            0=>array(
                'title'=>'扫雷',
                'game'=>'saolei',
                'img'=>'img/game1.png'
            ),
            1=>array(
                'title'=>'接龙',
                'game'=>'jielong',
                'img'=>'img/game2.png'
            )
        );
        $this->ajaxReturn($data,'success',1);

	}

	public function test1(){
        Gateway::$registerAddress = '127.0.0.1:1238';

	    $data=array(
	        'roomid'=>3735273,
            'm'=>1,
            'data'=>array(
                'username'=>'美女',
                'user_id'=>'1675547',
                'avatar'=>'http://www.baidu.com',
                'hongbao_id'=>'38',
                'money'=>'500',
                'bom_num'=>5
            )
        );
	    $data=json_encode($data);
        Gateway::sendToAll($data);
        echo '发送完毕';


    }
    public function test4(){
        Gateway::$registerAddress = '127.0.0.1:1238';

        $data=array(
            'roomid'=>3735273,
            'm'=>1,
            'data'=>array(
                'username'=>'别人的',
                'user_id'=>'1675590',
                'avatar'=>'http://www.baidu.com',
                'hongbao_id'=>'38',
                'money'=>'2000',
                'bom_num'=>9
            )
        );
        $data=json_encode($data);
        Gateway::sendToAll($data);
        echo '发送完毕';


    }
    public function test2(){
        Gateway::$registerAddress = '127.0.0.1:1238';

        $data=array(
            'roomid'=>3735273,
            'm'=>2,
            'data'=>array(
                'username'=>'美女',
                'user_id'=>'1675547',
                'aword'=>'50',
                'hongbao_id'=>'38',
                'money'=>'500',
                'bom_num'=>7,
                'type'=>1

            )
        );
        $data=json_encode($data);
        Gateway::sendToAll($data);
        echo '发送完毕';


    }
    public function test3(){
        Gateway::$registerAddress = '127.0.0.1:1238';

        $data=array(
            'roomid'=>3735273,
            'm'=>3,
            'data'=>array(
                'username'=>'美女',
                'user_id'=>'1675547',
                'hongbao_id'=>'38',
                'money'=>'600',
                'bom_num'=>3
            )
        );
        $data=json_encode($data);
        Gateway::sendToAll($data);
        echo '发送完毕';
    }
    public function redis(){

        $s=Cac()->lRange('kickback_queue_1',0,-1);
        print_r($s);
    }
    public function test22(){
	    $this->uid=1675552;
        echo D('Users')->getUserMoney($this->uid);
    }
}