<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 15:47
 */

class LoginAction extends CommonAction{

    //todo lcfling
    public function login(){
        $userName=$_GET['username'];
        $userPassword=$_GET['password'];

        //判断用户名密码
        if($userName!=""&&$userPassword!=""){
            $this->ajaxReturn('','账号密码不能为空！',0);
        }
        $userModel = M('users');
        $map['account']=$userName;
        $res=$userModel->where($map)->Field('user_id,user_phone,user_pwd')->find();
        if($res['password']!=md5($userPassword)){
            $this->ajaxReturn('','账号密码错误！',0);
        }

        $data['token']=md5($this->genRandomString());
        $userModel->where('user_id='.$res['user_id'])->save($data);
        $data['user_id']=$res['user_id'];
        //todo 数据token 存入redis
        $uid=$res['user_id'];
        $this->redis->set('login_'.$uid,$data['token']);


        $this->ajaxReturn($data,'登陆成功！');

    }
    public function mobile(){
        $mobile=(int)$_POST['mobile'];
        $code=(int)$_POST['code'];
        if(!isMobile($mobile)){
            $this->ajaxReturn('','手机号码格式错误！',0);
        }
        $userModel = D('Users');
        $userInfo=$userModel->getUserByMobile($mobile,true);
        //print_r($userInfo);
        $Cachecode=Cac()->get('login_code_'.$mobile);
        if($code!=$Cachecode){
            $this->ajaxReturn('','验证码错误！',0);
        }
        //判断用户是否存在
        $pid=(int)$_GET['pid'];
        if(!empty($userInfo)){
            $userInfo=$userModel->updateLoginCache($userInfo);
        }else{
            //不存在 入库用户信息
            $userInfo=$userModel->insertUserInfo($mobile,$pid);
        }
        $this->ajaxReturn($userInfo,'登录成功！',1);
    }

    public function test(){
        D('Hongbao')->creathongbao(500,1,7,172,2);
    }

    public function sendcode(){
        $mobile=(int)$_POST['mobile'];
        if(!isMobile($mobile)){
            $this->ajaxReturn('','手机号码格式错误！',0);
        }
        $code=rand_string(6,1);
        Cac()->set('login_code_'.$mobile,$code,300);
        //todo 发送短信
        //Sms:LoginCodeSend($mobile,$code);
        $this->ajaxReturn('','短信发送成功！',1);
    }
    public function getcodeview(){
        $mobile=(int)$_GET['mobile'];
        if(!isMobile($mobile)){
            $this->ajaxReturn('','手机号码格式错误！',0);
        }
        $code=Cac()->get('login_code_'.$mobile);

        echo $code;
    }



    //产生一个指定长度的随机字符串,并返回给用户
    private function genRandomString($len = 6) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }


}