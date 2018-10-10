<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/21
 * Time: 14:07
 */

class UpgradeAction extends CommonAction
{


    //todo 升级会员       Lee_zhj
    public function upgrade()
    {
        date_default_timezone_set('PRC');
        $time = date("Y-m-d H:i:s");
        //$userOpenID=$_POST['openid'];
        $userOpenID='oFaDv1cc3HPvj3m4QH4UMyEBxeS4';

        //todo 查询user表的ID，openID       Lee_zhj
        $tb_user = M(user);
        //$user['openid']=$userOpenID;
        $user['openID'] = $userOpenID;
        //$user['ID'] = 1146;


        $ID = $tb_user->where($user)->getField('ID');
        var_dump($ID);
        $ID=1146;
        $sqluseropenID = $tb_user->where($user)->getField('openID');


       // var_dump($sqluserID);
      //  var_dump($sqluseropenID);
        //die($sqluser1);

        //根据主键ID查询用户姓名
        $sqlusernickName = $tb_user->where($user)->getField('nickName');

        $sqlusernickName='leeleelee';
       // var_dump($sqlusernickName);


        //根据订单id查询订单;
        //判断应付金额和实际付款金额是否一致；
        //取出套餐类型
        $tb_order = M(order);
        //$orderid=$_POST['orderid'];
        $orderid=9999;
        //$realpay=$_POST['realpay'];
        $order['ID'] = '905';
        //$realpay=0.01;

        //$taocan = isset($_GET["taocan"])?$_GET["taocan"]:'';
        $taocan=198;
        $sqlorder = $tb_order->where($order)->Field('ID, payMoney, title')->select();

        //var_dump($sqlorder);
        //die();



        //---????????入库之前判断????????????????????????????--------------------------------------
        /*$payin = mysql_query($sqlorder) or die(mysql_error());

        while($row = mysql_fetch_array($payin))
        {
            $pay[]=$row;
        }

        $taocan=$pay[0]['title'];
//$taocan=198;

        if($taocan==''||!'oFaDv1cc3HPvj3m4QH4UMyEBxeS4'){
            die("非法访问-02");
        }*/
        //----------入库之前判断????????????????????????????--------------------------------------



        //查询当前用户的ID,支付状态,上级ID,会员等级
        /*$str_sqlpay = $tb_user->where($user)->field('ID,if_pay,p1id')->select();
        echo "<br>";
        var_dump($str_sqlpay);*/

        echo "<br>";
      //  $temppay$temppay = $tb_user->where($user)->getField('if_pay');
        $parent = $tb_user->where($user)->getField('p1id');
        $userlv = $tb_user->where($user)->getField('user_lv');

        $temppay = $tb_user->where($user)->getField('if_pay');

        //$temppay=1;

        echo "<br>";
       //var_dump($parent);
        echo "<br>";
      //  var_dump($userlv);
        echo "<br>";
       // var_dump($temppay);


//        if($temppay ){
//            echo "1";
//            exit();
//        }
        //echo "2";
        //die();

        //如果用户购买的是598套餐，更改数据库的状态
        /*if($taocan=='598'){
	        mysql_query("update tb_user set vip_rank=2,if_pay=1 WHERE ID=$ID") or die(mysql_error());
        	mysql_query("update tb_user set pay_num=pay_num+1 WHERE ID='$parent'") or die("ERROR1");
        }else{
	        mysql_query("update tb_user set vip_rank=1 WHERE ID=$ID") or die("ERROR2");
        }*/


        $parents=array();

       $this->getparents($parent,3,$parents);
//        //$tb_user = M(user);
//        $userq['ID'] = 1146;
//
//        $str_sql1=$tb_user->where($userq)->field('ID,p1id,user_lv,vip_rank,openID')->select();
//        //print_r($str_sql1);
//        //die();
//
//        // $str_sql1="SELECT ID,p1id,user_lv,vip_rank,openID FROM tb_user where ID=$ID";
//
//            $parents['ID']=$str_sql1[0]['ID'];
//            $parents['p1id']=$str_sql1[0]['p1id'];
//            $parents['userlv']=$str_sql1[0]['user_lv'];
//            $parents['viprank']=$str_sql1[0]['vip_rank'];
//            $parents['deep']=1;
//
//        var_dump($parents);
//        die();


            if($taocan=="598") {
                $readyback = 0;
                $is_glf = 1;

                foreach($parents as $key=>$value) {

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
            }else {
                $readyback = 0;
                $is_glf = 1;

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



            //var_dump($parents);



        foreach($parents as $k=>$v) {
            //返佣
            $glomoney=$v['glf']+$v['realback'];

            //var_dump($v);
            //die();
            if($glomoney>0){

                $ulimit['ulimit'] = 'ulimit+$glomoney';
                $tb_user->where('id==\'".$v[\'ID\']."\'')->save($ulimit); // 根据条件更新记录

                var_dump($glomoney);
                //$sql1 = "UPDATE tb_user SET ulimit=ulimit+$glomoney WHERE ID='".$v['ID']."'";
                //$result1 = mysql_query($sql1) or die("ERROR3");
            }
        }

        //插入返佣数据表
        if(!isset($parents[1])){
            $parents[1]['realback']=0;
            $parents[1]['glf']=0;
        }

        if(!isset($parents[2])){
            $parents[2]['realback']=0;
            $parents[2]['glf']=0;
        }

        foreach($parents as $key=>$value){
            if(($value['realback']+$value['glf'])>0){
                $data['ushangji']=$value['openID'];//分享者的 openID

                $data['uopenid']=$userOpenID;
                //$data['uopenid']='oFaDv1cc3HPvj3m4QH4UMyEBxeS4';
                $data['uprice']=$value['realback']+$value['glf'];
                $data['utaocan']=$value['taocan'];
                //$this->https_post("http://jinfu.yiaigo.com/notic_yongjin.php",$data);

            }

        }


        //数据写入返佣表
        $tb_fanyong = M("fanyong"); // 实例化User对象
        $fanyong['ordeId'] = $orderid;
        $fanyong['openID'] = $ID ;
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

        //$tb_fanyong->data($fanyong)->add();
        $list=$tb_fanyong->add($fanyong);

//        if ($list){
//            $data['msg']="success";
//            $data['error']=1;
//            die(json_encode($data));
//        }else{
//            $data['msg']="fail";
//            $data['error']=2;
//            die(json_encode($data));
//        }

        echo "------amaze------";
        var_dump($fanyong);
        //die();
        //$result2 = mysql_query($tb_fanyong) or die("ERRORfanyong");
        //var_dump($result2);

        echo "------amazing------";
    }


  public function getparents($ID,$deep=1,&$object){

      $tb_user = M(user);
      $userq['ID'] = $ID;

      $str_sql1=$tb_user->where($userq)->field('ID,p1id,user_lv,vip_rank,openID')->select();
      //print_r($str_sql1);
      //die();

      // $str_sql1="SELECT ID,p1id,user_lv,vip_rank,openID FROM tb_user where ID=$ID";

      $temp['ID']=$str_sql1[0]['ID'];
      $temp['p1id']=$str_sql1[0]['p1id'];
      $temp['userlv']=$str_sql1[0]['user_lv'];
      $temp['viprank']=$str_sql1[0]['vip_rank'];
      $temp['deep']=1;

      var_dump($temp);
      //die();

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



//    public function https_post($url,$data)
//    {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
//        curl_setopt($curl, CURLOPT_POST, 1);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//
//        $ref =curl_exec($curl);
//
//        if (curl_errno($curl)) {
//            return 'Errno'.curl_error($curl);
//        }
//        curl_close($curl);
//        return $ref;
//
//    }

}

