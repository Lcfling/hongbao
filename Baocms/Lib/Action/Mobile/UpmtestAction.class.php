<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 10:29
 */


class UpmtestAction extends CommonAction{

    //todo 升级会员返佣逻辑      Lee_zhj
    public function upmember($order_sn){

        //global $order_sn;
        //$order_sn='A7171540606615777274';

        $tb_order = M(order);
        $ID=$tb_order->where('outTradeNo='.$order_sn)->getField('uid');
        //$ID=811;

        //日期格式化
        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");

        //根据$ID查询用户姓名
        $tb_user = M(user);
        $sqlusernickName = $tb_user->where('ID='.$ID)->getField('nickName');
        //var_dump($sqlusernickName);


        //根据订单ID更新orderState
        $orderState['orderState'] =1;
        $tb_order->where('ID='.$order_sn)->save($orderState); // 根据条件更新记录

        //根据订单号查询订单
        //判断应付金额和实际付款金额是否一致
        //取出套餐类型
        $sql=$tb_order->where('outTradeNo='.$order_sn)->field('ID,payMoney,title')->select();
        //var_dump($orderidmsg);

        $taocan=$sql[0]['title'];
        //echo "套餐类型";
        //var_dump($taocan);

//        if($taocan==''||!$ID){
//            die("非法访问，请退出！");
//        }


        //查找当前用户信息
        $temppay = $tb_user->where('ID='.$ID)->getField('if_pay');
        $parent = $tb_user->where('ID='.$ID)->getField('p1id');
        $userlv = $tb_user->where('ID='.$ID)->getField('user_lv');

        //echo "_________---------------------";
        //var_dump($temppay);
        //var_dump($parent);
        //var_dump($userlv);


        //如果支付过，则退出
        if($temppay){
            exit();
        }

        if($taocan=='598'){
            //更新tb_user表中的用户信息
            $rankpay['vip_rank'] =2;
            $rankpay['if_pay'] =1;
            $tb_user->where('ID='.$ID)->save($rankpay); // 根据条件更新记录

            $tb_user->where('ID='.$parent)->setInc('pay_num'); // 用户的上级的支付人数加1
        }else{
            $rankpayelse['vip_rank'] =1;
            $tb_user->where('ID='.$ID)->save($rankpayelse); // 根据条件更新记录
        }




        $parents=array();
        $this->getparents($parent,3,$parents);

        if($taocan=='598'){
            $readyback=0;
            $is_glf=1;
            foreach($parents as $key=>$value){
                if($value['userlv']==1&&$value['viprank']<1){
                    $parents[$key]['backmoney']=50;
                    $parents[$key]['realback']=50-$readyback;
                }elseif($value['userlv']==1&&$value['viprank']>=1){
                    $parents[$key]['backmoney']=278;
                    $parents[$key]['realback']=278-$readyback;
                }elseif($value['userlv']==2){
                    $parents[$key]['backmoney']=368;
                    $parents[$key]['realback']=368-$readyback;
                }elseif($value['userlv']==3){
                    $parents[$key]['backmoney']=438;
                    $parents[$key]['realback']=438-$readyback;
                }

                $readyback+=$parents[$key]['realback'];
                if($parents[$key]['realback']<=0||$value['deep']<2||($value['viprank']<$parents[$key-1]['viprank'])){
                    $parents[$key]['realback']=0;
                }

                //管理费
                if($value['viprank']<2||($value['userlv']<$parents[$key-1]['userlv'])||$value['userlv']<$userlv){
                    $is_glf=0;
                }

                if($value['deep']==3||$is_glf==0){
                    $parents[$key]['glf']=0;
                }else{
                    $parents[$key]['glf']=15;
                }

            }

        }else{
            $readyback=0;
            $is_glf=1;
            foreach($parents as $key=>$value){
                if($value['userlv']==1&&$value['viprank']<1){
                    $parents[$key]['backmoney']=20;
                    $parents[$key]['realback']=20-$readyback;
                }elseif($value['userlv']==1&&$value['viprank']>=1){
                    $parents[$key]['backmoney']=98;
                    $parents[$key]['realback']=98-$readyback;
                }elseif($value['userlv']==2){
                    $parents[$key]['backmoney']=128;
                    $parents[$key]['realback']=128-$readyback;
                }elseif($value['userlv']==3){
                    $parents[$key]['backmoney']=158;
                    $parents[$key]['realback']=158-$readyback;
                }

                $readyback+=$parents[$key]['realback'];
                if($parents[$key]['realback']<=0||$value['deep']<2||($value['viprank']<$parents[$key-1]['viprank'])){
                    $parents[$key]['realback']=0;
                }

                if($value['viprank']<1||($value['viprank']<$parents[$key-1]['viprank'])||($value['userlv']<$parents[$key-1]['userlv'])||$value['userlv']<$userlv){

                    $is_glf=0;
                }

                if($value['deep']==3||$is_glf==0){
                    $parents[$key]['glf']=0;
                }else{
                    if($value['viprank']==1){
                        $parents[$key]['glf']=10;
                    }else{
                        $parents[$key]['glf']=15;
                    }

                }

            }

        }



        //返佣
        foreach($parents as $k=>$v){
            $glomoney=$v['glf']+$v['realback'];
            if($glomoney>0){
                $tb_user->where('ID='.$v['ID'])->setInc('ulimit',$glomoney);//上级ulimit
            }
        }


        //判断条件
        if(!isset($parents[1])){
            $parents[1]['realback']=0;
            $parents[1]['glf']=0;
        }

        if(!isset($parents[2])){
            $parents[2]['realback']=0;
            $parents[2]['glf']=0;
        }


        //返佣提示
        /*foreach($parents as $key=>$value){
            if(($value['realback']+$value['glf'])>0){
                $data['ushangji']=$value['openID'];//分享者的 openID
                $data['uopenid']=$userOpenID;
                $data['uprice']=$value['realback']+$value['glf'];
                $data['utaocan']=$value['taocan'];
                https_post("http://jinfu.yiaigo.com/notic_yongjin.php",$data);
            }
        }*/


        //数据插入返佣表
        $tb_fanyong = M(fanyong); // 实例化User对象
        $fanyong['orderId'] = $order_sn;
        $fanyong['uid'] = $ID ;
        $fanyong['p1id'] = $parents[0]['ID'] ;
        $fanyong['p2id'] = $parents[1]['ID'] ;
        $fanyong['p3id'] = $parents[2]['ID'] ;
        $fanyong['p1fy'] = $parents[0]['realback'] ;
        $fanyong['p2fy'] = $parents[1]['realback'] ;
        $fanyong['p3fy'] = $parents[2]['realback'] ;
        $fanyong['p1gl'] = $parents[0]['glf'] ;
        $fanyong['p2gl'] = $parents[1]['glf'] ;
        $fanyong['p3gl'] = $parents[2]['glf'] ;
        $fanyong['userOrder'] = $taocan ;
        $fanyong['isFlag'] = 1 ;
        $fanyong['fyDate'] = $time ;
        $fanyong['nickName'] = $sqlusernickName ;

        $tb_fanyong->add($fanyong);
        var_dump($fanyong);


    }
    //todo 查找用户上级信息       Lee_zhj
    function getparents($ID,$deep=1,&$object){

        //根据$ID查找上级信息
        $tb_user = M(user);
        $str_sql1=$tb_user->where('ID='.$ID)->field('ID,p1id,user_lv,vip_rank,openID')->select();

        $temp['ID']=$str_sql1[0]['ID'];
        $temp['p1id']=$str_sql1[0]['p1id'];
        $temp['userlv']=$str_sql1[0]['user_lv'];
        $temp['viprank']=$str_sql1[0]['vip_rank'];
        $temp['deep']=$deep;


        //如果存在上级
        if($temp){
            array_push($object,$temp);
            $deep--;

            if($deep>0&&($temp['ID']!=$temp['p1id'])){
                $this->getparents($temp['p1id'],$deep,$object);
            }

        }else{
            return;
        }

    }





}