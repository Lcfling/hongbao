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

    public function test(){
        D('Hongbao')->creathongbao(500,1,7,172,2);
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