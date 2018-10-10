<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 10:49
 */

class SellsystemAction extends CommonAction{


    //TODO 分销系统      Lee_zhj
    public function sell(){

        echo '购买198套餐，一级分佣138，二级18';
        echo '<br>';
        echo '--------------------------------------------------------------------------<br>';


        $tb_client_order = M('client_order');
        $order_sn = 'ABCDEF';
        //$UID=$tb_client_order->where("outTradeNo='$order_sn'")->getField('uid');
        echo "<br>用户id";
        $UID = 3;
        var_dump($UID);


        //根据订单号查询订单
        //判断应付金额和实际付款金额是否一致
        //取出套餐类型
        $sql=$tb_client_order->where("outTradeNo='$order_sn'")->field('ID,payMoney,title')->select();

        $taocan=$sql[0]['title'];
        $taocan = '198';
        echo "<br>套餐类型";
        var_dump($taocan);
        echo '<br>';


        //查找上级信息
        $tb_client_user = M(client_user);
        $str_sql1=$tb_client_user->where('ID='.$UID)->field('ID,p1id,p2id')->select();

        $temp1=$str_sql1[0]['ID'];
        $temp2=$str_sql1[0]['p1id'];
        $temp3=$str_sql1[0]['p2id'];

        $temp3 = 2;

        var_dump($temp3);
        //查找当前用户信息
        $tb_client_user = M('client_user');
        $parent = $tb_client_user->where('ID='.$UID)->getField('p1id');


            $tb_client_fanyong = M('client_sellfanyong');

            $client_fanyong['orderId'] = 1;
            $client_fanyong['uid'] = $UID;
            $client_fanyong['p1id'] = $parent;
            $client_sellfanyong['p2id'] = $temp3;
            /*$client_sellfanyong['p1fy'] = '138';
            $client_sellfanyong['p2fy'] = '18';*/



            $tb_client_fanyong->add($client_fanyong);
            //var_dump($www);


    }


}