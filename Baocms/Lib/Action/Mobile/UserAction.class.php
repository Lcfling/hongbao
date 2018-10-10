<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/16
 * Time: 10:17
 */

class UserAction extends CommonAction {

    //todo 个人中心首页信息 ZF
    public function index(){
        //获取用户信息

        $user_id=$_GET['ID'];

        $randpwd=$_GET['randpwd'];

        //调用登陆验证
        $this->login_verify($user_id,$randpwd);
        //通过id查找自己的个人信息
        $tb_user=M('user');
        $user['ID']=$user_id;
        $userlist=$tb_user->where($user)->find();

        //返回数据
        $data['list']=$userlist;
        // 总收益
        $ulimit=$this->ulimit($user_id);
        $data['limit']=$ulimit;
        //总结算
        $yijiesuan=$this->yijiesuan($user_id);
        $data['yijiesuan']=$yijiesuan;

        //----------------------------------------------------
//        $tb_record = M('record');
//        $record['user_ID']=$user_id;
//        $record['operation']=0;
//        $record['status']=3;
//        $operation = $tb_record->where($record)->find();
//
//       if ($operation){
//
//           $tb_user->where('ID='.$user_id)->setDec('yijiesuan',$operation['txmoney']);
//
//           $opera1['operation'] = 1;
//           $opera['ID']=$operation['ID'];
//           $opera['user_ID']=$operation['user_ID'];
//
//           $tb_record->where($opera)->save($opera1);
//       }


        //----------------------------------------------------


        $this->jsonout('success','',$data);

    }

//todo 首次实名认证
  public function getdata(){

        $user_id=$_GET['ID'];

        $tb_user=M("user");
        $user['ID']=$user_id;
        $list=$tb_user->where($user)->field('user_phone,user_pwd')->find();


            if ($list['user_phone']!="" || $list['user_pwd']!=""){
                $this->jsonout("success",'已完善');
            }else{
                $this->jsonout("faild",'未完善');
            }
  }
  //todo 完善资料
    public function setdata(){

        $userID=$_GET['ID'];
        $phonenum=$_GET['phonenum'];  //手机号
        $wxnum=$_GET['wxnum'];//微信号
        $password1=$_GET['password1']; //密码1
        $password2=$_GET['password2']; // 密码2

        if ($password1 != $password2){
            $this->jsonout("faild","两次密码输入不同");
        }

        //修改用户资料
        $tb_user=M('user');

        $user['user_phone']=$phonenum;
        $user['user_wxnum']=$wxnum;
        $user['user_pwd']=$password1;

        $list=$tb_user->where('ID='.$userID)->save($user);
        if ($list){
            $this->jsonout("success","完善成功！");
        }else{
            $this->jsonout("faild","完善失败！");
        }
    }

    //  todo 账户管理收益明细 ZF
    public function earnings(){

        //获取用户信息

        $user_id=(int)$_GET['ID'];
        $randpwd=$_GET['randpwd'];

        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        // 通过id查找自己的收益来源!
        $sql2="select a.*,b.user_lv,b.vip_rank,b.headerImg from tb_fanyong as a JOIN tb_user as b on a.uid=b.ID where a.isShow=1 and   (a.p1id=$user_id or a.p2id=$user_id or a.p3id=$user_id) ;";
        $res=mysql_query($sql2);
        while($row=mysql_fetch_row($res,MYSQL_ASSOC))
        {
            $listmoney[] = $row;
        }

//
//        //查询总收益
//        $tb_fanyong=M('fanyong');
//        $fanyong1['p1id']=$user_id;
//        $money1=$tb_fanyong->where($fanyong1)->field('sum(p1fy)+sum(p1gl) as money')->select();
//
//        $fanyong2['p2id']=$user_id;
//        $money2=$tb_fanyong->where($fanyong2)->field('sum(p2fy)+sum(p2gl) as money')->select();
//
//        $fanyong3['p3id']=$user_id;
//        $money3=$tb_fanyong->where($fanyong3)->field('sum(p3fy)+sum(p3gl) as money')->select();
//        //总收益
//        $money=$money1[0]['money']+$money2[0]['money']+$money3[0]['money'];

        //总收益
        $money=$this->ulimit($user_id);

        $tb_user=M("user");
        $user_img=$tb_user->where('ID='.$user_id)->field('headerImg')->select();

        $data['headerImg']=$user_img[0]['headerImg'];
        $data['list']=$listmoney;
        $data['money']=$money;


        //贷款收益
        $tb_dkfanyong=M("dbfanyong");
        $dkfanyong['uid']=$user_id;
        $dkmoney=$tb_dkfanyong->where($dkfanyong)->field('sum(p1fy)+sum(p1gl) as money')->select();

        //信用卡收益
        $tb_xyfanyong=M("xyfanyong");
        $xyfanyong['uid']=$user_id;
        $xymoney=$tb_xyfanyong->where($dkfanyong)->field('sum(p1fy)+sum(p1gl) as money')->select();

        $this->jsonout('success','',$data);

    }

