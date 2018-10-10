<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 9:30
 */


class ForgetpwdAction extends CommonAction{

    /**
     * 忘记密码
     * @author Lee_zhj
     */
    public function userforgetpwd(){

        //获取手机号码，发送验证码
        $phonenum=$_GET['user_phone'];
        $yzm=$_GET['yzm'];

        $tb_dkinfo = M('dkinfo');
        $dkinfo['tel'] = $phonenum;
        $dkinfo['yzm'] = $yzm;

        $belongto = $tb_dkinfo->where($dkinfo)->find();
        if (!$belongto){
            $this->jsonout('faild','验证码输入有误！');
        }else{
            $this->jsonout('success','验证码输入正确！');
        }

    }


    /**
     * 重置密码
     */
    public function resetpwd(){


        //根据用户ID更改密码
        $userpwd=(string)$_GET['user_pwd'];
        $phonenum=(int)$_GET['user_phone'];
        


        $tb_user = M('user');
        $user['user_pwd']=$userpwd;

        $info = $tb_user->where("user_phone='".$phonenum."'")->save($user); // 根据条件更改密码



        if ($info){
            $this->jsonout('success','修改密码成功！'.$phonenum);
        }else{
            $this->jsonout('faild','修改密码失败！'.$phonenum);
        }

    }




}