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
                'game'=>'saolei'
            ),
            1=>array(
                'title'=>'接龙',
                'game'=>'jielong'
            )
        );
        $this->ajaxReturn($data,'success',1);

	}

	public function test1(){
        Gateway::$registerAddress = '127.0.0.1:1238';

	    $data=array(
	        'roomid'=>1007,
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
            'roomid'=>1007,
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
            'roomid'=>1007,
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
            'roomid'=>1007,
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
        $redis = new redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->delete('test');
        $redis->delete('test_2');
        $redis->lpush("test","111");
        $redis->lpush("test","222");
        $redis->rpush("test","333");
        $redis->rpush("test","444");
        $s=$redis->lRange('test',0,-1);
        print_r($s);
        foreach ($s as $v){
            $redis->rpush("test_2",$v);
        }
        //$redis->set('test_2',$s);
        var_dump($redis->lget("test_2",2));
    }
}