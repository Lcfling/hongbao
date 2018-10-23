<?php



class UcenterAction extends CommonAction {


    //todo 获取用户信息
	public function index(){

	    $user_id=$_POST['user_id'];
	    $user=D('Users');
	    $list=$user->getUserByUid($user_id);
	    if ($list){
            $paid=D('paid');
            $where['user_id']=$user_id;
            $money=$paid->where($where)->field('sum(money) as money')->find();
            $list['money']=$money['money'];
            $this->ajaxReturn($list,"个人信息");
        }else{
            $this->ajaxReturn($list,"个人信息");
        }
    }
    // todo 我的推荐 （自己邀请的人）
    public function pid(){

            $user_id="1675554";
            $tb_users=M("Users");
            $where['pid']=$user_id;
            $list=$tb_users->where($where)->select();
//            if ($list !=""){
//                Cac()->set('userpid_'.$list);
//            }
            $data['x'][]=$list;
            foreach ($list as $k){
                $where['pid']=$list[$k]['ID'];
                $list1=$tb_users->where($where)->field();
                $data['xx'][]=$list1;
//                if ($list1 != ""){
//                    Cac()->set('userpid_'.$list1);
//                }
            }
            $this->ajaxReturn($data,"我的推荐");
    }
    //todo  银行卡
    public function bank(){
        $user_id="1675554";
        $tb_bank=M("Userbank");
        $where['user_id']=$user_id;
        $list=$tb_bank->where($where)->field();
        if ($list){
            $this->ajaxReturn($list,"我的银行卡");
        }else{
            $this->ajaxReturn($list,"没有银行卡",0);
        }
    }

    //todo 提现
    public function tx(){
        $user_id="1675554";
        $money="554";
        $zfb="18503723336";
        $users=D("Users");
       $data= $users->txmoney($user_id,$money,$zfb);
        $this->ajaxReturn($data,$data['msg'],$data['status']);
    }

    //todo 修改个人资料（只允许修改头像 昵称）
    public function set_userinfo(){
        $user_id="1675554";
        $user_name="张三";
        $user_img="./img.jpg";

        $users=D("Users");
        $status=$users->setuserinfo($user_id,$user_name,$user_img);
        if ($status){
            $this->ajaxReturn(null,"修改成功");
        }else{
            $this->ajaxReturn(null,"修改失败");
        }
    }

    //todo 绑定支付宝账号
    public function zhifubao(){

            $user_id="1675554";
            $user_zfb="18503723336";
            $users=D("Users");
           $status=$users->setzyb($user_id,$user_zfb);
        if ($status){
            $this->ajaxReturn($user_zfb,"绑定成功");
        }else{
            $this->ajaxReturn($user_zfb,"绑定失败");
        }

    }
}
