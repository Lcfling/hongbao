<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 15:47
 */

class LoginAction extends CommonAction{

    //todo 用户登录20180709     Lee_zhj
    public function login(){

        $data['input']=file_get_contents("php://input");
        $data['get']=$_GET;
        $userName=$data['get']['user_name'];
        $userPassword=$data['get']['user_pwd'];

        //判断用户名密码
        if($userName!=""&&$userPassword!=""){
            $tb_userlogin = M('user');
            $userloginmsg['user_phone']=$userName;
            $userloginmsg['user_pwd']=$userPassword;
            $sqlnmsg=$tb_userlogin->where($userloginmsg)->Field('user_phone,user_pwd')->select();
            $sqlid=$tb_userlogin->where($userloginmsg)->getField('ID');

            if($sqlnmsg){
                $getrandom['rand_psd']=$this->genRandomString();

                $tb_userlogin->where('ID='.$sqlid)->save($getrandom);
                $par= $tb_userlogin->where('ID='.$sqlid)->getField('rand_psd');
                //var_dump($userrandom);

                $result=array();
                $result['ID']=$sqlid;
                $result['rand_psd']=md5($par);

                $this->jsonout("success",'登录成功',$result);

            }else{

                $this->jsonout('faild','用户名或密码错误');
            }
        }else{
            $this->jsonout('faild','用户名或密码为空');
        }

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