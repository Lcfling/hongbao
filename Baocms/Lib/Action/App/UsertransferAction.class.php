<?php
require_once LIB_PATH.'/GatewayClient/Gateway.php';

class UsertransferAction extends CommonAction
{
    //获取通讯录
    public function index(){
        $uid=$this->uid;

        $users=D("Usertransfer");
        $data= $users->addressbook($uid);

        $this->ajaxReturn($data,'好友通讯录');

    }
    // 转账
    public function transfer(){
        //获取用户余额
        $user_id=$this->uid;
        $to_id = (int) $_POST['toid'];
        $money = (int) $_POST['money'];
        $zfb_pwd=(int)$_POST['zfb_pwd'];
        $money=$money*100;
        if ($zfb_pwd == "" || $to_id == "" || $money == ""){
            $this->ajaxReturn(null,"数据异常!请检查！");
        }


        if ($money<5000){
            $this->ajaxReturn(null,"单笔金额不能低于50元！");
        }


        $users=D("Users");
        //判断支付密码是否正确
        $data=$users->getUserByUid($user_id);
        if ($data['zfb_pwd'] != md5($zfb_pwd)){
            $this->ajaxReturn(null,"支付密码错误!");
        }

        $sql_money= $users->getUserMoney($user_id);
        if($sql_money<$money){
            $this->ajaxReturn($user_id,'账号余额不足',0);
        }

        $users=D("Usertransfer");
        $data= $users->transfer($user_id,$to_id,$money);
        $this->ajaxReturn($data,"转账成功!");
    }

    // 搜索用户
    public function search(){
        $to_id = (int) $_POST['toid'];
        if ($to_id == ""){
            $this->ajaxReturn(null,"数据异常!请检查！");
        }
        $users=D("Usertransfer");
        $data= $users->search($to_id);
        if($data){
            $this->ajaxReturn($data,'好友用户信息');

        }else{
            $this->ajaxReturn($to_id,'用户不存在',0);

        }


        public function search(){
            $user_id=$this->uid;

            $users=D("Usertransfer");
            $data= $users->transferinfo($user_id);
            if($data){
                $this->ajaxReturn($data,'转账记录');

            }else{
                $this->ajaxReturn(null,'暂无转账记录',0);

            }

    }
}