    //todo   账户管理提现记录 ZF
    public function deposit(){
        //获取用户信息

        $user_id=$_GET['ID'];

        $randpwd=$_GET['randpwd'];
        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        $tb_record=M('record');
        $record['user_ID']=$user_id;
        $tixian=$tb_record->where($record)->select();
     
        //返回数据
        $data['tixian']=$tixian;
        $this->jsonout('success','',$data);

    }

    //todo 我的银行卡 ZF
    public function bank(){

        //获取用户信息
        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];
//        //调用登陆验证
        $this->login_verify($user_id,$randpwd);
       // $user_id=811;
        // 查询自己的银行卡
        $tb_bank=M('bank');
        $bank['user_id']=$user_id;
        $banklist=$tb_bank->where($bank)->select();

        $data['list']=$banklist;
        $this->jsonout('success','我的银行卡',$data);
    }

    // todo 添加银行卡 zf
    public function addbank(){
        //获取用户信息
        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];
        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        $bk_info_name = $_GET["bk_info_name"];  //用户姓名
        $bk_info_idCard = $_GET["bk_info_idCard"];  // 身份证
        $bk_info_bankNum = $_GET["bk_info_bankNum"];  //银行卡号
        $bk_info_bankName = $_GET["bk_info_bankName"]; //银行名字
        $bk_info_phoneNum=$_GET['bk_info_phoneNum'];     //电话号码
        $bk_info_bankinfo=$_GET['bk_info_bankinfo']; //开户支行


                // 查询数据库是否已有这张银行卡
                $tb_bank=M('bank');
                $bank['bank_num']=$bk_info_bankNum;
                $bangklise=$tb_bank->where($bank)->select();
                if ($bangklise){
                    $data['RSPMSG']="已有此银行信息";
                    $this->jsonout('faild','',$data);
                }

                //存入数据库信息
                $bank1['bank_num']=$bk_info_bankNum; //银行卡号
                $bank1['username']=$bk_info_name;//姓名
                $bank1['bank_class']=$bk_info_bankName;//银行名字
                $bank1['bank_info']=$bk_info_bankinfo;//银行开户支行
                $bank1['idCard']=$bk_info_idCard;//身份证号
                $bank1['phonenum']=$bk_info_phoneNum;//电话
                $bank1['user_id']=$user_id;
                $list=$tb_bank->add($bank1);
                if ($list){
                    $this->jsonout('seccess','');
                }else{
                    $data['RSPMSG']="添加失败";
                    $this->jsonout('faild','',$data);
                }

    }
    //todo 删除银行卡 ZF
    public function deletebank(){

        //获取用户信息
        $ID=$_GET['ID'];
        $user_id=$_GET['user_id'];
        $randpwd=$_GET['randpwd'];

        //调用登陆验证
       $this->login_verify($user_id,$randpwd);
        //删除银行卡
        $tb_bank=M('bank');
        $bank['user_id']=$user_id;
        $bank['ID']=$ID;
        $list=$tb_bank->where($bank)->delete();

        if ($list){
            $this->jsonout('success','');
        }else{
            $this->jsonout('faild','');
        }

    }

    //todo 个人设置  ZF
    public function setuserdata(){

        //获取用户修改的资料
//        $userID=$_POST['ID']; // 用户id
//        $headerImg=$_POST['headerImg'];//头像
//        $userName=$_POST['name'];//名称
//        $wxImg=$_POST['wxImg'];//微信二维码
//        $wxNumber=$_POST['wxNumber'];//微信号

        //获取用户信息
        $userID=$_GET['ID'];
        $randpwd=$_GET['randpwd'];

        //$userID=811;
        $headerImg="http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLeKdLZTWmcxJgnrOvtXScI6za5NyCLcFw7OcMRTHnsegt03gCGmmo0RRYsMZRO9GBL9C7ibqP3Oicg/132";
        $userName="张贱贱002";
        $wxImg="www.baidu.com";
        $wxNumber="185037233336";

        //修改用户资料
        $tb_user=M('user');
        $user['nickName']=$userName;
        $user['headerImg']=$headerImg;
        $user['user_wxnum']=$wxNumber;
        $user['wx_erweima']=$wxImg;
        $list=$tb_user->where('ID='.$userID)->save($user);

      //  $sql=" UPDATE tb_user set nickName='$userName',headerImg='$headerImg',user_wxnum='$wxNumber',wx_erweima='$wxImg' where ID=".$userID;
        //返回数据
       if ($list){
           $data['msg']="修改成功!";
           $data['error']=1;
           die(json_encode($data));
       }else{
           $data['msg']="修改失败";
           $data['error']=2;
           die(json_encode($data));
       }


    }

    //todo 密码修改 ZF
    public function setpwd(){

        $userID=$_GET['ID']; //用户ID
        $randpwd=$_GET['randpwd'];

        $formerPwd=$_GET['formerPwd']; //旧密码
        $newmerPwd1=$_GET['newmerPwd1']; //新密码1
        $newmerPwd2=$_GET['newmerPwd2']; //新密码2
        //调用登陆验证
        $this->login_verify($userID,$randpwd);

        if($newmerPwd1 != $newmerPwd2){

            $this->jsonout('4','两次输入密码不同','');
        }
        //查询用户的旧密码
        $tb_user=M("user");
        $user['ID']=$userID;
        $list=$tb_user->where($user)->select();

        //判断用户发过来的旧密码是否与数据库密码一致
        if ($list[0]['user_pwd'] == $formerPwd){
            //密码一致 修改密码
            $userpwd['user_pwd']=$newmerPwd1;
            $list1=$tb_user->where('ID='.$userID)->save($userpwd);
            //返回数据
            if ($list1){
                $this->jsonout('1','修改成功','');
            }else{
                $this->jsonout('3','修改失败','');
            }
        }else{
            $this->jsonout('2','密码错误','');
        }
    }

   //todo 专属推荐人 ZF
    public function referrer(){
        //获取用户id
        $userID=(int)$_GET['ID'];

        $randpwd=$_GET['randpwd'];
        //调用登陆验证
        $this->login_verify($userID,$randpwd);


        //通过id查找自己的上4级
        $sql1="select a.p1id,b.* from tb_user as a join tb_user as b on a.p1id = b.ID where a.ID=".$userID;
        //返回数据
        $p1id=mysql_fetch_array(mysql_query($sql1),MYSQL_ASSOC);

        $sql2="select a.p2id,b.* from tb_user as a join tb_user as b on a.p2id = b.ID where a.ID=".$userID;
        //返回数据
        $p2id=mysql_fetch_array(mysql_query($sql2),MYSQL_ASSOC);

        $sql3="select a.p3id,b.* from tb_user as a join tb_user as b on a.p3id = b.ID where a.ID=".$userID;
        //返回数据
        $p3id=mysql_fetch_array(mysql_query($sql3),MYSQL_ASSOC);

        $sql4="select a.p4id,b.* from tb_user as a join tb_user as b on a.p4id = b.ID where a.ID=".$userID;
        //返回数据
        $p4id=mysql_fetch_array(mysql_query($sql4),MYSQL_ASSOC);


        $data['p1id']=$p1id;
        $data['p2id']=$p2id;
        $data['p3id']=$p3id;
        $data['p4id']=$p4id;

        $this->jsonout('success','',$data);
    }

    //todo 团队管理 ZF
    public function  team(){
        //   通过id查找自己的团队成员

        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];
        //调用登陆验证
       $this->login_verify($user_id,$randpwd);

        $tb_user=M('user');
        $user['p1id']=$user_id;
        $user['vip_rank']=0;
        $list1=$tb_user->where($user)->select();//查询实习会员

        $user1['p1id']=$user_id;
        $user1['vip_rank']=array('GT','0');
        $list2=$tb_user->where($user1)->select();//查询非实习会员

        $data['user']=$tb_user->where('ID='.$user_id)->select();

        //返回数据
        $data['list1']=$list1;
        $data['list2']=$list2;

        $this->jsonout('success','',$data);
    }

    //todo 排行榜 ZF
    public function rankinglist(){

        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];
        //调用登陆验证
       $this->login_verify($user_id,$randpwd);

        // 获取用户总收益排行 前10名
        $tb_user=M("user");
        $data=$tb_user->order('ulimit desc')->limit(10)->select();
        if ($data){
            $ret['data']=$data;
            $this->jsonout('success','',$ret);
        }
    }

    //todo 提现 ZF
    public function withdraw_deposit(){

        //获取用户提现信息
        $user_id=$_GET['user_id'];   //用户id
        $txmoney=$_GET['txmoney'];  //提现金额
        $user_name=$_GET['username'];   //用户姓名
        $bank_class=$_GET['bank_class'];  //银行
        $bank_info=$_GET['bank_info'];//银行详情
        $bank_num=$_GET['bank_num'];//银行卡号
        $time=date("Y-m-d H:i:s");//时间

        $randpwd=$_GET['randpwd'];
        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        //查询总收益和已结算
//        $tb_user=M('user');
//        $tbuser['ID']=$user_id;
//        $user1=$tb_user->where($tbuser)->field('ulimit,yijiesuan')->select();

        //总收益
        $ulmit=$this->ulimit($user_id);
        //已结算
        $yijiesuan=$this->yijiesuan($user_id);

        if ($txmoney > ($ulmit-$yijiesuan)){
            //提现金额大于剩余金额
            $this->jsonout('faild','提现金额大于剩余金额','');
        }
        //  查询是否有审核中的提现记录
        $tb_record=M('record');
        $record['user_ID']=$user_id;
        $record['status']=array('lt',2);

        $tixian=$tb_record->where($record)->select();


        if ($tixian){
            //已有提现款  不能再提现
            $this->jsonout('faild','已有提现款','');
        }else{
//            //提现操作
//            $tb_user=M('user');
//            $user['ID']=$user_id;
//
//            $tb_user->where('ID='.$user_id)->setInc('yijiesuan',$txmoney);   //更新user表已结算
//            $list=$tb_user->where($user)->select();
            // 总收益
            $ulmit1=$this->ulimit($user_id);
            //已结算
            $yijiesuan=$this->yijiesuan($user_id);

            $yue=$ulmit1-($yijiesuan+$txmoney);

            //存入数据库提现记录
            $listbank['user_ID']=$user_id;  //用户id
            $listbank['username']=$user_name; //用户姓名
            $listbank['txmoney']=$txmoney;  //提现金额
            $listbank['bank_class']=$bank_class; // //银行
            $listbank['bank_info']=$bank_info;//银行详情
            $listbank['bank_num']=$bank_num;//银行卡号
            $listbank['status']=0; //状态
            $listbank['date']=$time;   //时间
            $listbank['yue']=$yue; //余额

            $data=$tb_record->data($listbank)->add();

            if ($data){
                //操作成功
                $this->jsonout('success','提现成功!','');
            }else{
                //操作失败
                $this->jsonout('faild','提现失败!','');
            }

        }
    }


    //todo 提现信息
    public function tx(){
        //获取用户信息
        $user_id=$_GET['ID'];
        $randpwd=$_GET['randpwd'];
        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        //查询用户信息
//        $tb_user=M('user');
//        $user['ID']=$user_id;
//        $list=$tb_user->where($user)->select();
//        //可提现金额
//        $money=$list[0]['ulimit']-$list[0]['yijiesuan'];
      $ulimit= $this->ulimit($user_id);
        $yijiesuan=$this->yijiesuan($user_id);
        $money=$ulimit-$yijiesuan;
        $data['money']=$money;
        //查询银行卡
        $tb_bank=M('bank');
        $bank['user_id']=$user_id;
        $banklist=$tb_bank->where($bank)->select();
        $data['bank']=$banklist;
        //返回数据
        $this->jsonout('success','',$data);

    }

    //todo 今日收益
    public function getTime(){

        //用户id
        $user_id=$_GET['user_id'];
        $randpwd=$_GET['randpwd'];


        //调用登陆验证
        $this->login_verify($user_id,$randpwd);

        //当前日期
        $str=date("Y-m-d",time());

        //查询总收益
        $tb_fanyong=M('fanyong');
        $fanyong1['p1id']=$user_id;
        $fanyong1['fyDate']=array('like',"$str%");
        $money1=$tb_fanyong->where($fanyong1)->field('sum(p1fy)+sum(p1gl) as money')->select();

        $fanyong2['p2id']=$user_id;
        $fanyong2['fyDate']=array('like',"$str%");
        $money2=$tb_fanyong->where($fanyong2)->field('sum(p2fy)+sum(p2gl) as money')->select();

        $fanyong3['p3id']=$user_id;
        $fanyong3['fyDate']=array('like',"$str%");
        $money3=$tb_fanyong->where($fanyong3)->field('sum(p3fy)+sum(p3gl) as money')->select();
        //总收益
        $money=$money1[0]['money']+$money2[0]['money']+$money3[0]['money'];

        $data['money']=$money;


        $sql3="select a.*,b.user_lv,b.vip_rank,b.headerImg from tb_fanyong as a JOIN tb_user as b on a.uid=b.ID where fyDate like '$str%' and a.isShow=1 and   (a.p1id=$user_id or a.p2id=$user_id or a.p3id=$user_id) ;";


        $res=mysql_query($sql3);
        while($row=mysql_fetch_row($res,MYSQL_ASSOC))
        {
            $data['list'][] = $row;
        }


        $tb_user=M("user");
        $user['ID']=$user_id;
        $data['user']= $tb_user->where($user)->find();


        $this->jsonout('success','今日收益',$data);

    }

}



